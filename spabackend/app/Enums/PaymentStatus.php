<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING            = 'Pending Payment';
    case AWAITING           = 'Awaiting Payment';
    case DECLINED           = 'Payment Declined';
    case ABANDONED          = 'Payment Abandoned';
    case CANCELLED          = 'Payment Cancelled';
    case COMPLETED          = 'Payment Completed';
}