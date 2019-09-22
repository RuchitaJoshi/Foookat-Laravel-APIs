<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class League extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the stores associated with league.
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }
}
