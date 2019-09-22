<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    protected $table = 'membership_plans';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the businesses associated with membership plan.
     */
    public function businesses()
    {
        return $this->hasMany('App\Business');
    }
}
