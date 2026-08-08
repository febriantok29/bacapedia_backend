<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Support\ApiMessages;
use App\Support\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Category::query();

        if ($user->role === UserRole::ADMIN->value && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($categories);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Category::withCount('books');

        if ($user->role === UserRole::ADMIN->value && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $category = $query->find($id);

        if (!$category) {
            return ApiResponse::notFound(ApiMessages::CATEGORY_NOT_FOUND);
        }

        return ApiResponse::success($category);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== UserRole::ADMIN->value) {
            return ApiResponse::forbidden();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $category = Category::create([
            'name' => $request->name,
        ]);

        $category->created_by = $user->id;
        $category->save();

        return ApiResponse::success($category, ApiMessages::DATA_CREATED, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== UserRole::ADMIN->value) {
            return ApiResponse::forbidden();
        }

        $category = Category::find($id);

        if (!$category) {
            return ApiResponse::notFound(ApiMessages::CATEGORY_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $category->name = $request->name;
        $category->updated_by = $user->id;
        $category->save();

        return ApiResponse::success($category, ApiMessages::DATA_UPDATED);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== UserRole::ADMIN->value) {
            return ApiResponse::forbidden();
        }

        $category = Category::find($id);

        if (!$category) {
            return ApiResponse::notFound(ApiMessages::CATEGORY_NOT_FOUND);
        }

        $category->deleted_by = $user->id;
        $category->save();
        $category->delete();

        return ApiResponse::success(null, ApiMessages::DATA_DELETED);
    }
}
