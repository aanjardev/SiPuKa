<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmailService;
use App\Models\Karyawan;

class PermissionsController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $query = DB::table('users')
            ->join('karyawan', 'users.id', '=', 'karyawan.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.status',
                'karyawan.status as karyawan_status'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'all') {
            $query->where('users.status', $request->status_filter);
        }

        $user_data = $query->paginate(10)->withQueryString();

        $roles = DB::table('users')->distinct()->pluck('role');

        return view('admin.permissions', compact('user_data', 'roles'))
            ->with('search_term', $request->input('search', ''))
            ->with('selected_role', $request->input('role', 'all'))
            ->with('selected_status', $request->input('status_filter', 'all'));
    }

    public function create()
    {
        $karyawan_data = DB::table('karyawan')
            ->whereNotIn('id', DB::table('users')->pluck('id'))
            ->where('status', 'aktif')
            ->get();
        return view('admin.inputPermissions', compact('karyawan_data'));
    }


    public function store(Request $request){
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required'
        ]);

        $karyawan = Employee::findOrFail($request->karyawan_name);

        $activationToken = bin2hex(random_bytes(32));

        $user = User::create([
            'id' => $karyawan->id,
            'name' => $karyawan->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // Random password yang tidak akan digunakan
            'role' => $request->role,
            'status' => 'pending',
            'activation_token' => $activationToken,
            'token_expiry' => now()->addHours(72), // Token berlaku 3 hari
        ]);

        $emailSent = $this->emailService->sendActivationEmail($user, $activationToken);

        if ($emailSent) {
            return redirect()->route('admin.permissions')
                ->with('success', 'User berhasil dibuat! Email aktivasi telah dikirim ke ' . $user->email);
        } else {

            return redirect()->route('admin.permissions')
                ->with('success', 'User berhasil dibuat! Namun email aktivasi gagal dikirim. Silakan generate ulang token.')
                ->with('warning', 'Email activation failed. Please check email configuration.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $karyawan_data = Employee::whereNotIn('id', User::pluck('id'))
                                ->orWhere('id', $id) // agar user saat ini tetap muncul
                                ->where('status', 'aktif')
                                ->get();
        return view('admin.inputPermissions', compact('user', 'karyawan_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'nullable|min:6',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:manager,staff',
        ]);

        $user = User::findOrFail($id);

        $emailChanged = $request->email !== $user->email;

        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($emailChanged) {

            $user->email_verified_at = null;
            $user->status = 'pending';
            $user->activation_token = bin2hex(random_bytes(32));
            $user->token_expiry = now()->addHours(72);
        }

        $user->save();

        if ($emailChanged) {

            $this->emailService->sendActivationEmail($user, $user->activation_token);

            try {
                if (config('session.driver') === 'database') {
                    $table = config('session.table', 'sessions');
                    DB::table($table)->where('user_id', $user->id)->delete();
                }

                $user->remember_token = Str::random(60);
                $user->save();
            } catch (\Throwable $e) {

            }

            return redirect()->route('admin.permissions')
                ->with('success', 'User berhasil diperbarui!');
        }

        return redirect()->route('admin.permissions')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {

        if (Auth::id() && (int) $id === (int) Auth::id()) {
            return redirect()->route('admin.permissions')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);

        Karyawan::where('id', $id)->update(['status' => 'non-aktif']);

        return redirect()->route('admin.permissions')->with('success', 'User dinonaktifkan. Anda dapat mengaktifkannya kembali jika diperlukan.');
    }

    public function regenerateToken($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->route('admin.permissions')
                ->with('error', 'Token hanya bisa di-generate untuk user dengan status pending.');
        }

        $activationToken = bin2hex(random_bytes(32));
        $user->update([
            'activation_token' => $activationToken,
            'token_expiry' => now()->addHours(72), // Extend 3 hari lagi
        ]);

        $emailSent = $this->emailService->sendActivationEmail($user, $activationToken);

        if ($emailSent) {
            return redirect()->route('admin.permissions')
                ->with('success', 'Token aktivasi berhasil diperbarui! Email baru telah dikirim ke ' . $user->email);
        } else {
            return redirect()->route('admin.permissions')
                ->with('success', 'Token aktivasi berhasil diperbarui! Namun email gagal dikirim.')
                ->with('warning', 'Email activation failed. Please check email configuration.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        if ($validated['status'] === 'inactive' && Auth::id() && (int) $id === (int) Auth::id()) {
            return redirect()->route('admin.permissions')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->update(['status' => $validated['status']]);

        $karyawanStatus = $validated['status'] === 'active' ? 'aktif' : 'non-aktif';
        Karyawan::where('id', $id)->update(['status' => $karyawanStatus]);

        $message = $validated['status'] === 'active'
            ? 'User berhasil diaktifkan.'
            : 'User berhasil dinonaktifkan. User non-aktif tidak dapat login.';

        return redirect()->route('admin.permissions')->with('success', $message);
    }

}
