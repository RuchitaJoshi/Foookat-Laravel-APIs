<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['commission' ,'active', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'cover_image1'  => 'boolean',
        'cover_image2'  => 'boolean',
        'cover_image3'  => 'boolean'
    ];
    
    /**
     * Get the deals associated with category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deals()
    {
        return $this->hasMany('App\Deal');
    }
}
