<?php

namespace App\Repositories;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistRepository
{
    private $model;
    private $items;

    public function __construct(
        Checklist $model,
        ChecklistItem $item
    )
    {
        $this->model = $model;
        $this->item = $item;
    }

    public function get($params = [])
    {
        $checklist = $this->model
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

        return $checklist->paginate();
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
            $checklist = $this->model->create($data['data']['attributes']);
            foreach($data['data']['attributes']['items'] as $item){
                $this->item->create(
                    [
                        'description' => $item,
                        'checklist_id' => $checklist->id
                    ]
                );
            }
            DB::commit();
            return $checklist;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function update($request, Checklist $checklist)
    {
        DB::beginTransaction();
        try {
            $data = $request->json()->all();
            $checklist->update($data['data']['attributes']);
            DB::commit();
            return $checklist;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    public function delete(Checklist $checklist)
    {
        DB::beginTransaction();

        try {
            $this->item->where('checklist_id', $checklist->id)->delete();
            $checklist->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }
}
