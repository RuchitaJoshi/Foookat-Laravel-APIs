<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'overview', 'address', 'city', 'zip_code', 'latitude', 'longitude', 'logo', 'email', 'mobile_number', 'phone_number', 'mon_open', 'mon_close', 'tue_open', 'tue_close', 'wed_open', 'wed_close', 'thu_open', 'thu_close', 'fri_open', 'fri_close', 'sat_open', 'sat_close', 'sun_open', 'sun_close', 'cover_image1', 'cover_image2', 'cover_image3', 'league_id', 'city_id', 'business_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'league_id', 'business_id', 'city_id', 'active', 'note', 'created_at', 'updated_at', 'deleted_at'];

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
     * Get email attribute
     * @param $value
     * @return string
     */
    public function getEmailAttribute($value)
    {
        return $value ? $value : "";
    }

    /**
     * Get mobile number attribute
     * @param $value
     * @return string
     */
    public function getMobileNumberAttribute($value)
    {
        return $value ? $value : "";
    }

    /**
     * Get phone number attribute
     * @param $value
     * @return string
     */
    public function getPhoneNumberAttribute($value)
    {
        return $value ? $value : "";
    }

    /**
     * Get cover image2 attribute
     * @param $value
     * @return string
     */
    public function getCoverImage2Attribute($value)
    {
        return $value ? $value : "";
    }

    /**
     * Get cover image3 attribute
     * @param $value
     * @return string
     */
    public function getCoverImage3Attribute($value)
    {
        return $value ? $value : "";
    }

    /**
     * Get the league belongs to store.
     */
    public function league()
    {
        return $this->belongsTo('App\League');
    }

    /**
     * Get the city belongs to store.
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    /**
     * Get the business belongs to store.
     */
    public function business()
    {
        return $this->belongsTo('App\Business');
    }

    /**
     * Get the deals associated with store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deals()
    {
        return $this->hasMany('App\Deal');
    }

    /**
     * Get the news associated with the store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function news()
    {
        return $this->hasMany('App\StoreNews');
    }

    /**
     * Get the average rating associated with store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function averageRating()
    {
        return $this->hasOne('App\StoreAverageRating');
    }

    /**
     * Get the users who marked this store as a favourite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favouritedBy()
    {
        return $this->belongsToMany('App\User', 'users_favourites_stores')->withTimestamps();
    }

    /**
     * Get the users who reviewed this store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reviewedBy()
    {
        return $this->belongsToMany('App\User', 'stores_reviews')->withPivot('rating', 'review')->withTimestamps();
    }
}
