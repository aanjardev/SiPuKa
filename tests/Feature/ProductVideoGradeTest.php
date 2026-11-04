<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVideoGradeTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Produk
    {
        return Produk::create(array_merge([
            'id_kategori' => Kategori::firstOrCreate(['nama_kategori' => 'Mirrorless'])->id,
            'kode_sku' => \Illuminate\Support\Str::random(12),
            'nama_produk' => 'Kamera Video Test', 'harga_jual' => 1000000,
            'status' => 'Second', 'grade' => 'A', 'stok_produk' => 1,
            'is_visible' => true, 'is_archived' => false,
        ], $attributes));
    }

    public function test_video_can_be_opened_directly_from_home_catalog_and_gallery(): void
    {
        $video = 'https://www.youtube.com/watch?v=example';
        $product = $this->product(['instagram_link' => $video]);
        foreach (['/', '/katalog'] as $path) {
            $response = $this->get($path)->assertOk()->assertDontSee('A (90-100%)');
            $html = $response->getContent();
            $this->assertSame(1, substr_count($html, 'href="'.$video.'"'));
            $this->assertStringContainsString('class="product-card-video"', $html);
            $this->assertStringContainsString('rel="noopener noreferrer"', $html);
        }
        $html = $this->get('/katalog/'.$product->id)->assertOk()->assertSee('A (90-100%)')->getContent();
        $this->assertSame(1, substr_count($html, 'href="'.$video.'"'));
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new \DOMXPath($document);
        $this->assertSame(1, $xpath->query('//*[@id="galleryPreview"]/a[@class="detail-video-link"]')->length);
    }

    public function test_products_without_video_have_no_video_action(): void
    {
        $product = $this->product(['grade' => 'C']);
        $this->get('/')->assertOk()->assertDontSee('C (<70%)')->assertDontSee('class="product-card-video"', false);
        $this->get('/katalog')->assertOk()->assertDontSee('C (<70%)')->assertDontSee('class="product-card-video"', false);
        $this->get('/katalog/'.$product->id)->assertOk()->assertSee('C (<70%)')->assertDontSee('class="detail-video-link"', false);
    }

    public function test_grade_labels_are_configurable_without_changing_stored_values(): void
    {
        foreach (['A' => 'A (90-100%)', 'B' => 'B (70-90%)', 'C' => 'C (<70%)'] as $grade => $label) {
            $product = new Produk(['grade' => $grade]);
            $this->assertSame($label, $product->grade_label);
            $this->assertSame($grade, $product->grade);
        }
        config(['product_grades.labels.B' => 'B (rentang disetujui)']);
        $this->assertSame('B (rentang disetujui)', (new Produk(['grade' => 'B']))->grade_label);
    }

    public function test_video_links_allow_only_http_and_https(): void
    {
        foreach (['javascript:alert(1)', 'data:text/html,test', 'ftp://example.com/video', null] as $url) {
            $this->assertNull((new Produk(['instagram_link' => $url]))->video_url);
        }
        $this->assertSame('https://youtu.be/example', (new Produk(['instagram_link' => 'https://youtu.be/example']))->video_url);
    }
}
