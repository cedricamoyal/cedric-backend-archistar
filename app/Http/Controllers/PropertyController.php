<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Property;

class PropertyController extends Controller
{
    public function index()
    {
        return Property::all();
    }

    public function show($property_id)
    {
        return Property::with('analytics')->find($property_id);
    }

    // Get all analytics for an inputted property
    public function showAnalyticsOfProperty($property_id)
    {
        $property = Property::with('analytics')->find($property_id);

        return $property->analytics;
    }

    // Add a new property
    public function store(Request $request)
    {
        $property = Property::create($request->all());

        return response()->json($property, 201);
    }

    // Add/Update an analytic to a property
    public function updateAnalyticToProperty(Property $property, $analytic)
    {
        $property->analytics()->sync([$analytic]);
        $propertyWithAnalytics = $property->with('analytics')->get();

        return $propertyWithAnalytics;
    }

    public function suburbAnalytics ($suburb)
    {
        $properties = Property::with('analytics')->where('suburb', $suburb)->get();

        return $properties;
    }

    public function stateAnalytics ($state)
    {
        $properties = Property::with('analytics')->where('state', $state)->get();

        return $properties;
    }

    public function countryAnalytics ($country)
    {
        $properties = Property::with('analytics')->where('country', $country)->get();

        return $properties;
    }

    // public function getDataFromPropertyArray ($array)
    // {
    //     $data = array();
    //     foreach ($array as $x) {

    //     };

    //     return $data;
    // }
}
