<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessInquiry extends Model
{
    protected $table = "businesses_inquiries";

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'city', 'zip_code', 'contact_number', 'user_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the user belongs to business inquiry.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
