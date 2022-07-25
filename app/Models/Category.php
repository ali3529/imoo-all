<?php


namespace App\Models;

use App\Enums\BaseStatusEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
//use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_categories';

    /**
     * @var array
     */
    protected $fillable
        = [
            'name',
            'description',
            'status',
            'order',
            'is_default',
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

    /**
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
