<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;

class History extends Model
{

    protected $table = 'histories';

    const TYPE_ITEM = 'items';
    
    const ACTION_ITEM_ASSIGN = 'assign';
    const ACTION_ITEM_COMPLETE = 'complete';
    const ACTION_ITEM_INCOMPLETE = 'incomplete';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loggable_type', 'loggable_id', 'action', 'kwuid', 'value'
    ];
}
