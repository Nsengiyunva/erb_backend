<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payment
 * @property string $status
 * @property string $phone_no
 * @property string $category
 * @property string $mode
 * @property string $reference
 * @property int $amount
 * @property string $notes
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        "status",
        "phone_no",
        "category",
        "mode",
        "reference"
    ];
}
