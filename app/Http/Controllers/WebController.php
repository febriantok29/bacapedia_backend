<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\User;
use App\Services\BorrowService;
use App\Support\Enums\BorrowStatus;
use App\Support\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    public function login()
    {
        if (Session::has('user_id')) {
            return redirect('/');
        }
        return view('panel.login');
    }

    public function doLogin(Request $request)
    {
        $user = User::where('email', strtolower($request->credentials))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah')->withInput();
        }

        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_role', $user->role);
        Session::put('user_email', $user->email);

        return redirect('/');
    }

    public function register()
    {
        if (Session::has('user_id')) {
            return redirect('/');
        }
        return view('panel.register');
    }

    public function doRegister(Request $request)
    {
        $existing = User::where('email', strtolower($request->email))->first();
        if ($existing) {
            return back()->with('error', 'Email sudah digunakan')->withInput();
        }

        $prefix = 'USR-';
        $lastUser = User::where('user_code', 'like', $prefix . '%')->orderByDesc('user_code')->first();
        $nextNumber = $lastUser ? (int) substr($lastUser->user_code, -5) + 1 : 1;
        $userCode = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $user = User::create([
            'user_code' => $userCode,
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => UserRole::MEMBER->value,
        ]);

        $user->created_by = $user->id;
        $user->save();

        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_role', $user->role);
        Session::put('user_email', $user->email);

        return redirect('/');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }

    public function dashboard()
    {
        $totalBooks = Book::count();
        $totalUsers = User::count();
        $totalActive = Borrow::where('status', BorrowStatus::ACTIVE->value)->count();
        $totalOverdue = Borrow::where('status', BorrowStatus::ACTIVE->value)
            ->where('due_date', '<', Carbon::today())->count();
        $totalReturned = Borrow::where('status', BorrowStatus::RETURNED->value)->count();
        $totalLate = Borrow::where('status', BorrowStatus::OVERDUE->value)->count();
        $totalPenalty = Borrow::where('penalty', '>', 0)->sum('penalty');
        $totalCategories = Category::count();

        return view('panel.dashboard', compact(
            'totalBooks', 'totalUsers', 'totalActive', 'totalOverdue',
            'totalReturned', 'totalLate', 'totalPenalty', 'totalCategories'
        ));
    }

    public function books(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->orderBy('title')->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('panel.books', compact('books', 'categories'));
    }

    public function storeBook(Request $request)
    {
        $year = now()->format('Y');
        $prefix = "BK-{$year}-";
        $lastBook = Book::where('book_code', 'like', "{$prefix}%")->orderByDesc('book_code')->first();
        $nextNumber = $lastBook ? (int) substr($lastBook->book_code, -5) + 1 : 1;
        $bookCode = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        Book::create([
            'book_code' => $bookCode,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'published_year' => $request->published_year,
            'stock' => $request->stock,
        ]);

        return back()->with('success', 'Buku berhasil ditambahkan');
    }

    public function updateBook(Request $request, string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return back()->with('error', 'Buku tidak ditemukan');
        }

        $book->fill($request->only(['category_id', 'title', 'author', 'publisher', 'published_year', 'stock']));
        $book->save();

        return back()->with('success', 'Buku berhasil diperbarui');
    }

    public function deleteBook(string $id)
    {
        $book = Book::find($id);
        if ($book) {
            $book->delete();
        }
        return back()->with('success', 'Buku berhasil dihapus');
    }

    public function categories()
    {
        $categories = Category::withCount('books')->orderBy('name')->paginate(15);
        return view('panel.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        Category::create(['name' => $request->name]);
        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateCategory(Request $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return back()->with('error', 'Kategori tidak ditemukan');
        }

        $category->name = $request->name;
        $category->save();

        return back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function deleteCategory(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
        }
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function borrows(Request $request)
    {
        $role = Session::get('user_role');
        $isStaff = in_array($role, [UserRole::ADMIN->value, UserRole::OFFICER->value]);

        $query = Borrow::with(['book', 'user']);

        if (!$isStaff) {
            $query->where('user_id', Session::get('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id') && $isStaff) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->boolean('is_overdue')) {
            $query->where('status', BorrowStatus::ACTIVE->value)
                  ->where('due_date', '<', Carbon::today());
        }

        $borrows = $query->orderByDesc('created_at')->paginate(15);
        $books = Book::where('stock', '>', 0)->orderBy('title')->get();
        $users = $isStaff ? User::where('role', UserRole::MEMBER->value)->orderBy('name')->get() : collect();

        return view('panel.borrows', compact('borrows', 'books', 'users', 'isStaff'));
    }

    public function borrowDetail(string $id)
    {
        $borrow = Borrow::with(['book', 'user', 'histories'])->find($id);

        if (!$borrow) {
            return back()->with('error', 'Data peminjaman tidak ditemukan');
        }

        $role = Session::get('user_role');
        if ($role === UserRole::MEMBER->value && $borrow->user_id !== Session::get('user_id')) {
            return back()->with('error', 'Anda tidak memiliki akses');
        }

        return view('panel.borrow-detail', compact('borrow'));
    }

    public function storeBorrow(Request $request)
    {
        $borrowService = app(BorrowService::class);
        $role = Session::get('user_role');
        $isStaff = in_array($role, [UserRole::ADMIN->value, UserRole::OFFICER->value]);

        $bookIds = $request->input('book_ids', []);
        if (empty($bookIds)) {
            return back()->with('error', 'Pilih minimal 1 buku');
        }

        $borrowUserId = ($isStaff && $request->filled('user_id'))
            ? $request->user_id
            : Session::get('user_id');

        $borrowUser = User::find($borrowUserId);
        if (!$borrowUser) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $successCount = 0;
        $errors = [];

        foreach ($bookIds as $bookId) {
            $result = $borrowService->borrow($borrowUser, $bookId, Session::get('user_id'), [
                'borrow_date' => $request->borrow_date,
            ]);

            if ($result['success']) {
                $successCount++;
            } else {
                $book = Book::find($bookId);
                $errors[] = ($book->title ?? $bookId) . ': ' . $result['message'];
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return back()->with('success', "Berhasil meminjam {$successCount} buku");
        }

        if ($successCount > 0 && !empty($errors)) {
            return back()
                ->with('success', "Berhasil meminjam {$successCount} buku")
                ->with('error', implode('. ', $errors));
        }

        return back()->with('error', implode('. ', $errors));
    }

    public function returnBorrow(string $id)
    {
        $borrowService = app(BorrowService::class);
        $result = $borrowService->returnBook($id, Session::get('user_id'));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        $penalty = $result['data']->penalty ?? 0;
        $message = 'Pengembalian berhasil';
        if ($penalty > 0) {
            $message .= '. Denda: Rp' . number_format($penalty, 0, ',', '.');
        }

        return back()->with('success', $message);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15);
        return view('panel.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $existing = User::where('email', strtolower($request->email))->first();
        if ($existing) {
            return back()->with('error', 'Email sudah digunakan')->withInput();
        }

        $prefix = 'USR-';
        $lastUser = User::where('user_code', 'like', $prefix . '%')->orderByDesc('user_code')->first();
        $nextNumber = $lastUser ? (int) substr($lastUser->user_code, -5) + 1 : 1;
        $userCode = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        User::create([
            'user_code' => $userCode,
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User berhasil ditambahkan');
    }

    public function updateUser(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        if ($request->filled('name')) $user->name = $request->name;
        if ($request->filled('email')) $user->email = strtolower($request->email);
        if ($request->filled('role')) $user->role = $request->role;
        $user->save();

        return back()->with('success', 'User berhasil diperbarui');
    }

    public function deleteUser(string $id)
    {
        $user = User::find($id);
        if ($user && $user->id !== Session::get('user_id')) {
            $user->delete();
            return back()->with('success', 'User berhasil dihapus');
        }
        return back()->with('error', 'Tidak dapat menghapus akun sendiri');
    }

    public function resetUserPassword(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil direset');
    }
}
