<?php

namespace App\Http\Controllers;

use App\Models\CatalogSettings;
use App\Models\CatalogCustomerGallery;
use Illuminate\Http\Request;
use App\Helpers\ImageUpload;

class CatalogSettingsController extends Controller
{
    public function logo()
    {
        $path = CatalogSettings::first()?->logo_path;
        abort_unless($path, 404);
        $local = str_starts_with($path, 'public/');
        $key = $local ? substr($path, 7) : $path;
        // Serve only the configured image, never a path supplied by the request.
        abort_if(str_contains($key, '..') || str_starts_with($key, '/'), 404);
        $disk = \Illuminate\Support\Facades\Storage::disk($local ? 'public' : 'r2');
        abort_unless($disk->exists($key), 404);
        return $disk->response($key, null, ['Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function edit()
    {
        return view('admin.catalog-settings', [
            'cat_setting' => $this->settings(),
        ]);
    }

    public function update(Request $request)
    {
        $cat_setting = $this->settings();

        $request->validate([
            'site_name'         => 'required|string|max:255',
            'photo_logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'og_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'social_facebook'   => ['nullable', 'url:http,https', $this->allowedHosts(['facebook.com', 'fb.com'])],
            'social_instagram'  => ['nullable', 'url:http,https', $this->allowedHosts(['instagram.com'])],
            'social_tiktok'     => ['nullable', 'url:http,https', $this->allowedHosts(['tiktok.com'])],
            'social_youtube'    => ['nullable', 'url:http,https', $this->allowedHosts(['youtube.com', 'youtu.be'])],
            'contact_phone' => ['nullable', 'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'],
            'about_description' => 'nullable|string|max:2000',
            'service_buy_title' => 'nullable|string|max:100',
            'service_buy_description' => 'nullable|string|max:2000',
            'service_sell_title' => 'nullable|string|max:100',
            'service_sell_description' => 'nullable|string|max:2000',
            'service_repair_title' => 'nullable|string|max:100',
            'service_repair_description' => 'nullable|string|max:2000',
            'hero_eyebrow'      => 'nullable|string|max:100',
            'hero_title'        => 'nullable|string|max:120',
            'hero_description'  => 'nullable|string|max:500',
            'seo_title'         => 'nullable|string|max:70',
            'seo_description'   => 'nullable|string|max:320',
        ]);

        // Prepare uploads before updating text or deleting any existing image.
        $uploads = [];
        foreach (['photo_logo' => 'catalog/logo', 'og_image' => 'catalog/seo', 'hero_image' => 'catalog/hero'] as $field => $folder) {
            if ($request->hasFile($field)) {
                $uploads[$field] = $this->uploadImage($request->file($field), $folder, $field)['path'];
            }
        }
        $cleanup = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $cat_setting, $uploads) {
            $cleanup = [];
            $data = [
                'nama_website' => $request->site_name,
                'nomor_telepon' => $request->contact_phone,
                'facebook_link' => $request->social_facebook,
                'instagram_link' => $request->social_instagram,
                'tiktok_link' => $request->social_tiktok,
                'youtube_link' => $request->social_youtube,
                'about_description' => $request->input('about_description'),
                'service_buy_title' => $request->input('service_buy_title'),
                'service_buy_description' => $request->input('service_buy_description'),
                'service_sell_title' => $request->input('service_sell_title'),
                'service_sell_description' => $request->input('service_sell_description'),
                'service_repair_title' => $request->input('service_repair_title'),
                'service_repair_description' => $request->input('service_repair_description'),
                'hero_eyebrow' => $request->input('hero_eyebrow'),
                'hero_title' => $request->input('hero_title'),
                'hero_description' => $request->input('hero_description'),
                'seo_title' => $request->input('seo_title'),
                'seo_description' => $request->input('seo_description'),
            ];
            foreach (['photo_logo' => 'logo_path', 'og_image' => 'og_image_path', 'hero_image' => 'hero_image_path'] as $field => $column) {
                if (isset($uploads[$field])) {
                    if ($cat_setting->{$column} !== $uploads[$field]) { $cleanup[] = $cat_setting->{$column}; }
                    $data[$column] = $uploads[$field];
                }
            }
            $cat_setting->update($data);
            return $cleanup;
        });
        foreach (array_filter(array_unique($cleanup)) as $path) { $this->deleteImageVariants($path); }


        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

    /**
     * Delete all size variants (thumb/medium/large) of a stored image.
     */
    private function deleteImageVariants(?string $path): void
    {
        if (!$path || CatalogCustomerGallery::where('image_path', $path)->exists()
            || CatalogSettings::where('logo_path', $path)->orWhere('og_image_path', $path)->orWhere('hero_image_path', $path)->exists()) {
            return;
        }
        try {
            \App\Helpers\StoredImage::delete($path);
        } catch (\Throwable $exception) {
            // The content is already saved; cleanup can be retried without losing it.
            report($exception);
        }
    }

    public function destroyGallery($id)
    {
        $gallery = CatalogCustomerGallery::where('catalog_setting_id', $this->settings()->id)->find($id);
        if (!$gallery) {
            return response()->json(['success' => false, 'message' => 'Galeri tidak ditemukan'], 404);
        }

        $path = $gallery->image_path;
        $gallery->delete();
        $this->deleteImageVariants($path);

        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

    private function uploadImage($file, string $folder, string $field): array
    {
        try {
            return ImageUpload::upload($file, $folder);
        } catch (\Throwable $exception) {
            report($exception);
            throw \Illuminate\Validation\ValidationException::withMessages([$field => 'Gambar gagal diunggah. Periksa ukuran gambar dan koneksi penyimpanan, lalu coba lagi.']);
        }
    }

    private function settings(): CatalogSettings
    {
        return CatalogSettings::firstOrCreate(
            ['singleton_key' => true],
            ['nama_website' => 'PusatKamera.id']
        );
    }

    private function allowedHosts(array $allowedHosts): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($allowedHosts): void {
            if (!$value) {
                return;
            }

            $host = strtolower((string) parse_url($value, PHP_URL_HOST));
            $valid = collect($allowedHosts)->contains(
                fn (string $allowed) => $host === $allowed || str_ends_with($host, '.' . $allowed)
            );

            if (!$valid) {
                $fail("Domain pada {$attribute} tidak sesuai dengan platform yang dipilih.");
            }
        };
    }
}
