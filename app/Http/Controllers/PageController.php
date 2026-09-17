<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\CatalogSettings;
use App\Models\CatalogCustomerGallery;
use App\Models\Kategori;
use App\Models\Branch;
use App\Models\KategoriHarga;

class PageController extends Controller
{
    public function index(){
        $latestProducts = Produk::with('gambarUtama')
            ->where('stok_produk', '>', 0)
            ->where('is_visible', true)
            ->where('is_archived', false)
            ->latest()
            ->take(8)
            ->get();

        $cat_setting = CatalogSettings::first();
        $kategoris = Kategori::orderBy('id')->take(6)->get();
        $managedBudgetCategories = KategoriHarga::active()->get();

        return view('mainPage', compact('latestProducts', 'cat_setting', 'kategoris', 'managedBudgetCategories'));

    }

    public function about(){
        $cat_setting = CatalogSettings::first();
        $gallery = CatalogCustomerGallery::orderBy('sort_order')->orderBy('id')->get();
        return view("AboutStore", compact('cat_setting', 'gallery'));
    }

    public function contact(){
        $cat_setting = CatalogSettings::first();
        $stores = Branch::with('jamOperasional')
            ->where('is_active', true)
            ->get()
            ->map(function ($branch) {
                $phone = preg_replace('/\D+/', '', $branch->nomor_telepon ?? '');
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                } elseif (str_starts_with($phone, '8')) {
                    $phone = '62' . $phone;
                }

                return [
                    'id' => $branch->id,
                    'nama' => $branch->nama,
                    'alamat' => $branch->alamat,
                    'telepon' => $branch->nomor_telepon,
                    'maps_embed_url' => $branch->maps_embed_url,
                    'wa_phone' => $phone,
                    'jam' => $this->formatJamOperasional($branch->jamOperasional),
                ];
            });

        return view("contact", compact('cat_setting', 'stores'));
    }

    public function katalog(){
        return view("product");
    }

    public function admin()
    {
        return redirect()->route('admin.dashboard');
    }

    public function edit(){
        return view("admin.edit");
    }

    public function sitemap()
    {
        $products = Produk::where('is_visible', true)->where('is_archived', false)->get(['id', 'updated_at']);

        return response()
            ->view('sitemap', compact('products'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: " . url('/sitemap.xml') . "\n", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Format jam operasional ringkas + detail harian.
     */
    private function formatJamOperasional($jamCollection)
    {
        if (!$jamCollection || $jamCollection->isEmpty()) {
            return [
                'ringkas' => 'Jam operasional tidak tersedia',
                'catatan' => null,
                'harian' => collect([]),
                'hari_ini' => ['hari' => now()->translatedFormat('l'), 'slot' => 'Jadwal tidak tersedia'],
            ];
        }

        $hariUrut = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        $hariSingkat = [
            'Senin'=>'Sen', 'Selasa'=>'Sel', 'Rabu'=>'Rab', 'Kamis'=>'Kam', 'Jumat'=>'Jum', 'Sabtu'=>'Sab', 'Minggu'=>'Min'
        ];

        $data = [];
        foreach ($hariUrut as $hari) {
            $row = $jamCollection->firstWhere('hari', $hari);
            if ($row && $row->is_buka) {
                $slot = ($row->jam_buka && $row->jam_tutup)
                    ? \Illuminate\Support\Str::of($row->jam_buka)->beforeLast(':') . ' - ' . \Illuminate\Support\Str::of($row->jam_tutup)->beforeLast(':')
                    : 'Buka';
            } else {
                $slot = 'Tutup';
            }
            $data[] = [
                'hari' => $hari,
                'label' => $hariSingkat[$hari],
                'slot' => $slot,
                'catatan' => $row->catatan ?? null,
            ];
        }

        $grup = [];
        foreach ($data as $item) {
            if (empty($grup) || end($grup)['slot'] !== $item['slot']) {
                $grup[] = [
                    'slot' => $item['slot'],
                    'labels' => [$item['label']],
                    'catatan' => $item['catatan'] ?? null,
                ];
            } else {
                $grup[count($grup)-1]['labels'][] = $item['label'];
                if ($item['catatan']) {
                    $grup[count($grup)-1]['catatan'] = $item['catatan'];
                }
            }
        }

        $parts = [];
        $catatan = null;
        foreach ($grup as $g) {
            $hariLabel = count($g['labels']) > 1
                ? $g['labels'][0] . ' - ' . end($g['labels'])
                : $g['labels'][0];
            $parts[] = $hariLabel . ' ' . $g['slot'];
            if ($g['catatan']) {
                $catatan = $g['catatan'];
            }
        }

        $todayIndex = now()->dayOfWeekIso;
        $hariIni = $data[$todayIndex - 1] ?? [
            'hari' => now()->translatedFormat('l'),
            'slot' => 'Jadwal tidak tersedia',
            'catatan' => null,
        ];

        return [
            'ringkas' => implode('; ', $parts),
            'catatan' => $catatan,
            'harian' => collect($data)->map(function($d){
                return [
                    'hari' => $d['hari'],
                    'slot' => $d['slot'],
                    'catatan' => $d['catatan'] ?? null,
                ];
            }),
            'hari_ini' => $hariIni,
        ];
    }
}
