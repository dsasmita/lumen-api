<?php

namespace App\Http\Transformers;

use App\Models\ChecklistTemplate;
use League\Fractal\TransformerAbstract;

class ChecklistTemplateTransformer extends TransformerAbstract
{

    public $type = 'checklist-template';

    public function transform(ChecklistTemplate $checklistTemplate)
    {
        $transform = [
            'id' => $checklistTemplate->id,
            'type' => 'checklist-template',
            'attributes' => [
                'name' => $checklistTemplate->name,
                'checklist' => $checklistTemplate->checklist ? $checklistTemplate->checklist : null,
                'items' => $checklistTemplate->checklist ? $checklistTemplate->checklist->items : null,
                'created_at' => $checklistTemplate->created_at,
                'update_at' => $checklistTemplate->update_at,
                'created_by' => $checklistTemplate->created_by,
                'last_update_by' => $checklistTemplate->updated_by
            ],
            "links" => [
                "self" => route('checklists.templates.detail', ['template_id' => $checklistTemplate->id])
            ]
        ];
        return $transform;
    }
}
