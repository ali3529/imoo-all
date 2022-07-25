<?php


namespace App\Models;

//use Botble\Base\Traits\EnumCastable;
//use Botble\Base\Enums\BaseStatusEnum;
//use Botble\Base\Models\BaseModel;
use App\Enums\BaseStatusEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * @var array
     */
    protected $fillable
        = [
            'name',
            'state_id',
            'country_id',
            'order',
            'is_default',
            'is_featured',
            'image',
            'status',
            'slug',
            'record_id',
        ];

    /**
     * @var array
     */
    protected $casts
        = [
        'status' => BaseStatusEnum::class,
        ];

    protected static function booted()
    {
        static::addGlobalScope('is_published', function (Builder $builder) {
            $builder->where('status', '=', 'published')
                ->orderBy('order', 'asc');
        });
    }

    public function scopeOfCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
