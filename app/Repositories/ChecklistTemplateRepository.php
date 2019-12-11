<?php

namespace App\Repositories;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\ChecklistTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistTemplateRepository
{
    private $model;
    private $checklist;
    private $items;

    public function __construct(
        Checklist $checklist,
        ChecklistItem $item,
        ChecklistTemplate $model
    )
    {
        $this->model = $model;
        $this->checklist = $checklist;
        $this->item = $item;
    }

    public function get($params = [])
    {
        $template = $this->model
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

        return $template->paginate();
    }

    public function findByColumn($column, $value)
    {
        $model = $this->model->where($column, $value)->first();

        if (! $model) {
            return false;
        }

        return $model;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $checklist = $this->checklist->create($data['data']['attributes']['checklist']);
            if($checklist){
                $data['data']['attributes']['checklist_id'] = $checklist->id;
                $checklistTemplate = $this->model->create($data['data']['attributes']);
                foreach($data['data']['attributes']['items'] as $item){
                    $item['checklist_id'] = $checklist->id;
                    $this->item->create(
                        $item
                    );
                }
            }else{
                return false;
            }
            DB::commit();
            return $checklistTemplate;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function update($request, ChecklistTemplate $template)
    {
        DB::beginTransaction();
        try {
            $data = $request->json()->all();
            $template->update($data['data']);
            $checklist = $template->checklist->first();
            if($checklist){
                $checklist->update($data['data']['checklist']);
                if(isset($data['data']['items'])){
                    $items = $checklist->items()->delete();
                    foreach($data['data']['items'] as $item){
                        $item['checklist_id'] = $checklist->id;
                        $this->item->create(
                            $item
                        );
                    }
                }
            }
            DB::commit();
            return $template;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function delete(ChecklistTemplate $template)
    {
        DB::beginTransaction();

        try {
            $checklist = $template->checklist->first();
            if($checklist){
                $items = $checklist->items()->delete();
                $checklist->delete();
            }
            $template->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function assigns($request, $template)
    {
        DB::beginTransaction();

        try {
            $data = $request->json()->all();
            $result = [];
            $result['data'] = [];
            $result['included'] = [];
            $checklist = $template->checklist->first();
            foreach($data['data'] as $object){
                $checklist->update($object['attributes']);
                $result['data'][] = [
                    'attributes' => $checklist->first(),
                    'links' => route('checklists.detail',['checklist_id' => $checklist->id]),
                    'relationships' => [
                        'items' => [
                            'link' => [
                                'related' => route('checklists.items.list', ['checklist_id' => $checklist->id])
                            ],
                            'data' => $checklist->items
                        ]
                    ]
                ];
                foreach($checklist->items()->get() as $item){
                    $result['included'][] = $item;
                }
            }
            $result['meta']['count'] = count($data['data']);
            $result['meta']['total'] = count($data['data']);

            return $result;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }
}
