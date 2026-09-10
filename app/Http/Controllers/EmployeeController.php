<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        if ($request->filled('jabatan') && $request->input('jabatan') !== 'all') {
            $query->where('jabatan', $request->input('jabatan'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama_lengkap', 'asc');
        } elseif ($sortBy === 'nama_desc') {
            $query->orderBy('nama_lengkap', 'desc');
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $employees = $query->paginate(10)->withQueryString();

        return view('admin.dataKaryawan', [
            'employees' => $employees,
            'search_term' => $request->input('search', ''),
            'selected_jabatan' => $request->input('jabatan', 'all'),
            'selected_status' => $request->input('status', 'all'),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function create()
    {
        return view('admin.inputDataKaryawan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                'unique:karyawan,nik'
            ],
            'jabatan' => [
                'required',
                'string',
                'max:50',
            ],
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'gaji' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'nullable|string|max:100',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
        ]);

        $status = !empty($validated['tanggal_keluar']) ? 'non-aktif' : $validated['status'];

        Employee::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'] ?? null,
            'status' => $status,
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'))->with('readOnly', true);
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $oldStatus = $employee->status;

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                'unique:karyawan,nik,' . $id
            ],
            'jabatan' => [
                'required',
                'string',
                'max:50',
            ],
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'gaji' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'nullable|string|max:100',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'tanggal_masuk'=>'Tanggal masuk harus berupa tanggal sebelum atau sama dengan hari ini.',
            'tanggal_keluar'=>'Tanggal keluar harus berupa tanggal setelah atau sama dengan tanggal masuk.',
        ]);

        $newStatus = !empty($validated['tanggal_keluar']) ? 'non-aktif' : $validated['status'];

        $employee->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'] ?? null,
            'status' => $newStatus,
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
        ]);

        if ($employee->user) {
            $employee->user->update([
                'name' => $validated['nama_lengkap'],
            ]);

            if (Auth::id() === $employee->user->id) {
                Auth::setUser($employee->user->fresh());
            }
        }

        if ($oldStatus !== 'non-aktif' && $newStatus === 'non-aktif') {
            if ($employee->user) {
                $employee->user->update(['status' => 'inactive']);


                \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $employee->user->id)
                    ->delete();
            }
        }

        elseif ($oldStatus !== 'aktif' && $newStatus === 'aktif') {
            if ($employee->user) {

                if ($employee->user->status === 'inactive') {
                    $employee->user->update(['status' => 'active']);
                }
            }
        }

        $redirect = match($request->input('redirect_to')) {
        'pageProfil' => route('admin.profile'), 
         default => route('admin.employees.index'),
    };

        $message = 'Karyawan berhasil diperbarui.';

        if ($oldStatus !== 'non-aktif' && $newStatus === 'non-aktif' && $employee->user) {
            $message .= ' Hak akses user telah dinonaktifkan.';
        } elseif ($oldStatus !== 'aktif' && $newStatus === 'aktif' && $employee->user && $employee->user->status === 'active') {
            $message .= ' Hak akses user telah diaktifkan kembali.';
        }

        return redirect($redirect)->with('success', $message);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->user) {
            $employee->user->delete();
        }
        
        $employee->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil dihapus.');
    }
}
