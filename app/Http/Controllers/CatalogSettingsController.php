<?php

namespace App\Http\Controllers;

use App\Models\CatalogSettings;
use App\Models\CatalogBanners;
use App\Models\CatalogPartnerLogo;
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
            'cat_setting' => CatalogSettings::first(),
            'cat_banners' => CatalogBanners::all(),
            'cat_partner' => CatalogPartnerLogo::all(),
            'cat_gallery' => CatalogCustomerGallery::all(),
        ]);
    }

    public function update(Request $request)
    {
        $cat_setting = CatalogSettings::first();

        $request->validate([
            'site_name'         => 'required|string|max:255',
            'photo_logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'brand_logos'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'customer_gallery'  => 'nullable',
            'customer_gallery.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'social_facebook'   => 'nullable|url',
            'social_instagram'  => 'nullable|url',
            'social_tiktok'     => 'nullable|url',
            'social_youtube'    => 'nullable|url',
            'social_tokopedia'  => 'nullable|url',
            'social_shopee'     => 'nullable|url',
            'contact_phone'     => 'nullable|string|max:20',
            'description_text'  => 'nullable|string',
        ]);

        $cat_setting->update([
            'nama_website'   => $request->site_name,
            'nomor_telfon'   => $request->contact_phone,
            'description'     => $request->description_text,
            'facebook_link'   => $request->social_facebook,
            'instagram_link'  => $request->social_instagram,
            'tiktok_link'     => $request->social_tiktok,
            'youtube_link'    => $request->social_youtube,
            'tokopedia_link'  => $request->social_tokopedia,
            'shopee_link'     => $request->social_shopee,
        ]);

        if ($request->filled('deleted_partners')) {
            $ids = json_decode($request->deleted_partners, true);

            $partners = CatalogPartnerLogo::whereIn('id', $ids)->get();

            foreach ($partners as $partner) {

                $this->deleteImageVariants($partner->logo_path);
            }

            CatalogPartnerLogo::whereIn('id', $ids)->delete();
        }

        if ($request->filled('deleted_galleries')) {
            $ids = json_decode($request->deleted_galleries, true);

            $galleries = CatalogCustomerGallery::whereIn('id', $ids)->get();

            foreach ($galleries as $gallery) {
                $this->deleteImageVariants($gallery->image_path);
            }

            CatalogCustomerGallery::whereIn('id', $ids)->delete();
        }

        if ($request->filled('deleted_banners')) {
            $ids = json_decode($request->deleted_banners, true);

            $banners = CatalogBanners::whereIn('id', $ids)->get();

            foreach ($banners as $banner) {

                $this->deleteImageVariants($banner->banner_path);
            }

            CatalogBanners::whereIn('id', $ids)->delete();
        }

        if ($request->hasFile('banner')) {

            $paths = ImageUpload::upload($request->file('banner'), 'catalog/banners');

            CatalogBanners::create([
                'banner_path' => $paths['path'],
                'catalog_setting_id' => 1,
            ]);
        }

        if ($request->hasFile('brand_logos')) {

            $paths = ImageUpload::upload($request->file('brand_logos'), 'catalog/partners');

            CatalogPartnerLogo::create([
                'logo_path' => $paths['path'],
                'catalog_setting_id' => 1,
            ]);
        }

        if ($request->hasFile('customer_gallery')) {
            foreach ($request->file('customer_gallery') as $file) {
                $paths = ImageUpload::upload($file, 'catalog/gallery');
                CatalogCustomerGallery::create([
                    'image_path' => $paths['path'],
                    'catalog_setting_id' => 1,
                ]);
            }
        }

        if ($request->hasFile('photo_logo')) {

            $this->deleteImageVariants($cat_setting->logo_path);

            $paths = ImageUpload::upload($request->file('photo_logo'), 'catalog/logo');

            $cat_setting->update(['logo_path' => $paths['path']]);
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

    public function destroyBanner($id)
    {
        $banner = CatalogBanners::find($id);
        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner tidak ditemukan'], 404);
        }

        $this->deleteImageVariants($banner->banner_path);
        $banner->delete();

        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

    public function destroyPartner($id)
    {
        $partner = CatalogPartnerLogo::find($id);
        if (!$partner) {
            return response()->json(['success' => false, 'message' => 'Partner tidak ditemukan'], 404);
        }

        $this->deleteImageVariants($partner->logo_path);
        $partner->delete();

        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
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
}
