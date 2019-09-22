<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['city_id', 'membership_plan_id', 'active', 'note', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get logo attribute
     * @param $value
     * @return string
     */
    public function getLogoAttribute($value)
    {
        return $value ? $value : "";
    }
    
    /**
     * Get the city belongs to business.
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }
    
    /**
     * Get the membership plan belongs to business.
     */
    public function membershipPlan()
    {
        return $this->belongsTo('App\MembershipPlan');
    }

    /**
     * Get the stores associated with business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }

    /**
     * Get the users belongs to business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'businesses_users_roles')->withPivot('role_id', 'active')->withTimestamps();
    }
}
