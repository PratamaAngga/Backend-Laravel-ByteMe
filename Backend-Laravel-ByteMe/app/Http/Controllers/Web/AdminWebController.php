<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminWebController extends Controller
{
    // Login form
    public function loginForm()
    {
        return view('admin.login');
    }

    // Process login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'admin') {
            return back()->withErrors(['username' => 'Username, password salah atau bukan admin']);
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_produk' => Produk::count(),
            'produk_pending' => Produk::where('status', 'pending')->count(),
            'total_users' => User::count(),
            'users_banned' => User::where('status', 'banned')->count(),
            'withdraw_pending' => WithdrawRequest::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // Produk pending
    public function produkPending()
    {
        $produk = Produk::where('status', 'pending')->latest()->paginate(10);
        return view('admin.produk-pending', compact('produk'));
    }

    // Approve produk
    public function approveProduk($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->status = 'approved';
        $produk->save();

        return back()->with('success', 'Produk berhasil diapprove');
    }

    // Reject produk
    public function rejectProduk(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);

        $produk = Produk::findOrFail($id);
        $produk->status = 'rejected';
        $produk->save();

        return back()->with('success', 'Produk berhasil direject');
    }

    // List users
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    // Kategori index
    public function categories()
    {
        $categories = Kategori::latest()->paginate(10);
        return view('admin.kategori.index', compact('categories'));
    }

    // Form tambah kategori
    public function createCategory()
    {
        return view('admin.kategori.create');
    }

    // Store kategori
    public function storeCategory(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama',
        ]);

        Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    // Form edit kategori
    public function editCategory($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // Update kategori
    public function updateCategory(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama,' . $kategori->id,
        ]);

        $kategori->nama = $request->nama;
        $kategori->save();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diupdate');
    }

    // Hapus kategori
    public function destroyCategory($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dihapus');
    }

    // Ban user
    public function banUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Admin tidak bisa diban');
        }

        $user->status = 'banned';
        $user->save();

        return back()->with('success', 'User berhasil diban');
    }

    // Unban user
    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User berhasil diunban');
    }

    // List withdraws
    public function withdraws()
    {
        $withdraws = WithdrawRequest::with('user')->latest()->paginate(10);
        return view('admin.withdraws', compact('withdraws'));
    }

    // Approve withdraw
    public function approveWithdraw($id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        $withdraw->status = 'approved';
        $withdraw->admin_note = 'Approved by admin';
        $withdraw->save();

        return back()->with('success', 'Withdraw berhasil diapprove');
    }

    // Reject withdraw
    public function rejectWithdraw(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);

        $withdraw = WithdrawRequest::findOrFail($id);
        $withdraw->status = 'rejected';
        $withdraw->admin_note = $request->alasan;
        $withdraw->save();

        return back()->with('success', 'Withdraw berhasil direject');
    }

    // Profile
    public function profile()
    {
        return view('admin.profile');
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:profiles,username,' . Auth::id(),
            'email' => 'required|email|unique:profiles,email,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Tambahkan type hint User
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Profile berhasil diupdate');
    }
}