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
use App\Services\SupabaseStorageService;

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
            return back()->withErrors(['username' => 'Incorrect username, password, or unauthorized access.']);
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
        $produk = Produk::with('user') // ← tambahkan ini
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        
        return view('admin.produk-pending', compact('produk'));
    }

    // Approve produk
    public function approveProduk($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->status = 'approved';
        $produk->save();

        return back()->with('success', 'Product approved successfully.');
    }

    // Reject produk
    public function rejectProduk(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);

        $produk = Produk::findOrFail($id);
        $produk->status = 'rejected';
        $produk->save();

        return back()->with('success', 'Product rejected successfully.');
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

        return redirect()->route('admin.categories')->with('success', 'Category added successfully.');
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

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully.');
    }

    // Hapus kategori
    public function destroyCategory($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully.');
    }

    // Ban user
    public function banUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be banned.');
        }

        $user->status = 'banned';
        $user->save();

        return back()->with('success', 'User banned successfully.');
    }

    // Unban user
    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User unbanned successfully.');
    }

    protected SupabaseStorageService $storage;

    public function __construct(SupabaseStorageService $storage)
    {
        $this->storage = $storage;
    }

    // List withdraw pending
    public function withdraws()
    {
        $withdraws = WithdrawRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.withdraws', compact('withdraws'));
    }

    // List withdraw handled
    public function withdrawsHandled()
    {
        $withdraws = WithdrawRequest::with('user')
            ->where('status', 'handled')
            ->latest()
            ->paginate(10);

        return view('admin.withdraws-handled', compact('withdraws'));
    }

    // Approve → ubah ke handled
    public function approveWithdraw($id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);

        if ($withdraw->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses');
        }

        $withdraw->status = 'handled';
        $withdraw->admin_note = 'Disetujui oleh admin';
        $withdraw->save();

        return back()->with('success', 'Request withdraw disetujui, silakan transfer manual');
    }

    // Upload bukti transfer → ubah ke success
    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'admin_note'   => 'nullable|string',
        ]);

        $withdraw = WithdrawRequest::findOrFail($id);

        if ($withdraw->status !== 'handled') {
            return back()->with('error', 'Request ini belum dalam status handled');
        }

        $file     = $request->file('receipt_file');
        $fileName = 'receipt_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        $uploadedUrl = $this->storage->uploadToBucket(
            $file->getRealPath(),
            $fileName,
            $file->getMimeType(),
            'transfer_receipt'
        );

        if (!$uploadedUrl) {
            return back()->with('error', 'Gagal mengupload bukti transfer');
        }

        $withdraw->receipt_file = $uploadedUrl;
        $withdraw->status       = 'success';
        $withdraw->admin_note   = $request->admin_note ?? $withdraw->admin_note;
        $withdraw->save();

        return back()->with('success', 'Bukti transfer berhasil diupload, withdraw selesai');
    }

    // Reject withdraw
    public function rejectWithdraw(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $withdraw = WithdrawRequest::findOrFail($id);

        if ($withdraw->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses');
        }

        // Kembalikan saldo seller
        $user = $withdraw->user;
        $user->balance += $withdraw->amount;
        $user->save();

        $withdraw->status     = 'rejected';
        $withdraw->admin_note = $request->alasan;
        $withdraw->save();

        return back()->with('success', 'Request withdraw direject dan saldo dikembalikan');
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
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}