<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;

class ChecklistItem extends Model
{

    protected $table = 'checklist_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'checklist_id', 'description', 'is_completed', 'completed_at', 
        'due', 'urgency' , 'created_by', 'updated_by', 'assignee_id'
    ];

    public function setCompletedAtAttribute($value)
    {
        $this->attributes['completed_at'] = Carbon::parse($value);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if(Auth::user()){
                $p->created_by = Auth::user()->id;
            }
            // assign
            if($p->assignee_id != null){
                $history = new History();
                $history->create([
                    'loggable_type' => History::TYPE_ITEM, 
                    'loggable_id' => $p->id,
                    'action' => History::ACTION_ITEM_ASSIGN,
                    'kwuid' => Auth::user() ? Auth::user()->id : 0,
                    'value' => $p->assignee_id
                ]);
            }
        });

        static::updating(function ($p) {
            if(Auth::user()){
                $p->updated_by = Auth::user()->id;
            }
            // assign
            if($p->assignee_id != null){
                $history = new History();
                $history->create([
                    'loggable_type' => History::TYPE_ITEM, 
                    'loggable_id' => $p->id,
                    'action' => History::ACTION_ITEM_ASSIGN,
                    'kwuid' => Auth::user() ? Auth::user()->id : 0,
                    'value' => $p->assignee_id
                ]);
            }
        });
    }
}
