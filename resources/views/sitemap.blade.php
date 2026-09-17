{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach (['/', '/katalog', '/about', '/contact'] as $path)
    <url><loc>{{ url($path) }}</loc></url>
    @endforeach
    @foreach ($products as $product)
    <url>
        <loc>{{ route('product.show', $product->id) }}</loc>
        <lastmod>{{ optional($product->updated_at)->toAtomString() }}</lastmod>
    </url>
    @endforeach
</urlset>
