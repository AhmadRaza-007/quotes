<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    // This model is no longer used. It remains for backward compatibility and will be removed.
    use HasFactory;

    protected $table = 'quotes_deprecated';
}
