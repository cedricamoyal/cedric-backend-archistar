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

    public function show(Property $property)
    {
        $item = $property->with('analytics')->first();

        return $item;
    }

    // Get all analytics for an inputted property
    public function showAnalyticsOfProperty(Property $property)
    {
        $item = $property->with('analytics')->first();

        return $item->analytics;
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
        $propertyWithAnalytics = $property->with('analytics')->first();

        return $propertyWithAnalytics;
    }
}
