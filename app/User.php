<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile_number', 'profile_picture', 'date_of_birth', 'gender', 'facebook_id', 'google_id', 'active', 'email_verification_code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'password', 'active',  'email_verification_code', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Get the verification code associated with user.
     */
    public function verificationCode()
    {
        return $this->hasOne('App\UserVerificationCode');
    }

    /**
     * Get the geolocations associated with user.
     */
    public function geolocations()
    {
        return $this->hasMany('App\UserGeolocation');
    }

    /**
     * Get the tokens associated with user.
     */
    public function tokens()
    {
        return $this->hasMany('App\UserTokens');
    }

    /**
     * Get the businesses associated with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businesses()
    {
        return $this->belongsToMany('App\Business','businesses_users_roles')->withPivot('role_id', 'active')->withTimestamps();
    }

    /**
     * Get the business inquiries associated with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businessInquiries()
    {
        return $this->hasMany('App\BusinessInquiry');
    }
    
    /**
     * Get the redeems associated with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function redeems()
    {
        return $this->belongsToMany('App\Deal','deals_redeems')->withPivot('status')->withTimestamps();
    }

    /**
     * Get the favourite deals associated with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favouriteDeals()
    {
        return $this->belongsToMany('App\Deal','users_favourites_deals')->withTimestamps();
    }

    /**
     * Get the favourite stores associated with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favouriteStores()
    {
        return $this->belongsToMany('App\Store','users_favourites_stores')->withTimestamps();
    }

    /**
     * Get the store reviews associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reviews()
    {
        return $this->belongsToMany('App\Store','stores_reviews')->withPivot('rating', 'review')->withTimestamps();
    }

    /**
     * Get the error reports of deals associated with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function errorReports()
    {
        return $this->belongsToMany('App\Deal','deals_error_reports')->withPivot('errors', 'additional_information')->withTimestamps();
    }

    /**
     * Check a user role with a business
     *
     * @param $business_id
     * @param $roleName
     * @return bool
     */
    public function is($business_id, $roleName)
    {
        $role = Role::where('name', '=', $roleName)->first();

        if ($this->businesses()->where(array('business_id' => $business_id, 'role_id' => $role->id))->wherePivot('active', '=', 1)->exists()) {
            return true;
        }

        return false;
    }
}
