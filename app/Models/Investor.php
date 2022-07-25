<?php


namespace App\Models;
//use Botble\Base\Traits\EnumCastable;
//use Botble\Base\Enums\BaseStatusEnum;
//use Botble\Base\Models\BaseModel;

use App\Enums\BaseStatusEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Model;

class Investor extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_investors';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
