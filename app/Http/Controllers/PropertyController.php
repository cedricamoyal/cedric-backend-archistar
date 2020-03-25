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

    // Get a summary of all property analytics for an inputted suburb
    // (min value, max value, median value, percentage properties with a value, percentage properties without a value)
    public function suburbAnalytics ($suburb)
    {
        // Get all the properties for the suburb "$suburb"
        $properties = Property::with('analytics')->where('suburb', $suburb)->get();

        // Get list of values per type (for one suburb) - See format below:
        // {
        //     "max_Bld_Height_m": [36,31,17, ...],
        //     "min_lot_size_m2": [896,801,1067, ...],
        //     "fsr": [34, 17, ...],
        //     "number of properties": 16
        // }
        $arraysOfListOfValuesPerSuburbPerType = $this->getListOfValuesPerType($properties);

        // Get suburb analytics
        // (min value, max value, median value, percentage properties with a value, percentage properties without a value)
        // See format below:
        // {
        //     "max_Bld_Height_m": {
        //         "min value": 10,
        //         "max value": 36,
        //         "median value": 18,
        //         "percentage properties with a value": 100,
        //         "percentage properties without a value": 0
        //     },
        //     "min_lot_size_m2": {
        //         "min value": 238,
        //         "max value": 1067,
        //         "median value": 896,
        //         "percentage properties with a value": 68.75,
        //         "percentage properties without a value": 31.25
        //     },
        //     "fsr": {
        //         "min value": 0,
        //         "max value": 0,
        //         "median value": 0,
        //         "percentage properties with a value": "No data",
        //         "percentage properties without a value": "No data"
        //     }
        // }
        $arrayOfSuburbAnalytics = $this->getSummaryOfAllPropertyAnalytics($arraysOfListOfValuesPerSuburbPerType);

        return $arrayOfSuburbAnalytics;
    }

    // Get a summary of all property analytics for an inputted state
    // (min value, max value, median value, percentage properties with a value, percentage properties without a value)
    public function stateAnalytics ($state)
    {
        // Similar to public function suburbAnalytics ($suburb){} - see comments above
        $properties = Property::with('analytics')->where('state', $state)->get();
        $arraysOfListOfValuesPerStatePerType = $this->getListOfValuesPerType($properties);
        $arrayOfStateAnalytics = $this->getSummaryOfAllPropertyAnalytics($arraysOfListOfValuesPerStatePerType);

        return $arrayOfStateAnalytics;
    }

    // Get a summary of all property analytics for an inputted country
    // (min value, max value, median value, percentage properties with a value, percentage properties without a value)
    public function countryAnalytics ($country)
    {
        // Similar to public function suburbAnalytics ($suburb){} - see comments above
        $properties = Property::with('analytics')->where('country', $country)->get();
        $arraysOfListOfValuesPerCountryPerType = $this->getListOfValuesPerType($properties);
        $arrayOfCountryAnalytics = $this->getSummaryOfAllPropertyAnalytics($arraysOfListOfValuesPerCountryPerType);

        return $arrayOfCountryAnalytics;
    }

    // Get list of values per type
    public function getListOfValuesPerType ($array)
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
    // Get suburb analytics
    // (min value, max value, median value, percentage properties with a value, percentage properties without a value)
    public function getSummaryOfAllPropertyAnalytics ($array)
    {
        $data = array(
            "max_Bld_Height_m" => array(
                "min value" => count($array["max_Bld_Height_m"]) ? min($array["max_Bld_Height_m"]) : 0,
                "max value" => count($array["max_Bld_Height_m"]) ? max($array["max_Bld_Height_m"]) : 0,
                "median value" => count($array["max_Bld_Height_m"]) ? $this->median($array["max_Bld_Height_m"]) : 0,
                "percentage properties with a value" =>
                    (count($array["max_Bld_Height_m"]) && $array["number of properties"]) ? round(count($array["max_Bld_Height_m"])/$array["number of properties"]*100, 2) : "No data",
                "percentage properties without a value" =>
                    (count($array["max_Bld_Height_m"]) && $array["number of properties"]) ? round(100 - (count($array["max_Bld_Height_m"])/$array["number of properties"]*100),2) : "No data",
            ),
            "min_lot_size_m2" => array(
                "min value" => count($array["min_lot_size_m2"]) ? min($array["min_lot_size_m2"]) : 0,
                "max value" => count($array["min_lot_size_m2"]) ? max($array["min_lot_size_m2"]) : 0,
                "median value" => count($array["min_lot_size_m2"]) ? $this->median($array["min_lot_size_m2"]) : 0,
                "percentage properties with a value" =>
                    (count($array["min_lot_size_m2"]) && $array["number of properties"]) ? round(count($array["min_lot_size_m2"])/$array["number of properties"]*100, 2) : "No data",
                "percentage properties without a value" =>
                    (count($array["min_lot_size_m2"]) && $array["number of properties"]) ? round(100 - (count($array["min_lot_size_m2"])/$array["number of properties"]*100), 2) : "No data",
            ),
            "fsr" => array(
                "min value" => count($array["fsr"]) ? min($array["fsr"]) : 0,
                "max value" => count($array["fsr"]) ? max($array["fsr"]) : 0,
                "median value" => count($array["fsr"]) ? $this->median($array["fsr"]) : 0,
                "percentage properties with a value" =>
                    (count($array["fsr"]) && $array["number of properties"]) ? round(count($array["fsr"])/$array["number of properties"]*100, 2) : "No data",
                "percentage properties without a value" =>
                    (count($array["fsr"]) && $array["number of properties"]) ? round(100 - (count($array["fsr"])/$array["number of properties"]*100), 2) : "No data",
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
