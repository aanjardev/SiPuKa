<?php

namespace App\Http\Controllers;

use App\Models\CatalogSettings;
use App\Models\CatalogCustomerGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ImageUpload;

class CatalogSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.catalog-settings', [
            'cat_setting' => $this->settings(),
            'cat_gallery' => CatalogCustomerGallery::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $cat_setting = $this->settings();

        $request->validate([
            'site_name'         => 'required|string|max:255',
            'photo_logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'og_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'customer_gallery'  => 'nullable',
            'customer_gallery.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'social_facebook'   => ['nullable', 'url:http,https', $this->allowedHosts(['facebook.com', 'fb.com'])],
            'social_instagram'  => ['nullable', 'url:http,https', $this->allowedHosts(['instagram.com'])],
            'social_tiktok'     => ['nullable', 'url:http,https', $this->allowedHosts(['tiktok.com'])],
            'social_youtube'    => ['nullable', 'url:http,https', $this->allowedHosts(['youtube.com', 'youtu.be'])],
            'contact_phone'     => 'nullable|string|max:20',
            'description_text'  => 'nullable|string',
            'hero_eyebrow'      => 'nullable|string|max:100',
            'hero_title'        => 'nullable|string|max:120',
            'hero_description'  => 'nullable|string|max:500',
            'seo_title'         => 'nullable|string|max:70',
            'seo_description'   => 'nullable|string|max:320',
            'gallery_captions'  => 'nullable|array',
            'gallery_captions.*' => 'nullable|string|max:150',
            'gallery_order'     => 'nullable|array',
            'gallery_order.*'   => 'nullable|integer|min:0|max:9999',
        ]);

        $cat_setting->update([
            'nama_website'   => $request->site_name,
            'nomor_telepon'  => $request->contact_phone,
            'description'     => $request->description_text,
            'hero_eyebrow'    => $request->hero_eyebrow,
            'hero_title'      => $request->hero_title,
            'hero_description' => $request->hero_description,
            'seo_title'       => $request->seo_title,
            'seo_description' => $request->seo_description,
            'facebook_link'   => $request->social_facebook,
            'instagram_link'  => $request->social_instagram,
            'tiktok_link'     => $request->social_tiktok,
            'youtube_link'    => $request->social_youtube,
        ]);

        foreach ($request->input('gallery_captions', []) as $id => $caption) {
            CatalogCustomerGallery::where('catalog_setting_id', $cat_setting->id)
                ->whereKey($id)
                ->update([
                    'caption' => $caption,
                    'sort_order' => (int) data_get($request->input('gallery_order', []), $id, 0),
                ]);
        }

        if ($request->filled('deleted_galleries')) {
            $ids = json_decode($request->deleted_galleries, true);

            $galleries = CatalogCustomerGallery::whereIn('id', $ids)->get();

            foreach ($galleries as $gallery) {
                $this->deleteImageVariants($gallery->image_path);
            }

            CatalogCustomerGallery::whereIn('id', $ids)->delete();
        }

        if ($request->hasFile('customer_gallery')) {
            foreach ($request->file('customer_gallery') as $file) {
                $paths = ImageUpload::upload($file, 'catalog/gallery');
                CatalogCustomerGallery::create([
                    'image_path' => $paths['path'],
                    'catalog_setting_id' => $cat_setting->id,
                ]);
            }
        }

        if ($request->hasFile('photo_logo')) {

            $this->deleteImageVariants($cat_setting->logo_path);

            $paths = ImageUpload::upload($request->file('photo_logo'), 'catalog/logo');

            $cat_setting->update(['logo_path' => $paths['path']]);
        }

        if ($request->hasFile('og_image')) {
            $this->deleteImageVariants($cat_setting->og_image_path);
            $paths = ImageUpload::upload($request->file('og_image'), 'catalog/seo');
            $cat_setting->update(['og_image_path' => $paths['path']]);
        }


        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

    /**
     * Delete all size variants (thumb/medium/large) of a stored image.
     */
    private function deleteImageVariants(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = Storage::disk('r2');

        if (preg_match('#^(.*)/(thumb|medium|large)/([^/]+)$#', $path, $m)) {
            $base = $m[1];
            $file = $m[3];
            foreach (['thumb', 'medium', 'large'] as $size) {
                $candidate = "{$base}/{$size}/{$file}";
                if ($disk->exists($candidate)) {
                    $disk->delete($candidate);
                }
            }
            return;
        }

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    public function destroyGallery($id)
    {
        $gallery = CatalogCustomerGallery::find($id);
        if (!$gallery) {
            return response()->json(['success' => false, 'message' => 'Galeri tidak ditemukan'], 404);
        }

        $this->deleteImageVariants($gallery->image_path);
        $gallery->delete();

        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

    private function settings(): CatalogSettings
    {
        return CatalogSettings::firstOrCreate(
            ['singleton_key' => true],
            ['nama_website' => config('app.name', 'PusatKamera.id')]
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
