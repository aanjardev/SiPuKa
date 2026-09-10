<?php

namespace App\Services;

use App\Jobs\CleanupProductAssets;
use App\Jobs\ProcessProductImage;
use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function createProduct(array $data, array $images, ?string $mainImageInput, ?int $userId = null): Produk
    {
        $tempPaths = $this->storeTemporaryImages($images, 'temp/product-creates');
        $selectedMainIndex = $this->extractNewMainIndex($mainImageInput);

        DB::beginTransaction();
        try {
            // PERBAIKAN: Auto-set visibility based on whether images will be uploaded
            // Jika ada gambar akan di-upload → visibility bisa ON (user choice)
            // Jika tidak ada gambar → visibility HARUS OFF (auto)
            $visibilityValue = !empty($tempPaths) ? ($data['is_visible'] ?? true) : false;

            $product = Produk::create([
                'nama_produk'      => $data['nama_produk'],
                'kode_sku'         => $data['kode_sku'],
                'id_kategori'      => $data['id_kategori'],
                'harga_jual'       => $data['harga_jual'] ?? null,
                'harga_beli'       => $data['harga_beli'] ?? null,
                'harga_servis'     => $data['harga_servis'] ?? null,
                'stok_produk'      => $data['stok_produk'] ?? null,
                'deskripsi_produk' => $data['deskripsi_produk'] ?? null,
                'instagram_link'   => $data['instagram_link'] ?? null,
                'status'           => $data['status'],
                'grade'            => $data['grade'],
                'is_visible'       => $visibilityValue,
                'is_archived'      => false,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->cleanupTemporaryUploads($tempPaths);
            throw $th;
        }

        if (!empty($tempPaths)) {
            // PERBAIKAN: Process langsung untuk development, queue untuk production
            if (config('app.env') === 'production' && config('queue.default') !== 'sync') {
                ProcessProductImage::dispatch(
                    $product->id,
                    $tempPaths,
                    $selectedMainIndex ?? 0,
                    $userId
                );
            } else {
                $processor = new ProcessProductImage(
                    $product->id,
                    $tempPaths,
                    $selectedMainIndex ?? 0,
                    $userId
                );
                $processor->handle();
            }
        }

        return $product;
    }

    public function updateProduct(Produk $product, array $data, array $newImages, ?string $mainImageInput, array $removeImages = [], ?int $userId = null): void
    {
        $tempPaths = $this->storeTemporaryImages($newImages, 'temp/product-updates');
        $selectedMainNewIndex = $this->extractNewMainIndex($mainImageInput);
        $selectedMainExistingId = $this->extractExistingMainId($mainImageInput);
        $pathsForCleanup = [];

        DB::beginTransaction();
        try {
            // PERBAIKAN: Handle image removal and auto-visibility
            $removeIds = array_filter($removeImages, fn ($v) => !empty($v));
            if (!empty($removeIds)) {
                $imagesToDelete = GambarProduk::whereIn('id', $removeIds)
                    ->where('id_produk', $product->id)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    $pathsForCleanup[] = $img->path_gambar;
                    $img->delete();
                }
            }

            // Calculate remaining images after deletion
            $remainingImagesCount = $product->gambar()->count() - count($removeIds);

            // Calculate visibility:
            // - Jika user upload gambar baru → tetap pakai nilai dari input (is_visible dari form)
            // - Jika user hanya remove (tidak upload baru) → check remaining images
            //   - Jika tidak ada gambar tersisa → force is_visible = false
            //   - Jika ada gambar tersisa → tetap pakai nilai dari input
            $visibilityValue = $data['is_visible'] ?? true;
            if (empty($tempPaths) && $remainingImagesCount <= 0) {
                // Tidak ada gambar baru dan tidak ada gambar tersisa → force OFF
                $visibilityValue = false;
            }

            $product->update([
                'nama_produk'      => $data['nama_produk'],
                'kode_sku'         => $data['kode_sku'],
                'id_kategori'      => $data['id_kategori'],
                'harga_jual'       => $data['harga_jual'] ?? null,
                'harga_beli'       => $data['harga_beli'] ?? null,
                'harga_servis'     => $data['harga_servis'] ?? null,
                'stok_produk'      => $data['stok_produk'] ?? null,
                'deskripsi_produk' => $data['deskripsi_produk'] ?? null,
                'instagram_link'   => $data['instagram_link'] ?? null,
                'status'           => $data['status'],
                'grade'            => $data['grade'],
                'is_visible'       => $visibilityValue,
            ]);

            if ($selectedMainExistingId !== null) {
                $targetImage = $product->gambar()->where('id', $selectedMainExistingId)->first();
                if ($targetImage) {
                    GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
                    $targetImage->is_main = true;
                    $targetImage->save();
                }
            }

            if (!$product->gambarUtama()->exists()) {
                $first = $product->gambar()->first();
                if ($first) {
                    $first->update(['is_main' => true]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->cleanupTemporaryUploads($tempPaths);
            throw $th;
        }

        if (!empty($pathsForCleanup)) {
            CleanupProductAssets::dispatch($pathsForCleanup);
        }

        if (!empty($tempPaths)) {
            $product->refresh();
            $productHasMain = $product->gambarUtama()->exists();
            // Determine if we should auto-enable visibility (product had no images before)
            $shouldAutoEnableVisibility = !$product->gambar()->exists();

            // PERBAIKAN: Process langsung untuk development, queue untuk production
            if (config('app.env') === 'production' && config('queue.default') !== 'sync') {
                ProcessProductImage::dispatch(
                    $product->id,
                    $tempPaths,
                    $selectedMainNewIndex !== null ? $selectedMainNewIndex : ($productHasMain ? null : 0),
                    $userId,
                    $shouldAutoEnableVisibility
                );
            } else {
                $processor = new ProcessProductImage(
                    $product->id,
                    $tempPaths,
                    $selectedMainNewIndex !== null ? $selectedMainNewIndex : ($productHasMain ? null : 0),
                    $userId,
                    $shouldAutoEnableVisibility
                );
                $processor->handle();
            }
        }
    }

    public function uploadAdditionalPhotos(Produk $product, array $images, ?string $mainImageInput, ?int $userId = null): void
    {
        if (empty($images)) {
            throw new \InvalidArgumentException('Tidak ada gambar yang diunggah.');
        }

        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'existing_')) {
            $idToSet = intval(substr($mainImageInput, strlen('existing_')));
            GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
            GambarProduk::where('id_produk', $product->id)
                ->where('id', $idToSet)
                ->update(['is_main' => true]);
        }

        $tempPaths = $this->storeTemporaryImages($images, 'temp/product-uploads');
        $mainImageIndex = $this->extractNewMainIndex($mainImageInput);

        // PERBAIKAN: Check jika produk awal tidak punya gambar - flag untuk auto-enable visibility nanti
        $shouldAutoEnableVisibility = !$product->gambar()->exists();

        // PERBAIKAN: Check apakah env production atau development
        // Development: proses langsung (synchronous) - PALING RELIABLE
        // Production: bisa pakai queue tapi dengan fallback
        \Log::info('uploadAdditionalPhotos - shouldAutoEnableVisibility', [
            'product_id' => $product->id,
            'has_images_before' => !$shouldAutoEnableVisibility ? true : false,
            'should_auto_enable' => $shouldAutoEnableVisibility,
            'temp_paths_count' => count($tempPaths),
        ]);
        if (config('app.env') === 'production' && config('queue.default') !== 'sync') {
            // Queue untuk production (opsional)
            ProcessProductImage::dispatch(
                $product->id,
                $tempPaths,
                $mainImageIndex,
                $userId,
                $shouldAutoEnableVisibility  // Pass flag ke job
            );
        } else {
            // Proses langsung untuk development & testing
            // Ini memastikan gambar tersimpan langsung sebelum redirect
            $processor = new ProcessProductImage(
                $product->id,
                $tempPaths,
                $mainImageIndex,
                $userId,
                $shouldAutoEnableVisibility  // Pass flag ke processor
            );
            $processor->handle();

            // SETELAH berhasil diproses - auto-enable visibility
            if ($shouldAutoEnableVisibility) {
                \Log::info('uploadAdditionalPhotos - enabling visibility (sync path)', ['product_id' => $product->id]);
                $product->update(['is_visible' => true]);
                \Log::info('uploadAdditionalPhotos - visibility updated', ['product_id' => $product->id, 'is_visible' => $product->is_visible]);
            }
        }
    }

    private function storeTemporaryImages(array $images, string $folder): array
    {
        $temporaryPaths = [];
        foreach ($images as $image) {
            $temporaryPaths[] = $image->store($folder, 'local');
        }
        return $temporaryPaths;
    }

    private function cleanupTemporaryUploads(array $paths): void
    {
        foreach ($paths as $tempPath) {
            try {
                if (Storage::disk('local')->exists($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                }
            } catch (\Throwable $th) {
                \Log::warning('Failed deleting temporary upload', [
                    'path' => $tempPath,
                    'error' => $th->getMessage(),
                ]);
            }
        }
    }

    private function extractNewMainIndex(?string $mainImageInput): ?int
    {
        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'new_')) {
            return intval(substr($mainImageInput, strlen('new_')));
        }
        return null;
    }

    private function extractExistingMainId(?string $mainImageInput): ?int
    {
        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'existing_')) {
            return intval(substr($mainImageInput, strlen('existing_')));
        }
        return null;
    }
}
