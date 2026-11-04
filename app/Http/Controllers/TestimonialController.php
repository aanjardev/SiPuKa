<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestimonialRequest;
use App\Models\Testimonial;
use App\Services\TestimonialService;

class TestimonialController extends Controller
{
    public function publicIndex()
    {
        $testimonials = Testimonial::where('is_active', true)->orderByDesc('created_at')->orderByDesc('id')->paginate(12);

        return view('testimonials', compact('testimonials'));
    }

    public function index()
    {
        $testimonials = Testimonial::orderByDesc('created_at')->orderByDesc('id')->paginate(12);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial(['is_active' => true])]);
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function store(TestimonialRequest $request, TestimonialService $service)
    {
        return $this->save($request, new Testimonial, $service);
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial, TestimonialService $service)
    {
        return $this->save($request, $testimonial, $service);
    }

    private function save(TestimonialRequest $request, Testimonial $testimonial, TestimonialService $service)
    {
        $service->save($testimonial, $request->safe()->except(['screenshot', 'remove_screenshot']), $request->file('screenshot'), $request->boolean('remove_screenshot'));

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil disimpan.');
    }

    public function destroy(Testimonial $testimonial, TestimonialService $service)
    {
        $service->delete($testimonial);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}
