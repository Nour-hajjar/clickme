<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Validator;

use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;

use App\Models\Statistics;


class HomeController extends Controller
{
    public function statistics(Request $request)
    {
        $statistics = Statistics::orderby('total', 'desc')->get();
        return response()->json($statistics, 200);
    }

    public function increase(Request $request)
    {



        $validatedData = Validator::make($request->all(), [
            'pop_count' => 'required|numeric|min:1|max:800',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => $validatedData->messages(),
            ], 422);
        }


        $ipAddress = $request->ip();
        $ip_country = Location::get($ipAddress);

        if($ip_country)
        {
            $country = Statistics::where('code',  $ip_country->countryCode)->first();


            if($country)
            {
                $country->total += $request->pop_count;
                $country->save();
                return response()->json([
                    'message' => 'done!!',
                ], 200);
            }
            else
            {
                return response()->json([
                    'message' => 'country not exist!',
                ], 422);
            }
        }

        else
        {
                return response()->json([
                    'message' => 'country not exist!',
                ], 422);
        }
    }
}
