<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['state_id', 'active', 'created_at', 'updated_at', 'deleted_at'];
    
    /**
     * Get the state belongs to city.
     */
    public function state()
    {
        return $this->belongsTo('App\State');
    }

    /**
     * Get the businesses associated with city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businesses()
    {
        return $this->hasMany('App\Business');
    }

    /**
     * Get the stores associated with city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }
}
