<?php

namespace Tests\Feature;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AdminPresentationTest extends TestCase
{
    public function test_product_requests_accept_only_abc_grades(): void
    {
        foreach ([new ProductStoreRequest, new ProductUpdateRequest] as $request) {
            $rules = ['grade' => $request->rules()['grade']];
            foreach (['A', 'B', 'C'] as $grade) {
                $this->assertTrue(Validator::make(['grade' => $grade], $rules)->passes());
            }
            foreach (['Unggulan', 'Standar', 'Minus', 'D', ''] as $grade) {
                $this->assertFalse(Validator::make(['grade' => $grade], $rules)->passes());
            }
        }
    }

    public function test_pagination_shows_middle_page_and_preserves_filters(): void
    {
        $paginator = new LengthAwarePaginator(range(1, 10), 200, 10, 10, ['path' => '/admin/produk']);
        $paginator->appends(['search' => 'Canon']);
        $html = $paginator->links('pagination::bootstrap-5')->render();

        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('<span class="page-link">10</span>', $html);
        $this->assertStringContainsString('search=Canon', $html);
        $this->assertStringContainsString('page=9', $html);
        $this->assertStringContainsString('page=11', $html);
        $this->assertStringContainsString('Menampilkan', $html);
        $this->assertStringNotContainsString('pagination.previous', $html);
    }
}
