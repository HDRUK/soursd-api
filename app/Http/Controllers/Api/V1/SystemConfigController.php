<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $systemConfig = SystemConfig::all();

        return response()->json([
            'message' => 'success',
            'data' => $systemConfig,
        ], 200);
    }

    public function getByName(Request $request, string $name): JsonResponse
    {
        try {
            $systemConfig = SystemConfig::where('name', $name)->first();

            return response()->json([
                'message' => 'success',
                'data' => $systemConfig,
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $exists = SystemConfig::where('name', $input['name'])->first();
        if (! $exists) {
            $systemConfig = SystemConfig::create([
                'name' => $input['name'],
                'value' => $input['value'],
                'description' => $input['description'],
            ]);

            return response()->json([
                'message' => 'success',
                'data' => $systemConfig->id,
            ], 201);
        }

        return response()->json([
            'message' => 'Configuration already exists',
            'data' => null,
        ], 400);
    }
}
