<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVerificationCode extends Model
{
    protected $table = "users_verification_codes";

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'mobile_number', 'verification_code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the user belongs to verification code.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
