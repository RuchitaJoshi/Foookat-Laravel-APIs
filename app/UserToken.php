<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserToken extends Model
{
    protected $table = "users_tokens";

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'token', 'endpoint', 'device_type'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the user belongs to token.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
