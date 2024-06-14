<?php

// app/Http/Controllers/VisualizationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Rating;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel; // Assuming you have a Rating model for saving ratings

class VisualizationController extends Controller
{
    public function index()

    {
        $path = storage_path('app/public/final.json');
        $data = json_decode(File::get($path), true);

        return view('visualization', compact('data'));
    }

    public function getData(Request $request, $id)
    {
        $path = storage_path('app/public/final.json');
        $data = json_decode(File::get($path), true);

        foreach ($data as $article) {
            if ($article['id'] == $id) {
                // $find = Rating::where('id',$article['id'])->where('ip', $request->getClientIp())->first();
                // if(!empty($find)){
                //      $status = true;
                // }else{
                //     $status = false;
                // }
                if (array_key_exists('gpt_table_para_pair_agent', $article)) {

                    return response()->json([
                        'gpt_table_para_pair_agent' => $article['gpt_table_para_pair_agent'],
                        'title' => $article['article_title'],
                        'article_ids' => $article['article_ids'],
                        // 'status' => $status
                        // 'gpt_table_para_pair_noagent' => $article['gpt_table_para_pair_noagent']
                    ]);
                }else{
                    return response()->json([
                        // 'gpt_table_para_pair_agent' => $article['gpt_table_para_pair_agent'],
                         'gpt_table_para_pair_noagent' => $article['gpt_table_para_pair_noagent'],
                         'title' => $article['article_title'],
                         'article_ids' => $article['article_ids'],
                        //  'status' => $status
                    ]);
                }

            }
        }
        return response()->json(['error' => 'Data not found'], 404);
    }

    public function saveRating(Request $request)
    {

        // dump($request->all());

        $request->validate([
            'article_id' => 'required',
            'graph_index' => 'required|string',
            'ratings' => 'required|array',
            'ratings.*' => 'integer|between:1,5' // Using between rule instead of min and max
        ]);
        $ratings = json_decode(json_encode($request->ratings), true); // Convert to array

        $ratingsModel = new Rating();
        $ratingsModel->json_id = $request->article_id;
        $ratingsModel->article_id = $request->article_ids;
        $ratingsModel->article_title = $request->title;
        $ratingsModel->type = $request->graph_index;
        $ratingsModel->relevance = $ratings[0];
        $ratingsModel->clarity_and_coherence = $ratings[1];
        $ratingsModel->visualization_quality = $ratings[2];
        $ratingsModel->narrative_quality = $ratings[3];
        $ratingsModel->factual_correctness = $ratings[4];
        $ratingsModel->ip = $request->getClientIp();
        // $ratingsModel->ratings = json_encode($request->ratings);
        $ratingsModel->save();


        return response()->json(['success' => 'Rating saved successfully']);
    }

    public function exportCsv()
    {
        return (new FastExcel(Rating::select(
            // 'id',
            // 'json_id',
            'article_id',
            'article_title',
            'type',
            'relevance',
            'clarity_and_coherence',
            'visualization_quality',
            'narrative_quality',
            'factual_correctness',
            // 'ip',
            // 'created_at',
            // 'updated_at'
        )->orderBy('article_id', 'asc')->get()))->download('ratings-'.Carbon::now()->format('Y-m-d').'.csv');
    }
}

