<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiences';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'from',
        'to',
        'organisation_id',
    ];
}
