<?php

namespace Tests\Feature;

use App\Models\{Branch, CatalogSettings, ItemPembelian, Kategori, KategoriHarga, Pembelian, Penjualan, Produk, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Hash, Storage};
use Illuminate\Support\Str;
use Tests\TestCase;

class PusatKameraAdminTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $id = DB::table('karyawan')->insertGetId([
            'nama_lengkap'=>'Pemilik Test', 'nik'=>'OWNER-TEST', 'jabatan'=>'Manager',
            'tanggal_masuk'=>'2026-01-01', 'status'=>'aktif', 'nomor_telepon'=>'081234567890',
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return User::create(['id'=>$id, 'name'=>'Pemilik Test', 'email'=>'owner@example.test',
            'password'=>Hash::make('password'), 'role'=>'manager', 'status'=>'active', 'email_verified_at'=>now()]);
    }

    private function storeRecord(): Branch
    {
        return Branch::create(['nama'=>'Pusat Kamera Malang', 'alamat'=>'Jl. Mertojoyo E1a',
            'nomor_telepon'=>'082345672034', 'is_active'=>true, 'singleton_key'=>true]);
    }

    private function customer(): int
    {
        return DB::table('customer')->insertGetId(['kode_customer'=>'CUST-TEST', 'nama'=>'Pelanggan Test', 'no_telp'=>'081234567890',
            'jenis_kelamin'=>'L', 'created_at'=>now(), 'updated_at'=>now()]);
    }

    private function product(array $attributes = []): Produk
    {
        return Produk::create(array_merge([
            'nama_produk'=>'Kamera '.Str::random(6), 'kode_sku'=>Str::random(12),
            'id_kategori'=>Kategori::firstOrCreate(['nama_kategori'=>'Mirrorless'])->id,
            'harga_jual'=>9000000, 'harga_beli'=>5000000, 'stok_produk'=>3,
            'status'=>'Second', 'grade'=>'Standar', 'is_visible'=>true, 'is_archived'=>false,
        ], $attributes));
    }

    private function qcPayload(Produk $product): array
    {
        return ['nama_item'=>$product->nama_produk, 'kategori_id'=>$product->id_kategori,
            'kode_sku'=>$product->kode_sku, 'qty'=>2, 'harga_jual'=>9000000,
            'deskripsi_produk'=>'Kamera sudah diperiksa', 'status'=>'Second', 'grade'=>'Standar',
            'status_qc'=>'lolos_qc', 'action'=>'save'];
    }

    public function test_price_filter_uses_manual_choice_not_price(): void
    {
        $category = KategoriHarga::active()->firstOrFail();
        $assigned = $this->product(['nama_produk'=>'MANUAL-ASSIGNED', 'id_kategori_harga'=>$category->id]);
        $this->product(['nama_produk'=>'PRICE-ONLY', 'harga_jual'=>$category->harga_minimum]);
        $this->get('/katalog?budget='.$category->slug)->assertOk()->assertSee($assigned->nama_produk)->assertDontSee('PRICE-ONLY');
        $this->get('/katalog')->assertSee('PRICE-ONLY');
    }

    public function test_inactive_price_category_is_not_a_public_filter(): void
    {
        $category = KategoriHarga::active()->firstOrFail();
        $category->update(['is_active'=>false, 'nama'=>'INACTIVE-CATEGORY']);
        $this->product(['nama_produk'=>'GENERAL-PRODUCT', 'id_kategori_harga'=>$category->id]);
        $this->get('/katalog?budget='.$category->slug)->assertOk()->assertSee('GENERAL-PRODUCT')->assertDontSee('INACTIVE-CATEGORY');
        $this->get('/')->assertDontSee('INACTIVE-CATEGORY');
    }

    public function test_out_of_stock_is_public_but_hidden_and_archived_products_are_not(): void
    {
        $empty = $this->product(['nama_produk'=>'EMPTY-STOCK', 'stok_produk'=>0]);
        $hidden = $this->product(['nama_produk'=>'HIDDEN-CAMERA', 'is_visible'=>false]);
        $archived = $this->product(['nama_produk'=>'ARCHIVED-CAMERA', 'is_archived'=>true]);
        foreach (['/', '/katalog'] as $url) {
            $this->get($url)->assertOk()->assertSee('EMPTY-STOCK')->assertSee('STOK HABIS')
                ->assertDontSee('HIDDEN-CAMERA')->assertDontSee('ARCHIVED-CAMERA');
        }
        $this->get('/katalog/'.$empty->id)->assertOk()->assertSee('STOK HABIS');
        foreach ([$hidden, $archived] as $product) {
            $this->get('/katalog/'.$product->id)->assertNotFound();
            $this->get('/katalog/suggest?q='.$product->nama_produk)->assertExactJson([]);
            $this->get('/sitemap.xml')->assertDontSee(url('/katalog/'.$product->id));
        }
    }

    public function test_product_camera_category_is_required_and_price_category_is_optional(): void
    {
        $this->actingAs($this->owner());
        $payload = ['nama_produk'=>'Produk Baru', 'kode_sku'=>'NEW-TEST', 'status'=>'Second', 'grade'=>'Standar', 'is_visible'=>0];
        $this->post('/admin/products', $payload)->assertSessionHasErrors('id_kategori');
        $payload['id_kategori'] = Kategori::firstOrCreate(['nama_kategori'=>'DSLR'])->id;
        $this->post('/admin/products', $payload)->assertSessionHasNoErrors()->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('produk', ['kode_sku'=>'NEW-TEST', 'id_kategori_harga'=>null]);
        $category = KategoriHarga::active()->firstOrFail();
        $category->update(['is_active'=>false]);
        $payload['kode_sku']='INACTIVE-NEW'; $payload['id_kategori_harga']=$category->id;
        $this->post('/admin/products', $payload)->assertSessionHasErrors('id_kategori_harga');
    }

    public function test_inactive_assignment_can_be_retained_when_editing_product(): void
    {
        $this->actingAs($this->owner());
        $category = KategoriHarga::active()->firstOrFail();
        $product = $this->product(['id_kategori_harga'=>$category->id]);
        $category->update(['is_active'=>false]);
        $this->get('/admin/products/'.$product->id.'/edit')->assertOk()->assertSee('(nonaktif)');
        $this->put('/admin/products/'.$product->id, $product->only([
            'nama_produk','kode_sku','id_kategori','id_kategori_harga','status','grade'
        ]) + ['is_visible'=>0])->assertSessionHasNoErrors();
        $this->assertEquals($category->id, $product->fresh()->id_kategori_harga);
    }

    public function test_used_price_category_cannot_be_deleted(): void
    {
        $this->actingAs($this->owner());
        $category = KategoriHarga::active()->firstOrFail();
        $this->product(['id_kategori_harga'=>$category->id]);
        $this->delete('/admin/price-categories/'.$category->id)->assertSessionHas('error');
        $this->assertDatabaseHas('kategori_harga', ['id'=>$category->id]);
    }

    public function test_catalog_settings_control_public_content_and_brand(): void
    {
        $this->actingAs($this->owner());
        $this->post('/admin/catalog-settings', [
            'site_name'=>'BRAND-TEST', 'hero_eyebrow'=>'HERO-LABEL', 'hero_title'=>'HERO-TITLE',
            'hero_description'=>'HERO-DESCRIPTION', 'about_description'=>'ABOUT-CONTENT',
            'service_buy_title'=>'BUY-TITLE', 'service_buy_description'=>'BUY-CONTENT',
            'service_sell_title'=>'SELL-TITLE', 'service_sell_description'=>'SELL-CONTENT',
            'service_repair_title'=>'REPAIR-TITLE', 'service_repair_description'=>'REPAIR-CONTENT',
            'seo_title'=>'SEO-TITLE', 'seo_description'=>'SEO-DESCRIPTION', 'contact_phone'=>'081234567899',
        ])->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('HERO-LABEL')->assertSee('HERO-TITLE')->assertSee('SEO-DESCRIPTION');
        $this->get('/about')->assertOk()->assertSee('ABOUT-CONTENT')->assertSee('BUY-CONTENT')->assertSee('SELL-TITLE')->assertSee('REPAIR-CONTENT');
        $this->get('/contact')->assertOk()->assertSee('6281234567899');
        $this->get('/admin/dashboard')->assertOk()->assertSee('BRAND-TEST')->assertDontSee('SiDiKa')->assertDontSee('Dinoyo');
        $this->assertStringContainsString('BRAND-TEST', (new \App\Mail\PasswordResetCodeMail(User::first(), '123456'))->envelope()->subject);
    }

    public function test_all_main_admin_pages_render_without_branch_selection_or_smart_stock(): void
    {
        $this->withoutExceptionHandling();
        if (getenv('SIPK_PREVIEW')) { app(\Illuminate\Foundation\Vite::class)->useHotFile(storage_path('app/preview-vite-hot')); }
        $this->actingAs($this->owner()); $store=$this->storeRecord();
        foreach (['dashboard','products','products/create','products/photos','categories','price-categories','price-categories/create',
            'catalog-settings','quality-control','quality-control/create','customers','employees','permissions','purchases','purchases/create','sales'] as $path) {
            $response = $this->get('/admin/'.$path);
            if (getenv('SIPK_PREVIEW') && in_array($path, ['dashboard', 'products/create', 'catalog-settings', 'quality-control/create'])) {
                $directory = storage_path('app/admin-preview');
                if (!is_dir($directory)) { mkdir($directory, 0777, true); }
                $html = preg_replace('#(href|src)="(?:https?://[^/]+)?/(build|css|js|mainIMG)/#', '$1="/public/$2/', $response->getContent());
                file_put_contents($directory.'/'.str_replace('/', '-', $path).'.html', $html);
            }
            $response->assertOk()->assertDontSee('name="branch_id"', false)->assertDontSee('name="cabang"', false)->assertDontSee('smart-stock');
        }
        $this->get('/admin/branches')->assertRedirect(route('admin.branches.edit', $store));
        $this->get('/admin/branches/'.$store->id.'/edit')->assertOk();
        $this->withExceptionHandling()->get('/admin/smart-stock')->assertNotFound();
    }

    public function test_qc_posts_stock_once_and_carries_manual_category(): void
    {
        $this->actingAs($owner=$this->owner()); $store=$this->storeRecord();
        $product=$this->product();
        $purchase=Pembelian::create(['customer_id'=>$this->customer(),'perusahaan_cabang_id'=>$store->id,
            'user_id'=>$owner->id,'status_pembelian'=>'deal','harga_deal'=>1000000]);
        $item=ItemPembelian::create(['pembelian_id'=>$purchase->id,'nama_item'=>$product->nama_produk,
            'kategori_id'=>$product->id_kategori,'qty'=>2,'status'=>'Second','status_qc'=>'menunggu_qc']);
        $category=KategoriHarga::active()->firstOrFail();
        $payload=$this->qcPayload($product)+['id_kategori_harga'=>$category->id];
        $this->put('/admin/quality-control/'.$item->id,$payload)->assertSessionHasNoErrors();
        $this->assertEquals(5,$product->fresh()->stok_produk);
        $this->assertEquals($category->id,$product->fresh()->id_kategori_harga);
        $this->put('/admin/quality-control/'.$item->id,$payload)->assertSessionHasNoErrors();
        $this->assertEquals(5,$product->fresh()->stok_produk);
        $this->assertNotNull($item->fresh()->stock_posted_at);
    }

    public function test_qc_cannot_publish_a_draft_purchase(): void
    {
        $this->actingAs($owner=$this->owner()); $store=$this->storeRecord(); $product=$this->product();
        $purchase=Pembelian::create(['customer_id'=>$this->customer(),'perusahaan_cabang_id'=>$store->id,'user_id'=>$owner->id,'status_pembelian'=>'draft']);
        $item=ItemPembelian::create(['pembelian_id'=>$purchase->id,'nama_item'=>$product->nama_produk,'kategori_id'=>$product->id_kategori,'qty'=>2,'status'=>'Second','status_qc'=>'menunggu_qc']);
        $this->put('/admin/quality-control/'.$item->id,$this->qcPayload($product))->assertSessionHasErrors('error');
        $this->assertEquals(3,$product->fresh()->stok_produk);
    }

    public function test_manual_qc_resubmission_does_not_duplicate_stock(): void
    {
        $this->actingAs($this->owner()); $product=$this->product();
        $payload=$this->qcPayload($product)+['submission_token'=>(string)Str::uuid()];
        $this->post('/admin/quality-control',$payload)->assertSessionHasNoErrors();
        $this->post('/admin/quality-control',$payload)->assertSessionHasNoErrors();
        $this->assertEquals(5,$product->fresh()->stok_produk);
        $this->assertEquals(1,ItemPembelian::count());
    }

    public function test_sale_resubmission_and_updates_preserve_stock_and_single_store(): void
    {
        $this->actingAs($this->owner()); $store=$this->storeRecord(); $product=$this->product();
        $payload=['customer_id'=>$this->customer(),'perusahaan_cabang_id'=>999999,'kas'=>'cash',
            'items'=>json_encode([['id'=>$product->id,'qty'=>2]]),'submission_token'=>(string)Str::uuid()];
        $this->post('/admin/sales',$payload)->assertSessionHasNoErrors()->assertSessionMissing('error');
        $this->post('/admin/sales',$payload)->assertSessionHasNoErrors()->assertSessionMissing('error');
        $this->assertEquals(1,Penjualan::count()); $this->assertEquals(1,$product->fresh()->stok_produk);
        $sale=Penjualan::firstOrFail(); $this->assertEquals($store->id,$sale->perusahaan_cabang_id);
        $this->assertEquals(18000000,$sale->harga_total);
        $this->put('/admin/sales/'.$sale->id,$payload)->assertSessionHasNoErrors()->assertSessionMissing('error');
        $this->put('/admin/sales/'.$sale->id,$payload)->assertSessionHasNoErrors()->assertSessionMissing('error');
        $this->assertEquals(1,$product->fresh()->stok_produk);
        $this->delete('/admin/sales/'.$sale->id)->assertSessionHasNoErrors();
        $this->assertEquals(3,$product->fresh()->stok_produk);
    }

    public function test_sale_rejects_overstock_even_with_duplicate_product_rows(): void
    {
        $this->actingAs($this->owner()); $this->storeRecord(); $product=$this->product();
        $payload=['customer_id'=>$this->customer(),'kas'=>'cash','submission_token'=>(string)Str::uuid(),
            'items'=>json_encode([['id'=>$product->id,'qty'=>2],['id'=>$product->id,'qty'=>2]])];
        $this->post('/admin/sales',$payload)->assertSessionHas('error');
        $this->assertEquals(3,$product->fresh()->stok_produk); $this->assertEquals(0,Penjualan::count());
    }
    public function test_purchase_qc_sale_flow_and_receipts_work_for_one_store(): void
    {
        $this->actingAs($owner=$this->owner()); $store=$this->storeRecord(); $reference=$this->product();
        $response=$this->postJson('/admin/purchases/store-item-draft', [
            'nama_item'=>'FLOW-CAMERA', 'kategori_id'=>$reference->id_kategori,
            'customer_id'=>$customer=$this->customer(), 'user_id'=>$owner->id, 'perusahaan_cabang_id'=>999999,
        ])->assertOk()->assertJsonPath('success',true);
        $purchaseId=$response->json('pembelian_id'); $itemId=$response->json('item.id');
        $deal=['pembelian_id'=>$purchaseId,'customer_id'=>$customer,'user_id'=>$owner->id,
            'status_pembelian'=>'deal','harga_deal'=>1000000];
        $this->post('/admin/purchases',$deal)->assertSessionHasNoErrors();
        $this->post('/admin/purchases',$deal)->assertSessionHasNoErrors();
        $this->assertEquals(1,Pembelian::count());
        $this->assertDatabaseHas('pembelian',['id'=>$purchaseId,'perusahaan_cabang_id'=>$store->id,'status_pembelian'=>'deal']);
        $payload=$this->qcPayload($reference); $payload['kode_sku']='FLOW-CAMERA'; $payload['nama_item']='FLOW-CAMERA';
        $payload['id_kategori_harga']=KategoriHarga::active()->firstOrFail()->id;
        $this->put('/admin/quality-control/'.$itemId,$payload)->assertSessionHasNoErrors();
        $this->put('/admin/quality-control/'.$itemId,$payload)->assertSessionHasNoErrors();
        $product=Produk::where('kode_sku','FLOW-CAMERA')->firstOrFail();
        $this->assertEquals(2,$product->stok_produk);
        $this->get('/admin/purchases/'.$purchaseId.'/print')->assertOk()->assertHeader('Content-Type','application/pdf');
        $this->get('/admin/purchases/export/pdf?month='.now()->format('Y-m'))->assertOk()->assertHeader('Content-Type','application/pdf');
        $this->get('/admin/purchases/export/excel?month='.now()->format('Y-m'))->assertOk();
        $this->get('/admin/sales/export/pdf?month='.now()->format('Y-m'))->assertOk()->assertHeader('Content-Type','application/pdf');
        $this->get('/admin/sales/export/excel?month='.now()->format('Y-m'))->assertOk();
    }

    public function test_removing_one_of_two_photos_keeps_product_visible_and_selects_main(): void
    {
        Storage::fake('r2'); $this->actingAs($this->owner()); $product=$this->product();
        $first=$product->gambar()->create(['path_gambar'=>'test/first.webp','is_main'=>true]);
        $second=$product->gambar()->create(['path_gambar'=>'test/second.webp','is_main'=>false]);
        app(\App\Services\ProductService::class)->updateProduct($product, $product->toArray(), [], null, [$first->id]);
        $this->assertTrue($product->fresh()->is_visible);
        $this->assertEquals($second->id,$product->fresh()->gambarUtama->id);
    }

    public function test_uploaded_photos_replace_demo_images_in_public_pages(): void
    {
        $product=$this->product();
        $product->gambar()->create(['path_gambar'=>'public/test/main.webp','is_main'=>true]);
        $product->gambar()->create(['path_gambar'=>'public/test/extra.webp','is_main'=>false]);
        foreach (['/','/katalog','/katalog/'.$product->id] as $url) {
            $this->get($url)->assertOk()->assertSee(\App\Helpers\StoredImage::url('public/test/main.webp'));
        }
        $this->get('/katalog/'.$product->id)->assertSee(\App\Helpers\StoredImage::url('public/test/extra.webp'));
    }

    public function test_logo_upload_falls_back_when_r2_returns_false_and_updates_all_branding(): void
    {
        $local = Storage::fake('public');
        $remote = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $remote->shouldReceive('exists')->andReturn(false);
        $remote->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('r2')->andReturn($remote);
        Storage::shouldReceive('disk')->with('public')->andReturn($local);
        $this->actingAs($this->owner());
        $this->post('/admin/catalog-settings', [
            'site_name' => 'Uploaded Brand',
            'photo_logo' => \Illuminate\Http\UploadedFile::fake()->image('logo.png', 80, 80),
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/catalog-settings');
        $setting = CatalogSettings::first();
        $this->assertStringStartsWith('public/catalog/logo/', $setting->logo_path);
        Storage::disk('public')->assertExists(substr($setting->logo_path, 7));
        $url = $setting->logo_url;
        $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/webp');
        $this->get('/admin/catalog-settings')->assertOk()->assertSee($url, false)->assertSee('catalogSaveStatus')->assertSee('Menyimpan...');
        foreach (['/', '/katalog', '/about', '/contact'] as $page) {
            $this->get($page)->assertOk()->assertSee('rel="icon" href="'.$url.'"', false)->assertSee('src="'.$url.'"', false);
        }
        $this->post('/admin/catalog-settings', ['site_name' => 'Uploaded Brand'])->assertSessionHasNoErrors();
        $this->assertSame($setting->logo_path, $setting->fresh()->logo_path);
    }

    public function test_logo_endpoint_streams_remote_logo_without_public_bucket_url(): void
    {
        Storage::fake('r2');
        Storage::disk('r2')->put('catalog/logo/remote.webp', 'remote-image');
        $setting = CatalogSettings::firstOrCreate(['singleton_key' => true], ['nama_website' => 'Brand']);
        $setting->update(['logo_path' => 'catalog/logo/remote.webp']);
        $firstUrl = $setting->logo_url;
        $this->get($firstUrl)->assertOk()->assertStreamedContent('remote-image');
        $setting->update(['logo_path' => 'catalog/logo/missing.webp']);
        $this->assertNotSame($firstUrl, $setting->logo_url);
        $this->get($setting->logo_url)->assertNotFound();
    }

    public function test_hero_and_seo_uploads_are_accessible_from_local_fallback(): void
    {
        $local = Storage::fake('public');
        $remote = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $remote->shouldReceive('exists')->andThrow(new \RuntimeException('R2 unavailable'));
        Storage::shouldReceive('disk')->with('r2')->andReturn($remote);
        Storage::shouldReceive('disk')->with('public')->andReturn($local);
        $this->actingAs($this->owner());
        $this->post('/admin/catalog-settings', [
            'site_name' => 'Image Test',
            'hero_image' => \Illuminate\Http\UploadedFile::fake()->image('hero.png', 120, 80),
            'og_image' => \Illuminate\Http\UploadedFile::fake()->image('seo.png', 100, 60),
        ])->assertSessionHasNoErrors();
        $setting = CatalogSettings::first();
        foreach (['hero_image_path', 'og_image_path'] as $field) {
            $this->assertStringStartsWith('public/catalog/', $setting->{$field});
            $local->assertExists(substr($setting->{$field}, 7));
            $this->get(\App\Helpers\StoredImage::url($setting->{$field}))->assertOk()->assertHeader('Content-Type', 'image/webp');
        }
        $this->get('/')->assertOk()->assertSee($setting->hero_image_url)->assertSee($setting->og_image_url);
        $this->post('/admin/catalog-settings', ['site_name' => 'Image Test'])->assertSessionHasNoErrors();
        $this->assertSame($setting->hero_image_path, $setting->fresh()->hero_image_path);
        $this->assertSame($setting->og_image_path, $setting->fresh()->og_image_path);
    }

    public function test_stored_image_urls_support_local_and_private_r2_and_reject_tampering(): void
    {
        Storage::fake('public');
        Storage::fake('r2');
        config(['filesystems.disks.r2.url' => null]);
        Storage::disk('public')->put('category/test.webp', 'local-image');
        Storage::disk('r2')->put('product/test.webp', 'remote-image');
        $this->get(\App\Helpers\StoredImage::url('public/category/test.webp'))->assertOk()->assertStreamedContent('local-image');
        $url = \App\Helpers\StoredImage::url('product/test.webp');
        $this->get($url)->assertOk()->assertStreamedContent('remote-image');
        $this->get(str_replace('product', 'private', $url))->assertForbidden();
        $this->get('/media/image?path=public/category/test.webp')->assertForbidden();
        $this->get(\App\Helpers\StoredImage::url('public/../secret.webp'))->assertNotFound();
        $this->get(\App\Helpers\StoredImage::url('public/private.txt'))->assertNotFound();
        $this->get(\App\Helpers\StoredImage::url('public/missing.webp'))->assertNotFound();
        config(['filesystems.disks.r2.url' => 'https://images.example.com']);
        $this->assertSame('https://images.example.com/product/test.webp', \App\Helpers\StoredImage::url('product/test.webp'));
    }

    public function test_hero_image_is_shown_and_preserved_when_no_replacement_is_uploaded(): void
    {
        $this->actingAs($this->owner());
        $setting = CatalogSettings::firstOrCreate(['singleton_key'=>true], ['nama_website'=>'Pusat Kamera']);
        $setting->update(['hero_image_path'=>'public/test/hero.webp']);
        $this->get('/admin/catalog-settings')->assertOk()->assertSee('Gambar Hero')->assertSee(\App\Helpers\StoredImage::url('public/test/hero.webp'))->assertDontSee('Galeri Pelanggan');
        $this->post('/admin/catalog-settings', ['site_name'=>'Pusat Kamera'])->assertSessionHasNoErrors();
        $this->assertEquals('public/test/hero.webp', $setting->fresh()->hero_image_path);
        $this->get('/')->assertOk()->assertSee(\App\Helpers\StoredImage::url('public/test/hero.webp'));
    }

    public function test_invalid_hero_image_cannot_partially_change_settings(): void
    {
        $this->actingAs($this->owner());
        $setting=CatalogSettings::firstOrCreate(['singleton_key'=>true],['nama_website'=>'OLD-BRAND']);
        $this->post('/admin/catalog-settings',['site_name'=>'CHANGED-BRAND','hero_image'=>\Illuminate\Http\UploadedFile::fake()->create('invalid.txt', 1, 'text/plain')])
            ->assertSessionHasErrors('hero_image');
        $this->assertEquals('OLD-BRAND',$setting->fresh()->nama_website);
    }

    public function test_drag_order_is_saved_and_used_by_public_catalog(): void
    {
        $this->actingAs($this->owner());
        $categories=KategoriHarga::orderBy('urutan')->get();
        $ids=$categories->pluck('id')->reverse()->values()->all();
        $this->patchJson('/admin/price-categories/order', ['ids'=>$ids])->assertOk();
        $this->assertSame($ids,KategoriHarga::orderBy('urutan')->pluck('id')->all());
        foreach ($ids as $index=>$id) { $this->assertDatabaseHas('kategori_harga',['id'=>$id,'urutan'=>$index+1]); }
        $this->get('/')->assertSeeInOrder(KategoriHarga::active()->pluck('nama')->map(fn ($name) => strtoupper($name))->all());
        $this->get('/admin/price-categories')->assertOk()->assertSee('price-order-handle')->assertSee('Ubah Urutan')->assertSee('Simpan Urutan')->assertSee('Batal');
    }

    public function test_reordering_rejects_duplicate_missing_or_stale_ids_without_changes(): void
    {
        $this->actingAs($this->owner());
        $ids=KategoriHarga::orderBy('urutan')->pluck('id')->all();
        $this->patchJson('/admin/price-categories/order',['ids'=>[$ids[0],$ids[0]]])->assertUnprocessable();
        $this->patchJson('/admin/price-categories/order',['ids'=>array_slice($ids,1)])->assertStatus(409);
        $stale=$ids; $stale[0]=999999;
        $this->patchJson('/admin/price-categories/order',['ids'=>$stale])->assertStatus(409);
        $this->assertSame($ids,KategoriHarga::orderBy('urutan')->pluck('id')->all());
    }

    public function test_category_forms_do_not_accept_numeric_order_and_new_categories_append(): void
    {
        $this->actingAs($this->owner());
        $last=(int)KategoriHarga::max('urutan');
        $payload=['nama'=>'Kategori Tambahan','harga_minimum'=>123,'harga_maksimum'=>456,'is_active'=>1,'urutan'=>0];
        $this->get('/admin/price-categories/create')->assertOk()->assertDontSee('name="urutan"',false);
        $this->post('/admin/price-categories',$payload)->assertSessionHasNoErrors();
        $category=KategoriHarga::where('slug','kategori-tambahan')->firstOrFail();
        $this->assertSame($last+1,$category->urutan);
        $this->get('/admin/price-categories/'.$category->id.'/edit')->assertOk()->assertDontSee('name="urutan"',false);
        $payload['nama']='Kategori Diperbarui';
        $this->put('/admin/price-categories/'.$category->id,$payload)->assertSessionHasNoErrors();
        $this->assertSame($last+1,$category->fresh()->urutan);
    }

}
