<?php


namespace App\Models;

//use Botble\Base\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class VerifyMobile extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_verify_mobiles';

    /**
     * @var array
     */
    protected $fillable
        = [
            'user_id',
            'mobile',
            'expire_date',
            'code'
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden
        = [
            'code',
            'user_id',
        ];

    /**
     * @var array
     */
    protected $dates
        = [
            'expire_date',
        ];
}
