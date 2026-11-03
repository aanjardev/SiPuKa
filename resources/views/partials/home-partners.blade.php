@if($partners->isNotEmpty() || $showPartnerDemo)
<section class="brutal-section partner-section" aria-labelledby="partner-title">
    <div class="container">
        <div class="partner-heading"><span>KOLABORASI & KOMUNITAS</span><h2 id="partner-title">PARTNER KAMI</h2><p>Bersama mendukung perjalanan fotografi dan kreativitas.</p></div>
        @if($showPartnerDemo)<p class="partner-demo-note">Contoh tampilan: nama dan logo berikut adalah partner fiktif.</p>@endif
        <div class="partner-grid">
            @if($showPartnerDemo)
                @foreach(['Ruang Lensa', 'Akademi Visual', 'Komunitas Bidik'] as $nama)
                    <figure class="partner-item"><img src="{{ asset('mainIMG/partners/demo-'.($loop->index + 1).'.svg') }}" alt="Logo contoh {{ $nama }}" width="160" height="90" loading="lazy"><figcaption>{{ $nama }}</figcaption></figure>
                @endforeach
            @else
                @foreach($partners as $partner)
                    <figure class="partner-item"><img src="{{ $partner->logo_url }}" alt="Logo {{ $partner->nama }}" width="160" height="90" loading="lazy"><figcaption>{{ $partner->nama }}</figcaption></figure>
                @endforeach
            @endif
        </div>
    </div>
</section>
@endif
