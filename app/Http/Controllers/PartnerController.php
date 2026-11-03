<?php

namespace App\Http\Controllers;

use App\Helpers\ImageUpload;
use App\Helpers\StoredImage;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PartnerController extends Controller
{
    public function __construct(private ImageUpload $images) {}

    public function index()
    {
        $partners = Partner::orderBy('id')->paginate(10);

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.form', ['partner' => new Partner(['is_active' => true])]);
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.form', compact('partner'));
    }

    public function store(PartnerRequest $request)
    {
        if (Partner::where('submission_token', $request->validated('submission_token'))->exists()) {
            return redirect()->route('admin.partners.index')->with('success', 'Partner sudah disimpan.');
        }

        return $this->save($request, new Partner);
    }

    public function update(PartnerRequest $request, Partner $partner)
    {
        return $this->save($request, $partner);
    }

    private function save(PartnerRequest $request, Partner $partner)
    {
        $data = $request->safe()->except('logo');
        $oldPath = $partner->logo_path;
        $newPath = null;
        if ($request->hasFile('logo')) {
            try {
                $newPath = $this->images->upload($request->file('logo'), 'catalog/partners/'.Str::uuid(), 640, false)['path'];
            } catch (\Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages(['logo' => 'Logo gagal disimpan. Silakan coba kembali.']);
            }
            $data['logo_path'] = $newPath;
        }
        try {
            $partner->fill($data)->save();
        } catch (\Throwable $exception) {
            $this->cleanup($newPath);
            if ($exception instanceof \Illuminate\Database\QueryException && $request->isMethod('POST')
                && Partner::where('submission_token', $request->validated('submission_token'))->exists()) {
                return redirect()->route('admin.partners.index')->with('success', 'Partner sudah disimpan.');
            }
            throw $exception;
        }
        if ($newPath) {
            $this->cleanup($oldPath);
        }

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil disimpan.');
    }

    public function destroy(Partner $partner)
    {
        $path = $partner->logo_path;
        $partner->delete();
        $this->cleanup($path);

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil dihapus.');
    }

    private function cleanup(?string $path): void
    {
        if (! $path) {
            return;
        }
        try {
            StoredImage::delete($path);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
