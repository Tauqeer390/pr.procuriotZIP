<?php

namespace App\Http\Controllers\Api;

use App\Venue;
use App\Beacon;
use App\Gateway;
use App\Payment;
use App\Category;
use App\BeaconProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;

class ManageProcuriotController extends Controller
{
    use ApiResponseTrait;

    public function getLocations(Request $request)
    {

        //    $venue = DB::select(DB::raw("SELECT venues.id as venue_id,venues.vendor_id as venue_vendor_id, venues.name as venue_name, venues.description as venue_description,venues.place_name as venue_place_name,venues.place_name as venue_place_name,
        // venues.longitude as venue_longitude,venues.latitude as venue_latitude, categories.id as category_id, categories.category_name as category_name,categories.image as image FROM  venues inner join categories ON venues.category=categories.id" ));
        // return $this->sendResponse($venue, 'Locations list!');
        $res = Validator::make($request->all(), [
            'long' => 'required|string',
            'lat' => 'required|string',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }
        $long = $request->long;
        $lat = $request->lat;

        $DISTANCE_KILOMETERS = 2;
        $venue = DB::select(DB::raw("SELECT venues.id as venue_id,venues.vendor_id as venue_vendor_id, venues.name as venue_name, venues.description as venue_description,venues.place_name as venue_place_name,venues.place_name as venue_place_name,
        venues.longitude as venue_longitude,venues.latitude as venue_latitude, categories.id as category_id, categories.category_name as category_name,categories.image as image,distance as distance FROM (
    SELECT *,
        (
            (
                (
                    acos(
                        sin(( $lat * pi() / 180))
                        *
                        sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                        *
                        cos(( `latitude` * pi() / 180)) * cos((( $long - `longitude`) * pi()/180)))
                ) * 180/pi()
            ) * 60 * 1.1515 * 1.609344
        )
    as distance FROM `venues`
) venues inner join categories ON venues.category=categories.id" ));

        return $this->sendResponse($venue, 'Locations list!');
    }

    public function getProcuriotListByName(Request $request)
    {

        $res = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }
        if ($request->long) {
            $long = $request->long;
        }
        if ($request->lat  ) {
            $lat = $request->lat;
        }
        $DISTANCE_KILOMETERS = 2;
        $name = $request->name;
        if (isset($long) && isset($lat)) {
            $venue = DB::select(DB::raw("SELECT  venues.id as venue_id,venues.vendor_id as venue_vendor_id, venues.name as venue_name, venues.description as venue_description,venues.place_name as venue_place_name,venues.place_name as venue_place_name ,
        venues.longitude as venue_longitude,venues.latitude as venue_latitude, categories.category_name as category_name,categories.image as image, distance as distance FROM (
    SELECT *,
        (
            (
                (
                    acos(
                        sin(( $lat * pi() / 180))
                        *
                        sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                        *
                        cos(( `latitude` * pi() / 180)) * cos((( $long - `longitude`) * pi()/180)))
                ) * 180/pi()
            ) * 60 * 1.1515 * 1.609344
        )
    as distance FROM `venues`
) venues inner join categories as  ON venues.category=categories.id
WHERE distance <= $DISTANCE_KILOMETERS AND name LIKE '%$name%'"));
        } else {
            $venue = Venue::where('name', 'LIKE', '%' . $name . '%')->with('category')->get();
        }
        return $this->sendResponse($venue, 'Venues list!');
    }
    public function getGateway($venueId)
    {
        $gateWay = Gateway::where('venue_id', $venueId)->first();
        if (!$gateWay) {
            return $this->sendError('Gateway not found.');
        } else {

            return $this->sendResponse($gateWay, 'Gateways list!');
        }
    }


    public function getBeacons($gateWayId)
    {
        $beacons = Beacon::where('gateway_id', $gateWayId)->select('id', 'mac_address', 'gateway_id', 'user_id', 'title', 'tagline', 'organization', 'description', 'distance', 'image')->get();
        if (count($beacons) < 1) {
            return $this->sendError('Beacons not found.');
        } else {
            return $this->sendResponse($beacons, 'Beacons list!');
        }
    }
    public function radians($degrees)
    {
        return 0.0174532925 * $degrees;
    }

    public function getCategories()
    {
        $categories = Category::all();
        if (!$categories) {
            return $this->sendError('Categories not found.');
        } else {
            return $this->sendResponse($categories, 'Categories list!');
        }
    }

       public function getBeaconProducts($beaconId)
    {

          $products = BeaconProduct::where('beacon_id', $beaconId)->get();
        if (count($products) < 1) {
            return $this->sendError('Products not found.');
        } else {
            return $this->sendResponse($products, 'Products list!');
        }
    }


    public function searchProducts(Request $request, $venueId)
    {

        $res = Validator::make($request->all(), [
            'tags' => 'required|string',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
         return $this->sendError('Validation Error.', $errors);
     }
            //  return Ledger::whereHas('type', static function ($query) {
            // $query->where('ledger_types.type', 'Equity');
        // })->where('ytd', '<>', '0')->get();
                $venue = Venue::where('id', $venueId)->first();
                $gateWays = Gateway::where('venue_id', $venueId)->pluck('id')->toArray();
                $beacons = Beacon::whereIn('gateway_id', $gateWays)->pluck('id')->toArray();
                $products = BeaconProduct::with("beacon")->whereIn('beacon_id', $beacons)->where(function ($query) use ($request){
                   $query->where('tags', 'LIKE', '%'.$request->tags.'%');
                 $query->orWhere('title', 'LIKE', '%'.$request->tags.'%');
                })->get();

 if (count($products) < 1) {
            return $this->sendError('Products not found.');
        } else {
            return $this->sendResponse($products, 'Products list!');
        }
    }

      public function payProduct(Request $request, $productID)
    {
       $product = BeaconProduct::where('id', $productID)->first();
        $res = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }
        $payment = new Payment();
        $payment->user_id = $request->user_id;
        $payment->amount = $product->price;
        $payment->product_id = $product->id;
        $payment->save();
        $product->status = 'paid';
        $product->save();

        return $this->sendResponse($payment, 'Payment Successfull!');
    }
}
