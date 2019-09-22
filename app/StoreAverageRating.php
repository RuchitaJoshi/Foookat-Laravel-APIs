<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreAverageRating extends Model
{
    protected $table = 'stores_average_ratings';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'store_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the store belongs to average ratings.
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
    }
}
