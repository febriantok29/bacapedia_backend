<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Config;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Config::query();

        if ($request->has('key')) {
            $query->where('key', $request->key);
        }

        $configs = $query->orderBy('key')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($configs);
    }

    public function show(string $id): JsonResponse
    {
        $config = Config::find($id);

        if (!$config) {
            return ApiResponse::notFound('Konfigurasi tidak ditemukan');
        }

        return ApiResponse::success($config);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:64',
            'active_start_date' => 'nullable|date',
            'active_end_date' => 'nullable|date|after_or_equal:active_start_date',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $config = Config::create([
            'key' => $request->key,
            'value' => $request->value,
            'active_start_date' => $request->active_start_date,
            'active_end_date' => $request->active_end_date,
        ]);

        $config->created_by = $user->id;
        $config->save();

        return ApiResponse::success($config, ApiMessages::DATA_CREATED, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $config = Config::find($id);

        if (!$config) {
            return ApiResponse::notFound('Konfigurasi tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'key' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|string|max:64',
            'active_start_date' => 'nullable|date',
            'active_end_date' => 'nullable|date|after_or_equal:active_start_date',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $config->fill($request->only(['key', 'value', 'active_start_date', 'active_end_date']));
        $config->updated_by = $user->id;
        $config->save();

        return ApiResponse::success($config, ApiMessages::DATA_UPDATED);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $config = Config::find($id);

        if (!$config) {
            return ApiResponse::notFound('Konfigurasi tidak ditemukan');
        }

        $config->deleted_by = $user->id;
        $config->save();
        $config->delete();

        return ApiResponse::success(null, ApiMessages::DATA_DELETED);
    }
}
