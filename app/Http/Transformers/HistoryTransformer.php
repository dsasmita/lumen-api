<?php

namespace App\Http\Transformers;

use App\Models\History;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\ChecklistItemTransformer;

class HistoryTransformer extends TransformerAbstract
{

    public $type = 'history';

    public function transform(History $history)
    {
        $transform = [
            'id' => $history->id,
            'type' => 'history',
            'attributes' => [
                $history
            ],
            "links" => [
                "self" => route('checklists.history.detail', ['history_id' => $history->id])
            ]
        ];
        return $transform;
    }
}
