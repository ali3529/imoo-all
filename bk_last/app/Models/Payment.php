<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Base\BaseModel;
use App\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'currency',
        'user_id',
        'charge_id',
        'payment_channel',
        'description',
        'status',
        'order_id',
        'payment_type',
        'customer_id',
        'refunded_amount',
        'refund_note',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'payment_channel' => PaymentMethodEnum::class,
        'status'          => PaymentStatusEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
//        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $time = $this->created_at->diffForHumans();

        return 'You have created a payment #' . $this->charge_id . ' via ' . $this->payment_channel->label() . ' ' . $time .
            ': ' . number_format($this->amount, 2, '.', ',') . $this->currency;
    }

    public function property()
    {
        return $this->belongsTo(Property::class,);
    }

}
