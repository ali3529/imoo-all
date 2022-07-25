<?php


namespace App\Models;
//use Botble\RealEstate\Enums\ConsultStatusEnum;
//use Botble\Base\Traits\EnumCastable;
//use Botble\Base\Models\BaseModel;
use App\Enums\ConsultStatusEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consult extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_consults';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'content',
        'project_id',
        'property_id',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => ConsultStatusEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
