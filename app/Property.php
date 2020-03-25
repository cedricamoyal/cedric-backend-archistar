<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'suburb',
        'state',
        'country',
    ];

    public function analytics()
    {
        return $this->belongsToMany('App\Analytic', 'property_analytics', 'property_id', 'analytic_type_id');
    }
}
