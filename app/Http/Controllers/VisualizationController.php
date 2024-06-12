<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VisualizationController extends Controller
{
    public function index()
    {
        return view('visualization');
    }

    public function getData($id, $key)
    {
        $path = storage_path('app/public/gpt_data.json');
        $data = json_decode(File::get($path), true);

        foreach ($data as $article) {
            if ($article['article_ids'] == $id) {
                return response()->json($article[$key]);
            }
        }
        return response()->json(['error' => 'Data not found'], 404);
    }
}
