<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffFile extends Model
{
    protected $table = 'nec_staff_files';

    protected $fillable = [
        "file_name",
        "reference_id",
        "slug",
        "name",
        "size"
    ];
}
