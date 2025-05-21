<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Completed = 'completed';
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Canceled = 'canceled';
}
