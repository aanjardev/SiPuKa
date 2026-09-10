@php
    $setting = $cat_setting ?? \App\Models\CatalogSettings::first();

    $rawPhone = preg_replace('/\D+/', '', $setting?->nomor_telfon ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }

    $storeName = $setting?->nama_website ?? 'Toko';
    $message = "Halo {$storeName}, saya ingin bertanya tentang produk yang ada di katalog.";
    $waLink = $rawPhone ? ('https://wa.me/' . $rawPhone . '?text=' . urlencode($message)) : null;
@endphp

@if ($waLink)
    <!-- Floating WhatsApp Button -->
    <a href="{{ $waLink }}" class="floating-wa" target="_blank" rel="noopener noreferrer">
        <i class="fa-brands fa-whatsapp"></i>
        <span>Chat via WhatsApp</span>
    </a>
@endif

<!-- WhatsApp Button Styles -->
<link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/components/floating-wa.css') }}">
