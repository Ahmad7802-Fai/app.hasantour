<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * =====================================================
     * LOGIN — SINGLE ENTRY, MULTI ROLE (STABIL)
     * =====================================================
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ LOGIN JAMAAH
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('jamaah')->attempt([
            $loginField => $request->login,
            'password'  => $request->password,
            'is_active' => 1,
        ])) {

            // 🔐 Regenerate & bersihkan redirect lama
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            // Security marker
            session([
                'password_confirmed_at' => now()->timestamp,
            ]);

            return redirect()->route('jamaah.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ LOGIN INTERNAL (WEB)
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('web')->attempt([
            'email'     => $request->login,
            'password'  => $request->password,
            'is_active' => 1,
        ])) {

            // 🔐 Regenerate & bersihkan redirect lama
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            $user = Auth::guard('web')->user();

            /*
            |--------------------------------------------------------------------------
            | 🎯 PENENTUAN DASHBOARD (SATU-SATUNYA TEMPAT)
            |--------------------------------------------------------------------------
            */

            // AGENT (SALES + agent_id)
            if ($user->isAgent()) {
                return redirect()->route('agent.dashboard');
            }


            // ADMIN CABANG
            if ($user->role === 'ADMIN' && !empty($user->branch_id)) {
                return redirect()->route('cabang.dashboard');
            }

            // PUSAT (ADMIN PUSAT, SALES PUSAT, SUPERADMIN, DLL)
            return redirect()->route('dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ GAGAL LOGIN
        |--------------------------------------------------------------------------
        */
        return back()->withErrors([
            'login' => 'Email / No HP atau password salah',
        ]);
    }

    /**
     * =====================================================
     * LOGOUT — BERSIH SEMUA SESSION
     * =====================================================
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('jamaah')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
//     public function login(Request $request)
//     {
//         $request->validate([
//             'login'    => 'required|string',
//             'password' => 'required|string',
//         ]);

//         $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
//             ? 'email'
//             : 'phone';

//         /*
//         |--------------------------------------------------------------------------
//         | 1️⃣ LOGIN JAMAAH
//         |--------------------------------------------------------------------------
//         */
//         if (Auth::guard('jamaah')->attempt([
//             $loginField => $request->login,
//             'password'  => $request->password,
//             'is_active' => 1,
//         ])) {

//             $request->session()->regenerate();

//             session([
//                 'password_confirmed_at' => now()->timestamp,
//             ]);

//             return redirect()->route('jamaah.dashboard');
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | 2️⃣ LOGIN INTERNAL (WEB)
//         |--------------------------------------------------------------------------
//         */
//         if (Auth::guard('web')->attempt([
//             'email'     => $request->login,
//             'password'  => $request->password,
//             'is_active' => 1,
//         ])) {

//             $request->session()->regenerate();

//             $user = Auth::guard('web')->user();

//             /*
//             |--------------------------------------------------------------------------
//             | 🔐 REDIRECT FINAL (BERDASARKAN FUNGSI)
//             |--------------------------------------------------------------------------
//             */

//             // 🧭 SALES → AGENT
//             if ($user->role === 'SALES' && $user->agent_id) {
//                 return redirect()->route('agent.dashboard');
//             }

//             // 🏢 ADMIN CABANG
//             if ($user->role === 'ADMIN' && $user->branch_id) {
//                 return redirect()->route('cabang.dashboard');
//             }

//             // 🏢 PUSAT (ADMIN / SALES / ROLE LAIN)
//             return redirect()->route('dashboard');
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | 3️⃣ GAGAL LOGIN
//         |--------------------------------------------------------------------------
//         */
//         return back()->withErrors([
//             'login' => 'Email / No HP atau password salah',
//         ]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | LOGOUT — SEMUA GUARD
//     |--------------------------------------------------------------------------
//     */
//     public function logout(Request $request)
//     {
//         Auth::guard('web')->logout();
//         Auth::guard('jamaah')->logout();

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }
// }

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
//     /**
//      * =====================================================
//      * LOGIN — SATU PINTU (JAMAAH & INTERNAL)
//      * =====================================================
//      */
//     public function login(Request $request)
//     {
//         $request->validate([
//             'login'    => 'required|string',
//             'password' => 'required|string',
//         ]);

//         $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
//             ? 'email'
//             : 'phone';

//         /*
//         |--------------------------------------------------------------------------
//         | LOGIN JAMAAH (GUARD: jamaah)
//         |--------------------------------------------------------------------------
//         */
//         if (Auth::guard('jamaah')->attempt([
//             $loginField => $request->login,
//             'password'  => $request->password,
//             'is_active' => 1,
//         ])) {

//             $request->session()->regenerate();

//             // Security marker
//             session([
//                 'password_confirmed_at' => now()->timestamp,
//             ]);

//             return redirect()->route('jamaah.dashboard');
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | LOGIN USER INTERNAL (GUARD: web)
//         |--------------------------------------------------------------------------
//         */
//         if (Auth::guard('web')->attempt([
//             'email'    => $request->login,
//             'password' => $request->password,
//             'is_active'=> 1,
//         ])) {

//             $request->session()->regenerate();

//             $user = Auth::user();
//             $role = strtolower($user->role);

//             /*
//             |--------------------------------------------------------------------------
//             | 🔐 ROLE-BASED REDIRECT (FINAL)
//             |--------------------------------------------------------------------------
//             */
//             return match ($role) {

//                 // ======================
//                 // AGENT / SALES
//                 // ======================
//                 'sales' => redirect()->route('agent.dashboard'),

//                 // ======================
//                 // ADMIN
//                 // - Punya branch_id → Admin Cabang
//                 // - Tanpa branch_id → Admin Pusat
//                 // ======================
//                 'admin' => $user->branch_id
//                     ? redirect()->route('cabang.dashboard')
//                     : redirect()->route('dashboard'),

//                 // ======================
//                 // SUPERADMIN
//                 // ======================
//                 'superadmin' => redirect()->route('dashboard'),

//                 // ======================
//                 // ROLE LAIN (OPERATOR, KEUANGAN, INVENTORY, DLL)
//                 // ======================
//                 default => redirect()->route('dashboard'),
//             };
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | GAGAL LOGIN
//         |--------------------------------------------------------------------------
//         */
//         return back()->withErrors([
//             'login' => 'Email / No HP atau password salah',
//         ]);
//     }

//     /**
//      * =====================================================
//      * LOGOUT — SEMUA GUARD (AMAN)
//      * =====================================================
//      */
//     public function logout(Request $request)
//     {
//         Auth::guard('web')->logout();
//         Auth::guard('jamaah')->logout();

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }
// }

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
//     /**
//      * =====================================================
//      * LOGIN — SATU PINTU (JAMAAH & INTERNAL)
//      * =====================================================
//      */
//     public function login(Request $request)
//     {
//         $request->validate([
//             'login'    => 'required|string',
//             'password' => 'required|string',
//         ]);

