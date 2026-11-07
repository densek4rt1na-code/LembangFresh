<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '! Anda berhasil masuk.');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users,email',
            'role' => 'required|in:petani,pembeli,mitra',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau silakan masuk.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Pilihan peran tidak valid.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'address.required' => 'Alamat di Kecamatan Lembang wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $defaultCoordinates = [
            'petani' => ['lat' => -6.7950, 'lng' => 107.6350],
            'pembeli' => ['lat' => -6.8110, 'lng' => 107.6170],
            'mitra' => ['lat' => -6.8050, 'lng' => 107.6200],
        ];

        $coords = $defaultCoordinates[$validated['role']] ?? ['lat' => -6.8120, 'lng' => 107.6180];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'lat' => $coords['lat'],
            'lng' => $coords['lng'],
            'is_verified' => true,
            'delivery_available' => $validated['role'] === 'petani',
            'delivery_radius' => 5.0,
            'delivery_fee' => 25000,
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat bergabung di LembangFresh, ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem LembangFresh.');
    }
}

