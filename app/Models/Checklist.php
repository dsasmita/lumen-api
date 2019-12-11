<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;

class Checklist extends Model
{

    protected $table = 'checklists';

    public $includeItems = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_id', 'description', 'object_domain', 'is_completed', 'completed_at', 
        'due_interval', 'due_unit',
        'due', 'urgency' , 'created_by', 'updated_by'
    ];

    public function setDueAttribute($value)
    {
        $this->attributes['due'] = Carbon::parse($value);
    }

    public function setCompletedAtAttribute($value)
    {
        $this->attributes['completed_at'] = Carbon::parse($value);
    }

    public function items(){
        return $this->hasMany(ChecklistItem::class,'checklist_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if(Auth::user()){
                $p->created_by = Auth::user()->id;
            }
        });

        static::updating(function ($p) {
            if(Auth::user()){
                $p->updated_by = Auth::user()->id;
            }
        });
    }
}