//         $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
//             ? 'email'
//             : 'phone';

//         /* =================================================
//          | LOGIN JAMAAH
//          ================================================= */
//         if (Auth::guard('jamaah')->attempt([
//             $loginField => $request->login,
//             'password'  => $request->password,
//             'is_active' => 1,
//         ])) {

//             $request->session()->regenerate();

//             // 🔐 Security marker (untuk middleware password confirm)
//             session([
//                 'password_confirmed_at' => now()->timestamp,
//             ]);

//             return redirect()->route('jamaah.dashboard');
//         }

//         /* =================================================
//          | LOGIN USER INTERNAL (ADMIN / STAFF)
//          ================================================= */
//         if (Auth::guard('web')->attempt([
//             'email'    => $request->login,
//             'password' => $request->password,
//         ])) {

//             $request->session()->regenerate();

//             return redirect('/dashboard'); // dashboard internal
//         }

//         /* =================================================
//          | GAGAL LOGIN
//          ================================================= */
//         return back()->withErrors([
//             'login' => 'Email / No HP atau password salah',
//         ]);
//     }

//     /**
//      * =====================================================
//      * LOGOUT — SEMUA GUARD (AMAN)
//      * =====================================================
//      */
//     public function logout(Request $request)
//     {
//         Auth::guard('web')->logout();
//         Auth::guard('jamaah')->logout();

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }
// }
