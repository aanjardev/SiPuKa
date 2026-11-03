<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Services\StorefrontData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class CatalogPaginationTest extends TestCase
{
    public function test_catalog_navigation_stays_small_with_one_hundred_thousand_pages(): void
    {
        $this->mock(StorefrontData::class, function ($mock) {
            $mock->shouldReceive('settings')->andReturn(null);
            $mock->shouldReceive('branch')->andReturn(null);
        });
        $product = new Produk(['nama_produk' => 'Kamera', 'harga_jual' => 1000, 'stok_produk' => 1, 'grade' => 'B']);
        $product->id = 1;
        $product->setRelation('kategori', null);
        $product->setRelation('gambarUtama', null);
        $products = new LengthAwarePaginator([$product], 1200000, 12, 50000, ['path' => '/katalog']);
        $html = (string) $this->view('product', ['products' => $products, 'kategoris' => collect(), 'kategoriFilter' => null, 'budget' => null, 'budgetRanges' => [], 'sort' => 'terbaru', 'sortOptions' => ['terbaru' => 'Terbaru'], 'search' => '', 'errors' => new ViewErrorBag]);
        preg_match('/<nav class="brutal-pagination".*?<\/nav>/s', $html, $match);
        $this->assertNotEmpty($match);
        $this->assertLessThanOrEqual(7, substr_count($match[0], '<a '));
        $this->assertStringContainsString('50000', $match[0]);
        $this->assertStringContainsString('100000', $match[0]);
        $this->assertStringContainsString('aria-current="page"', $match[0]);
    }
}
