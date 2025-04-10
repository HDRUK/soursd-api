<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    use HasFactory;

    protected $table = 'system_config';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'value',
        'description',
    ];
}
