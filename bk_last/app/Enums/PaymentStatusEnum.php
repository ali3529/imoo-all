<?php

namespace App\Enums;

use Html;

/**
 * @method static PaymentStatusEnum PENDING()
 * @method static PaymentStatusEnum COMPLETED()
 * @method static PaymentStatusEnum REFUNDING()
 * @method static PaymentStatusEnum REFUNDED()
 * @method static PaymentStatusEnum FRAUD()
 * @method static PaymentStatusEnum FAILED()
 */
class PaymentStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';
    public const REFUNDING = 'refunding';
    public const REFUNDED = 'refunded';
    public const FRAUD = 'fraud';
    public const FAILED = 'failed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/payment::payment.statuses';

}
