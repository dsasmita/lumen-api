<?php

namespace App\Http\Transformers;

use App\Models\ChecklistItem;
use League\Fractal\TransformerAbstract;

class ChecklistItemTransformer extends TransformerAbstract
{

    public $type = 'checklist';

    public function transform(ChecklistItem $checklistItem)
    {
        return [
            'id' => $checklistItem->id,
            'type' => 'checklist-item',
            'attributes' => [
                'description' => $checklistItem->description,
                'checklist_id' => $checklistItem->checklist_id,
                'is_completed' => $checklistItem->is_completed,
                'completed_at' => $checklistItem->completed_at, 
                'due' => $checklistItem->due,
                'urgency' => $checklistItem->urgency,
                'created_by' => $checklistItem->created_by,
                'updated_by' => $checklistItem->updated_by
            ],
            "links" => [
                "self" => route('checklists.items.get', ['checklist_id' => $checklistItem->checklist_id])
            ]
        ];
    }
}
