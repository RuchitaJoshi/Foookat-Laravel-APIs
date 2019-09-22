<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'details', 'overview', 'original_price', 'percentage_off', 'amount_off', 'new_price', 'start_date', 'end_date', 'start_time', 'end_time', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'redeem_code', 'used_once', 'cover_image1', 'cover_image2', 'cover_image3', 'category_id', 'store_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'store_id', 'category_id', 'active', 'note', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'used_once' => 'boolean',
        'mon' => 'boolean',
        'tue' => 'boolean',
        'wed' => 'boolean',
        'thu' => 'boolean',
        'fri' => 'boolean',
        'sat' => 'boolean',
        'sun' => 'boolean'
    ];

    /**
     * Get original price attribute
     * @param $value
     * @return string
     */
    public function getOriginalPriceAttribute($value)
    {
        return $value ? $value : 0;
    }

    /**
     * Get percentage off attribute
     * @param $value
     * @return string
     */
    public function getPercentageOffAttribute($value)
    {
        return $value ? $value : 0;
    }

    /**
     * Get amount off attribute
     * @param $value
     * @return string
     */
    public function getAmountOffAttribute($value)
    {
        return $value ? $value : 0;
    }

    /**
     * Get new price attribute
     * @param $value
     * @return string
     */
    public function getNewPriceAttribute($value)
    {
        return $value ? $value : 0;
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
     * Get the category belongs to deal.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * Get the store belongs to deal.
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    /**
     * Get the users who redeemed this deal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function redeemedBy()
    {
        return $this->belongsToMany('App\User', 'deals_redeems')->withPivot('status')->withTimestamps();
    }

    /**
     * Get the users who marked this deal as a favourite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favouritedBy()
    {
        return $this->belongsToMany('App\User', 'users_favourites_deals')->withTimestamps();
    }

    /**
     * Get the users who reported errors of this deal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function errorReportedBy()
    {
        return $this->belongsToMany('App\Deal', 'deals_error_reports')->withPivot('errors', 'additional_information')->withTimestamps();
    }
}
