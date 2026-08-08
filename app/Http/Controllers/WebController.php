<?php

namespace App\Http\Controllers;

use App\Support\ApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function login()
    {
        if (Session::has('access_token')) {
            return redirect('/');
        }
        return view('pages.login');
    }

    public function doLogin(Request $request)
    {
        return $this->authenticate('/auth/login', [
            'credentials' => $request->credentials,
            'password' => $request->password,
            'is_debug' => true,
            'access_token_ttl' => 86400,
        ]);
    }

    public function register()
    {
        if (Session::has('access_token')) {
            return redirect('/');
        }
        return view('pages.register');
    }

    public function doRegister(Request $request)
    {
        return $this->authenticate('/auth/register', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
    }

    public function logout()
    {
        $this->api->post('/auth/logout');
        Session::flush();
        return redirect('/login');
    }

    public function dashboard()
    {
        $summary = $this->api->get('/borrows/summary');
        $books = $this->api->get('/books', ['per_page' => 1]);
        $categories = $this->api->get('/categories', ['per_page' => 1]);

        $totalUsers = 0;
        if (Session::get('user_role') === 'Admin') {
            $users = $this->api->get('/users', ['per_page' => 1]);
            $totalUsers = $users['metadata']['total'] ?? 0;
        }

        return view('pages.dashboard', [
            'totalBooks' => $books['metadata']['total'] ?? 0,
            'totalCategories' => $categories['metadata']['total'] ?? 0,
            'totalUsers' => $totalUsers,
            'totalActive' => $summary['data']['total_active'] ?? 0,
            'totalOverdue' => $summary['data']['total_overdue'] ?? 0,
            'totalReturned' => $summary['data']['total_returned'] ?? 0,
            'totalLate' => $summary['data']['total_late_returned'] ?? 0,
            'totalPenalty' => $summary['data']['total_penalty_collected'] ?? 0,
        ]);
    }

    public function books(Request $request)
    {
        $params = $this->filterQuery($request, ['search', 'category_id', 'published_year', 'page', 'per_page']);
        $data = $this->api->get('/books', $params);
        $categories = $this->api->get('/categories', ['per_page' => 100]);

        return view('pages.books', [
            'books' => $this->toPaginator($data, $request),
            'categories' => $categories['data'] ?? [],
        ]);
    }

    public function storeBook(Request $request)
    {
        return $this->mutate($this->api->post('/books', [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'published_year' => (int) $request->published_year,
            'stock' => (int) $request->stock,
        ]));
    }

    public function updateBook(Request $request, string $id)
    {
        return $this->mutate($this->api->put("/books/{$id}", [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'published_year' => (int) $request->published_year,
            'stock' => (int) $request->stock,
        ]));
    }

    public function deleteBook(string $id)
    {
        return $this->mutate($this->api->delete("/books/{$id}"));
    }

    public function categories(Request $request)
    {
        $params = $this->filterQuery($request, ['search', 'page', 'per_page']);
        $data = $this->api->get('/categories', $params);

        return view('pages.categories', [
            'categories' => $this->toPaginator($data, $request),
        ]);
    }

    public function storeCategory(Request $request)
    {
        return $this->mutate($this->api->post('/categories', ['name' => $request->name]));
    }

    public function updateCategory(Request $request, string $id)
    {
        return $this->mutate($this->api->put("/categories/{$id}", ['name' => $request->name]));
    }

    public function deleteCategory(string $id)
    {
        return $this->mutate($this->api->delete("/categories/{$id}"));
    }

    public function borrows(Request $request)
    {
        $params = $this->filterQuery($request, ['status', 'user_id', 'page', 'per_page']);
        if ($request->boolean('is_overdue')) {
            $params['is_overdue'] = '1';
        }

        $data = $this->api->get('/borrows', $params);
        $booksData = $this->api->get('/books', ['per_page' => 100]);

        $isStaff = in_array(Session::get('user_role'), ['Admin', 'Petugas']);
        $usersData = $isStaff ? $this->api->get('/users', ['per_page' => 100]) : ['data' => []];

        return view('pages.borrows', [
            'borrows' => $this->toPaginator($data, $request),
            'books' => $booksData['data'] ?? [],
            'users' => $usersData['data'] ?? [],
            'isStaff' => $isStaff,
        ]);
    }

    public function borrowDetail(string $id)
    {
        $res = $this->api->get("/borrows/{$id}");

        if (($res['success'] ?? false) !== true) {
            return redirect('/borrows')->with('error', $res['message'] ?? 'Data tidak ditemukan');
        }

        return view('pages.borrow-detail', ['borrow' => (object) $res['data']]);
    }

    public function storeBorrow(Request $request)
    {
        $isStaff = in_array(Session::get('user_role'), ['Admin', 'Petugas']);
        $bookIds = $request->input('book_ids', []);

        if (empty($bookIds)) {
            return back()->with('error', 'Pilih minimal 1 buku');
        }

        $successCount = 0;
        $errors = [];

        foreach ($bookIds as $bookId) {
            $payload = ['book_id' => $bookId];
            if ($request->borrow_date) {
                $payload['borrow_date'] = $request->borrow_date;
            }
            if ($isStaff && $request->user_id) {
                $payload['user_id'] = $request->user_id;
            }

            $res = $this->api->post('/borrows', $payload);

            if ($res['success'] ?? false) {
                $successCount++;
            } else {
                $errors[] = $res['message'] ?? 'Gagal meminjam';
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return back()->with('success', "Berhasil meminjam {$successCount} buku");
        }

        if ($successCount > 0) {
            return back()->with('success', "Berhasil meminjam {$successCount} buku")->with('error', implode('. ', $errors));
        }

        return back()->with('error', implode('. ', $errors));
    }

    public function returnBorrow(string $id)
    {
        $res = $this->api->post("/borrows/{$id}/return");

        if (($res['success'] ?? false) !== true) {
            return back()->with('error', $res['message'] ?? 'Gagal mengembalikan');
        }

        $penalty = $res['data']['penalty'] ?? 0;
        $message = 'Pengembalian berhasil';
        if ($penalty > 0) {
            $message .= '. Denda: Rp' . number_format($penalty, 0, ',', '.');
        }

        return back()->with('success', $message);
    }

    public function users(Request $request)
    {
        $params = $this->filterQuery($request, ['search', 'role', 'page', 'per_page']);
        $data = $this->api->get('/users', $params);

        return view('pages.users', [
            'users' => $this->toPaginator($data, $request),
        ]);
    }

    public function storeUser(Request $request)
    {
        return $this->mutate($this->api->post('/users', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]));
    }

    public function updateUser(Request $request, string $id)
    {
        return $this->mutate($this->api->put("/users/{$id}", array_filter($request->only('name', 'email', 'role'))));
    }

    public function deleteUser(string $id)
    {
        return $this->mutate($this->api->delete("/users/{$id}"));
    }

    public function resetUserPassword(Request $request, string $id)
    {
        return $this->mutate($this->api->post("/users/{$id}/reset-password", ['password' => $request->password]));
    }

    private function authenticate(string $path, array $payload): RedirectResponse
    {
        $res = $this->api->post($path, $payload);

        if (($res['success'] ?? false) !== true) {
            return back()->with('error', $res['message'] ?? 'Terjadi kesalahan')->withInput();
        }

        Session::put('access_token', $res['data']['token']['access_token']);
        Session::put('refresh_token', $res['data']['token']['refresh_token']);
        Session::put('user_id', $res['data']['user']['id']);
        Session::put('user_name', $res['data']['user']['name']);
        Session::put('user_role', $res['data']['user']['role']);
        Session::put('user_email', $res['data']['user']['email']);

        return redirect('/');
    }

    private function mutate(array $res): RedirectResponse
    {
        if (($res['success'] ?? false) !== true) {
            $errorMsg = $res['message'] ?? 'Terjadi kesalahan';
            if (!empty($res['errors'])) {
                $errorMsg .= ': ' . collect($res['errors'])->flatten()->implode(', ');
            }
            return back()->with('error', $errorMsg)->withInput();
        }

        return back()->with('success', $res['message'] ?? 'Berhasil');
    }

    private function filterQuery(Request $request, array $keys): array
    {
        $params = [];
        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $params[$key] = $request->input($key);
            }
        }
        return $params;
    }

    private function toPaginator(array $apiResponse, Request $request)
    {
        $items = $apiResponse['data'] ?? [];
        $metadata = $apiResponse['metadata'] ?? [];

        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect($items)->map(fn($item) => (object) $item),
            $metadata['total'] ?? count($items),
            $metadata['per_page'] ?? 15,
            $metadata['current_page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
