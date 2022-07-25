<?php


namespace App\Models;
//use Botble\Base\Models\BaseModel;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Currency extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_currencies';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'symbol',
        'is_prefix_symbol',
        'order',
        'decimals',
        'is_default',
        'exchange_rate',
    ];
}
