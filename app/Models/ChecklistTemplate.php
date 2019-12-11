<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ChecklistTemplate extends Model
{

    protected $table = 'checklist_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'checklist_id', 'created_by', 'updated_by'
    ];

    public function checklist(){
        return $this->belongsTo('App\Models\Checklist');
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
