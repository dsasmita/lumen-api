<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Repositories\HistoryRepository;
use App\Http\Transformers\HistoryTransformer;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    private $historyRepository;

    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function index(Request $request)
    {
        $query = $request->only(['pagination', 'query']);
        $query = array_merge($query);

        $histories = $this->historyRepository->get(
            $query
        );

        return $this->paginator($histories, new HistoryTransformer());
    }

    public function detailAction($history_id)
    {
        $history = $this->historyRepository->findByColumn('id', $history_id);
        if(!$history){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        return $this->item($history, new HistoryTransformer);
    }
}
