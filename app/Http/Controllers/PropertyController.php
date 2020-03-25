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

        $arraysOfListOfValuesPerSuburbPerType = $this->getListOfValuesPerSuburbPerType($properties);

        $arraysOfMinMaxMedianValues = $this->getSummaryOfAllPropertyAnalytics($arraysOfListOfValuesPerSuburbPerType);

        return $arraysOfMinMaxMedianValues;
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

    public function getListOfValuesPerSuburbPerType ($array)
    {
        $data = array(
            "max_Bld_Height_m" => array(),
            "min_lot_size_m2" => array(),
            "fsr" => array(),
            "number of properties" => count($array)
        );

        foreach ($array as $property) {
            foreach ($property->analytics as $analytic) {
                if ($analytic->pivot->analytic_type_id === 1) {
                    array_push($data["max_Bld_Height_m"], $analytic->pivot->value);
                } else if ($analytic->pivot->analytic_type_id === 2) {
                    array_push($data["min_lot_size_m2"], $analytic->pivot->value);
                } else if ($analytic->pivot->analytic_type_id === 2) {
                    array_push($data["fsr"], $analytic->pivot->value);
                }
            };
        };

        return $data;
    }
    public function getSummaryOfAllPropertyAnalytics ($array)
    {
        $data = array(
            "max_Bld_Height_m" => array(
                "min value" => count($array["max_Bld_Height_m"]) ? min($array["max_Bld_Height_m"]) : 0,
                "max value" => count($array["max_Bld_Height_m"]) ? max($array["max_Bld_Height_m"]) : 0,
                "median value" => count($array["max_Bld_Height_m"]) ? $this->median($array["max_Bld_Height_m"]) : 0,
                "percentage properties with a value" =>
                    (count($array["max_Bld_Height_m"]) && $array["number of properties"]) ? count($array["max_Bld_Height_m"])/$array["number of properties"]*100 : "No data",
                "percentage properties without a value" =>
                    (count($array["max_Bld_Height_m"]) && $array["number of properties"]) ? 100 - (count($array["max_Bld_Height_m"])/$array["number of properties"]*100) : "No data",
            ),
            "min_lot_size_m2" => array(
                "min value" => count($array["min_lot_size_m2"]) ? min($array["min_lot_size_m2"]) : 0,
                "max value" => count($array["min_lot_size_m2"]) ? max($array["min_lot_size_m2"]) : 0,
                "median value" => count($array["min_lot_size_m2"]) ? $this->median($array["min_lot_size_m2"]) : 0,
                "percentage properties with a value" =>
                    (count($array["min_lot_size_m2"]) && $array["number of properties"]) ? count($array["min_lot_size_m2"])/$array["number of properties"]*100 : "No data",
                "percentage properties without a value" =>
                    (count($array["min_lot_size_m2"]) && $array["number of properties"]) ? 100 - (count($array["min_lot_size_m2"])/$array["number of properties"]*100) : "No data",
            ),
            "fsr" => array(
                "min value" => count($array["fsr"]) ? min($array["fsr"]) : 0,
                "max value" => count($array["fsr"]) ? max($array["fsr"]) : 0,
                "median value" => count($array["fsr"]) ? $this->median($array["fsr"]) : 0,
                "percentage properties with a value" =>
                    (count($array["fsr"]) && $array["number of properties"]) ? count($array["fsr"])/$array["number of properties"]*100 : "No data",
                "percentage properties without a value" =>
                    (count($array["fsr"]) && $array["number of properties"]) ? 100 - (count($array["fsr"])/$array["number of properties"]*100) : "No data",
            )
        );

        return $data;
    }
    // From https://rosettacode.org/wiki/Averages/Median#PHP
    public function median ($arr)
    {
        sort($arr);
        $count = count($arr); //count the number of values in array
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
        if ($count % 2) { // odd number, middle is the median
            $median = $arr[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }

}
