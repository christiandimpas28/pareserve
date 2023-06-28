<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING            = 'Pending';
    case CHECKOUT           = 'Checkout';
    case PAYMENT_SUBMITTED  = 'Payment Submitted';
    case BOOKED             = 'Booked';
    case CANCELLED          = 'Cancelled';
    case COMPLETED          = 'Completed';
}