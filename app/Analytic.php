<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    protected $fillable = [
        'name',
        'units',
        'is_numeric',
        'num_decimal_places',
    ];

    public function properties()
    {
        return $this->belongsToMany('App\Property', 'property_analytics', 'analytic_type_id', 'property_id');
    }

    protected $table = 'analytic_types';
}
