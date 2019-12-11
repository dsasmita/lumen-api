<?php

namespace App\Http\Transformers;

use App\Models\Checklist;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\ChecklistItemTransformer;

class ChecklistTransformer extends TransformerAbstract
{

    public $type = 'checklist';

    public $includeItems;

    protected $availableIncludes = [
        'items'
    ];

    public function transform(Checklist $checklist)
    {
        $transform = [
            'id' => $checklist->id,
            'type' => 'checklist',
            'attributes' => [
                'object_domain' => $checklist->object_domain,
                'object_id' => $checklist->object_id,
                'description' => $checklist->description,
                'is_completed' => $checklist->is_completed,
                'completed_at' => $checklist->completed_at,
                'due' => $checklist->due,
                'urgency' => $checklist->urgency,
                'created_at' => $checklist->created_at,
                'update_at' => $checklist->update_at,
                'created_by' => $checklist->created_by,
                'last_update_by' => $checklist->updated_by
            ],
            "links" => [
                "self" => route('checklists.detail', ['checklist_id' => $checklist->id])
            ]
        ];
        if($this->includeItems){
            $transform['attributes']['items'] = $checklist->items;
        }
        return $transform;
    }

    public function includeItems(Checklist $checklist)
    {
        return $this->collection($checklist->items, new ChecklistItemTransformer, false);
    }
}
