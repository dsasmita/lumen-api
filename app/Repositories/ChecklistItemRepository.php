<?php

namespace App\Repositories;

use Auth;
use App\Models\History;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistItemRepository
{
    private $model;
    private $checklist;

    public function __construct(
        ChecklistItem $model,
        Checklist $checklist
    )
    {
        $this->model = $model;
        $this->checklist = $checklist;
    }

    public function get($params = [])
    {
        $item = $this->model
            ->when(! empty($params['query']), function($query) use($params){
                $conditions = explode('|', $params['query']);
                if(count($conditions)){
                    foreach($conditions as $condition){
                        $rule = explode(':',$condition);
                        if(count($rule) == 2){
                            $query->where($rule[0], $rule[1]);
                        }elseif(count($rule) == 3){
                            if($rule[1] == 'like'){
                                $query->where($rule[0], 'like', '%'.$rule[2].'%');
                            }else{
                                $query->where($rule[0], $rule[1], $rule[2]);
                            }
                        }
                    }
                }
                return $query;
            })
            ->when(! empty($params['with']), function ($query) use ($params) {
                return $query->with($params['with']);
            })
            ->when(! empty($params['order']), function ($query) use ($params) {
                return $query->orderByRaw($params['order']);
            });

        return $item->paginate();
    }

    public function findByColumn($column, $value)
    {
        $model = $this->model->where($column, $value)->first();

        if (! $model) {
            return false;
        }

        return $model;
    }

    public function store(Request $request, $checklist_id)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $item = $this->model->create(
                [
                    'description' => $data['data']['attribute']['description'],
                    'checklist_id' => $checklist_id,
                    'due' => $data['data']['attribute']['due'],
                    'urgency' => $data['data']['attribute']['urgency'],
                    'assignee_id' => $data['data']['attribute']['assignee_id'],
                ]
            );
            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function update($request, ChecklistItem $checklistItem)
    {
        DB::beginTransaction();
        try {
            $data = $request->json()->all();
            $checklistItem->update($data['data']['attribute']);
            DB::commit();
            return $checklistItem;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function delete(ChecklistItem $checklistItem)
    {
        DB::beginTransaction();

        try {
            $checklistItem->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function complete($request)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $result = [];
            foreach($data['data'] as $item){
                $itemComplete = $this->model->where('id', $item['item_id'])->first();
                if($itemComplete){
                    $itemComplete->is_completed = true;
                    $itemComplete->completed_at = date('Y-m-d H:i:s');
                    $itemComplete->save();

                    $history = new History();
                    $history->create([
                        'loggable_type' => History::TYPE_ITEM, 
                        'loggable_id' => $itemComplete->id,
                        'action' => History::ACTION_ITEM_COMPLETE,
                        'kwuid' => Auth::user()->id,
                        'value' => true
                    ]);

                    $result[] = [
                            "id" => $itemComplete->id,
                            "item_id" => $itemComplete->id,
                            "is_completed" => $itemComplete->is_completed,
                            "checklist_id" => $itemComplete->checklist_id
                        ];
                }
            }
            DB::commit();
            return ['data' => $result];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function incomplete($request)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $result = [];
            foreach($data['data'] as $item){
                $itemComplete = $this->model->where('id', $item['item_id'])->first();
                if($itemComplete){
                    $itemComplete->is_completed = false;
                    $itemComplete->save();

                    $history = new History();
                    $history->create([
                        'loggable_type' => History::TYPE_ITEM, 
                        'loggable_id' => $itemComplete->id,
                        'action' => History::ACTION_ITEM_INCOMPLETE,
                        'kwuid' => Auth::user()->id,
                        'value' => false
                    ]);

                    $result[] = [
                            "id" => $itemComplete->id,
                            "item_id" => $itemComplete->id,
                            "is_completed" => $itemComplete->is_completed,
                            "checklist_id" => $itemComplete->checklist_id
                        ];
                }
            }
            DB::commit();
            return ['data' => $result];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function bulkUpdate($request, $checklist)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $result = [];
            foreach($data['data'] as $itemData){
                $item = $this->model->where(
                    [
                        'id' => $itemData['id'],
                        'checklist_id' => $checklist->id
                    ]
                    )->first();
                if($item){
                    $item->update($itemData['attributes']);

                    $result[] = [
                            "id" => $itemData['id'],
                            "action" => 'update',
                            "status" => 200
                        ];
                }else{
                    $result[] = [
                        "id" => $itemData['id'],
                        "action" => 'update',
                        "status" => 404
                    ];
                }
            }
            DB::commit();
            return ['data' => $result];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function summaries()
    {
        return [
                'data' => [
                    "today" => 0,
                    "past_due" => 0,
                    "this_week" => 2,
                    "past_week" => 0,
                    "this_month" => 2,
                    "past_month" => 0,
                    "total" => 2
                ]
            ];
    }
}
