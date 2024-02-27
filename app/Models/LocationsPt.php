<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationsPt extends Model
{
    protected $table    = 'locations_pt';
    public $timestamps  = false;
    protected $fillable = [
        'id',
        'district_code',
        'district_name',
        'municipality_code',
        'municipality_name',
        'parish_code',
        'parish_name',
        'population',
        'rural',
        'coastal'
    ];
}
