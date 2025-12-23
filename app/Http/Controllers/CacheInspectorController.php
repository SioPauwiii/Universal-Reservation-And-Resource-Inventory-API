<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Services\CacheInspectorService;

class CacheInspectorController extends Controller
{
    public function inspect(string $key, CacheInspectorService $inspector)
    {
        $result = $inspector->inspect($key);

        return response()->json($result);
    }
}
