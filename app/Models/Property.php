<?php


namespace App\Models;

//use Botble\Base\Models\BaseModel;
//use Botble\Base\Traits\EnumCastable;
//use Botble\RealEstate\Enums\ModerationStatusEnum;
//use Botble\Location\Models\City;
//use Botble\RealEstate\Enums\PropertyPeriodEnum;
//use Botble\RealEstate\Enums\PropertyStatusEnum;
//use Botble\RealEstate\Enums\PropertyTypeEnum;
use App\Enums\ModerationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PropertyPeriodEnum;
use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
use Exception;
use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Property extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_properties';

    /**
     * @var array
     */
    protected $fillable
        = [
            'name',
            'package',
            'type',
            'description',
            'content',
            'location',
            'images',
            'project_id',
            'number_bedroom',
            /// Edited v2
            'videos',
            'post_code',
            'house_number',
            'house_name',
            'gross_rent',
            'additional_costs',
            'net_rent',
            'garage',
            'parking_space',
            'contact_name',
            'contact_phone_number',
            'last_renovation',
            'last_reconstruction',
            'distance',
            /// End Edit V2

            //////// Edit v.1
            'number_wc',
            'construction_year',
            'available_date',
            ////// End Edited
            'number_bathroom',
            'number_floor',
            'square',
            'price',
            'status',
            'is_featured',
            'is_featured_vip',
            'currency_id',
            'city_id',
            'period',
            'author_id',
            'author_type',
            'category_id',
            'expire_date',
            'auto_renew',
            'longitude',
             'latitude',


            ///
            'neworedit',
            'advertising_type'
        ];

    /**
     * @var array
     */
    protected $casts
        = [
            'status'            => PropertyStatusEnum::class,
            'moderation_status' => ModerationStatusEnum::class,
            'type'              => PropertyTypeEnum::class,
            'period'            => PropertyPeriodEnum::class,
        ];

    /**
     * @var array
     */
    protected $dates
        = [
            'created_at',
            'updated_at',
            'expire_date',
        ];

    protected $appends
        = ['payed'];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 're_property_features',
            'property_id', 'feature_id');
    }

    /** 
     * @return BelongsToMany
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 're_facilities_distances',
            'reference_id')->withPivot('distance', 'reference_type');
    }

    /**
     * @param  string  $value
     *
     * @return array
     */
    public function getImagesAttribute($value)
    {
        try {
            if ($value === '[null]') {
                return [];
            }

            return json_decode($value) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param  string  $value
     *
     * @return array
     */
    public function getVideosAttribute($value)
    {
        try {
            if ($value === '[null]') {
                return [];
            }

            return json_decode($value) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @return string|null
     */
    public function getImageAttribute(): ?string
    {
        return Arr::first($this->images) ?? null;
    }

    /**
     * @return string
     */
    public function getSquareTextAttribute()
    {
        return $this->square.' '.setting('real_estate_square_unit', 'm²');
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    /**
     * @return MorphTo
     */
    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault();
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->where('expire_date', '>=', now()->toDateTimeString())
                ->orWhere('never_expired', true);
        });
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($query) {
            $query->where('expire_date', '<', now()->toDateTimeString())
                ->where('never_expired', false);
        });
    }

    public function makeReference(): bool
    {
        do {
            $code = Helper::bothify('?????.##??.??.##.#####');
        } while (Property::where('reference', '=', $code)->count());

        return $this->forceFill([
            'reference' => $code,
        ])->save();
    }

    /**
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package')->with('currency');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPayedAttribute()
    {
        return $this->payments()->get()
            ->where('status', '=', PaymentStatusEnum::COMPLETED)
            ->count();
    }
}
