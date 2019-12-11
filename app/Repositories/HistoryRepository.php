<?php

namespace App\Repositories;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryRepository
{
    private $model;

    public function __construct(
        History $model
    )
    {
        $this->model = $model;
    }

    public function get($params = [])
    {
        $history = $this->model
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

        return $history->paginate();
    }

    public function findByColumn($column, $value)
    {
        $model = $this->model->where($column, $value)->first();

        if (! $model) {
            return false;
        }

        return $model;
    }
}
