<?php

namespace App\Http\Controllers;

use App\Beacon;
use App\Category;
use App\Gateway;
use App\Mail\SendMail;
use App\Mail\SendMail2;
use App\Organization;
use App\User;
use App\Venue;
use console;
use DOMDocument;
use DOMXPath;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Mail;
use Throwable;
use Validator;

class UserController extends Controller
{

    public function getEditVEN(Request $request)
    {
        if ($request->is_EditFTN == 'true') {
            return Venue::where('id', $request->VarIDVenu)->first();
        }
        // 'is_EditFTN':true,
        // 'VarIDVenu':VarIDVenu,
    }
    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function addGateway(Request $request)
    {
        //      $this->validate($request, [
        //            'title'    =>  'required|string|max:100',
        //            'mac'    =>  'required',
        //            'location' =>  'required'
        //        ]);
        //
        //        if(!preg_match('/^(?:(?:[0-9a-f]{2}[\:]{1}){5}|(?:[0-9a-f]{2}[-]{1}){5}|(?:[0-9a-f]{2}){5})[0-9a-f]{2}$/i', $request->mac)){
        //            $isshow = true;
        //            return back()->with('success', 'Invalid mac address', compact('isshow'));
        //        }
        //
        //        $locationData = Venue::find($request->location);
        //
        //        $form_data = array(
        //            'vendor_id' => Auth::id(),
        //            'title' => $request->title,
        //            'mac_address' => $request->mac,
        //            'venue_id' => $request->location,
        //            'venue_name' => $locationData->name,
        //            'venue_lat' => $locationData->latitude,
        //            'venue_long' => $locationData->longitude
        //        );
        //
        //        if (Gateway::create($form_data)) {
        //            return redirect('/dashboard');
        //        } else {
        //            return back()->with('error', 'Something went wrong');
        //        }


        $rules = array(
            'title' => 'required|string|max:100',
            'mac' => 'required',
            'venue' => 'required|unique:gateways,venue_id,'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $gateway = Gateway::where("mac_address", $request->mac)->first();
        if ($gateway) {
            $organization = Organization::find($gateway->organization);
            $user = User::find($gateway->vendor_id);
            return response()->json(['errors' => ['This mac address is already in use by ' . $user->first_name . ' of ' . $organization->name]]);
        }
        //        if(!preg_match('/^(?:(?:[0-9a-f]{2}[\:]{1}){5}|(?:[0-9a-f]{2}[-]{1}){5}|(?:[0-9a-f]{2}){5})[0-9a-f]{2}$/i', $request->mac)){
        //            return response()->json(['invalid_mac' => 'Invalid mac address']);
        //        }

        $locationData = Venue::find($request->venue);

        $checkExists = Gateway::where('venue_id', $request->venue)
            ->where('venue_name', $locationData->name)
            ->where('venue_lat', $locationData->latitude)
            ->where('venue_long', $locationData->longitude)
            ->get();

        if (count($checkExists) > 0) {
            return response()->json(['success' => 'Some gateway is already using this location']);
        }

        $loggedInAdmin = User::find(Auth::id());

        $form_data = array(
            'vendor_id' => Auth::id(),
            'title' => $request->title,
            'mac_address' => $request->mac,
            'venue_id' => $request->venue,
            'venue_name' => $locationData->name,
            'venue_lat' => $locationData->latitude,
            'venue_long' => $locationData->longitude,
            'organization' => $loggedInAdmin->company
        );

        if (Gateway::create($form_data)) {
            Session::put('success', 'Gateway Added Successfully.');
            return response()->json(['success' => 'Gateway Added Successfully.']);
        } else {
            return response()->json(['errors' => 'Something went wrong']);
        }
    }

    public function addBeacon(Request $request)
    {
        $rules = array(
            // 'uuid'    =>  'required|unique:beacons,uuid',
            'mac' => 'required',
            'product_title' => 'required|string|max:100',

            'description' => 'required|string|max:200',
            // 'distance'     =>  'required|string|max:200',
            //    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'image' => 'required',
        );
        $error = Validator::make($request->all(), $rules);
        //    array:1 [
        //   0 => "The description field is required."
        // ]
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $beacon = Beacon::where(['user_id' => Auth::id(), "mac_address" => $request->mac])->first();
        // "organization" => "71"
        // "user_id" => "204"
        if ($beacon) {
            $user = User::where("id", $beacon->user_id)->first();
            $organization = Organization::where("id", $beacon->organization)->first();
            $error = [
                0 => 'Beacon already exists, It is registered to ' . $user->first_name . ' ' . $user->last_name . ' of ' . $organization->name
            ];
            return response()->json(['errors' => $error]);
        }

        //        if(!preg_match('/^(?:(?:[0-9a-f]{2}[\:]{1}){5}|(?:[0-9a-f]{2}[-]{1}){5}|(?:[0-9a-f]{2}){5})[0-9a-f]{2}$/i', $request->mac)){
        //            return response()->json(['invalid_mac' => 'Invalid mac address']);
        //        }

        $file = $request->file('image');
        $destinationPath = public_path() . '/beacons_images/';
        $imagename = time() . $file->getClientOriginalName();

        $image_path = url('/public/beacons_images/' . $imagename);
        $file->move($destinationPath, $imagename);

        $loggedInAdmin = User::find(Auth::id());

        $form_data = array(
            // 'uuid' => $request->uuid,
            'mac_address' => $request->mac,
            'gateway_id' => $request->hidden_gateway_id,
            'organization' => $loggedInAdmin->company,
            'user_id' => Auth::id(),
            'title' => $request->product_title,
            'tagline' => 'null',
            'description' => $request->description,
            'distance' => "null",
            'image' => $image_path
        );

        if (Beacon::create($form_data)) {
            Session::put('success', 'Beacon Added Successfully.');
            return response()->json(['success' => 'Beacon Added Successfully.']);
        } else {
            return response()->json(['errors' => 'Something went wrong']);
        }
    }

    public function addVenue(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:300',
            'category' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'place_name' => 'required',
        ];

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $placeNames = $request->input('place_name');
        $floorNumber = $request->input('floor_number');
        $loggedInAdmin = User::find(Auth::id());


        $venues = [];

        $form_data = [
            'vendor_id' => Auth::id() ?? '',
            'name' => $request->name,
            'description' => $request->description,
            'place_name' => $placeNames[0],
            'latitude' => $latitude[0],
            'longitude' => $longitude[0],
            'organization' => $loggedInAdmin->company ?? '',
            'category' => $request->category,
            'floor_number' => $floorNumber,
            'markers' => $request->markerData
        ];

        if ($request->hasFile('geoJson_file')) {
            $file = $request->file('geoJson_file');
            $extension = $file->getClientOriginalExtension();
            $imagepath = time() . '.' . $extension;
            $path = $file->move('app/public/assets/images/geojson_files/', $imagepath);
            $form_data['geoJson_file'] = $path;
        }

        $venues[] = $form_data;


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        if (Venue::insert($venues)) {
            Session::put('success', 'Venue Added Successfully.');
            return response()->json(['success' => 'Venue Added Successfully.']);
        } else {
            return response()->json(['errors' => 'Something went wrong']);
        }
    }


    
    public function editGateway($gateway_id)
    {

        $data = Gateway::find($gateway_id);
        return response()->json(['data' => $data]);
    }

    public function editBeacon($beacon_id)
    {

        $data = Beacon::find($beacon_id);
        return response()->json(['data' => $data]);
    }

    public function editVenue($venue_id)
    {

        $data = Venue::find($venue_id);
        $categories = Category::all();
        return response()->json(['data' => $data, 'categories' => $categories]);
    }


    public function updateGateway(Request $request)
    {
        $rules = array(
            'edit_gateway_title' => 'required',
            'edit_gateway_mac_address' => 'required',
            'edit_gateway_venue_id' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $gateWay = Gateway::find($request->gateway_id);

        $countGateway = Gateway::where('mac_address', $request->edit_gateway_mac_address)->where('id', '!=', $gateWay->id)->first();

        if ($countGateway) {
            $organization = Organization::find($countGateway->organization);
            $user = User::find($countGateway->vendor_id);
            return response()->json(['errors' => ['This mac address is already in use by ' . $user->first_name . ' of ' . $organization->name]]);
        }


        $check = Gateway::where('mac_address', $request->edit_gateway_mac_address)->where('venue_id', $request->edit_gateway_venue_id)->get();

        if (count($check) > 1) {
            return response()->json(['errors' => ['mac adddress already taken']]);
        }
        $venueData = Venue::find($request->edit_gateway_venue_id);
        $form_data = array(
            'title' => $request->edit_gateway_title,
            'mac_address' => $request->edit_gateway_mac_address,
            'venue_id' => $request->edit_gateway_venue_id,
            'venue_name' => $venueData->name
        );
        Gateway::whereId($request->gateway_id)->update($form_data);

        Session::put('success', 'Gateway Updated Successfully.');
        return response()->json(['success' => 'Data is Successfully updated.']);
    }

    public function updateVenue(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:300',
            'category' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'place_name' => 'required',
        );


        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $placeNames = $request->input('place_name');
        $floorNumber = $request->input('floor_number');
        $loggedInAdmin = User::find(Auth::id());
        $form_data = array(
            'vendor_id' => Auth::id() ?? '',
            'name' => $request->name,
            'description' => $request->description,
            'place_name' => $placeNames[0],
            'latitude' => $latitude[0],
            'longitude' => $longitude[0],
            'organization' => $loggedInAdmin->company ?? '',
            'category' => $request->category,
            'floor_number' => $floorNumber,
            'markers' => $request->input('markersData')

        );

        if ($request->hasFile('geoJson_file')) {
            $file = $request->file('geoJson_file');
            $extension = $file->getClientOriginalExtension();
            $imagepath = time() . '.' . $extension;
            $path = $file->move('app/public/assets/images/geojson_files', $imagepath);
            $form_data['geoJson_file'] = $path;
            $form_data['file_path'] = $imagepath;
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        if (Venue::whereId($request->venuID)->update($form_data)) {
            Session::put('success', 'Venue Updated Successfully.');
            return response()->json(['success' => 'Data is Successfully updated']);
        } else {
            return response()->json(['errors' => 'Something went wrong']);
        }
    }

    public function getMarkedLocations($venue_id)
    {
        $venue = Venue::find($venue_id);
        if ($venue) {
            $markedLocations = json_decode($venue->venues, true);
            return response()->json($markedLocations);
        } else {
            return response()->json(['error' => 'Venue not found']);
        }
    }


    public function updateBeacon(Request $request)
    {
        $rules = array(
            'edit_beacon_uuid' => 'required|unique:beacons,uuid,' . $request->beacon_id,
            'edit_beacon_title' => 'required|string|max:100',
            'edit_beacon_mac_address' => 'required|string|max:300',
            'edit_beacon_description' => 'required',
            // 'edit_beacon_distance'     =>  'required',

        );
        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $file = $request->file('edit_beacon_image');

        if ($file) {
            $destinationPath = public_path() . '/beacons_images/';
            $imagename = time() . $file->getClientOriginalName();
            $image_path = url('/public/beacons_images/' . $imagename);
            $file->move($destinationPath, $imagename);

            $form_data = array(
                'uuid' => $request->edit_beacon_uuid,
                'title' => $request->edit_beacon_title,
                'mac_address' => $request->edit_beacon_mac_address,
                'tagline' => 'null',
                'description' => $request->edit_beacon_description,
                'distance' => "null",
                'image' => $image_path
            );
            $beacon = Beacon::find($request->beacon_id);
            $beaconCheck = Beacon::where("mac_address", $request->edit_beacon_mac_address)->where("id", "!=", $beacon->id)->where("gateway_id", $beacon->gateway_id)->first();
            // "organization" => "71"
            // "user_id" => "204"
            if ($beaconCheck) {
                $user = User::where("id", $beacon->user_id)->first();
                $organization = Organization::where("id", $beacon->organization)->first();
                $error = [
                    0 => 'Beacon already exists, It is registered to ' . $user->first_name . ' ' . $user->last_name . ' of ' . $organization->name
                ];
                return response()->json(['errors' => $error]);
            }
            Beacon::whereId($request->beacon_id)->update($form_data);
            Session::put('success', 'Beacon Updated Successfully.');
            return response()->json(['response' => 'Data is Successfully updated']);
        } else {
            $beacon = Beacon::find($request->beacon_id);
            $beaconCheck = Beacon::where("mac_address", $request->edit_beacon_mac_address)->where("id", "!=", $beacon->id)->where("gateway_id", $beacon->gateway_id)->first();
            // "organization" => "71"
            // "user_id" => "204"
            if ($beaconCheck) {
                $user = User::where("id", $beacon->user_id)->first();
                $organization = Organization::where("id", $beacon->organization)->first();
                $error = [
                    0 => 'Beacon already exists, It is registered to ' . $user->first_name . ' ' . $user->last_name . ' of ' . $organization->name
                ];
                return response()->json(['errors' => $error]);
            }
            $form_data = array(
                'uuid' => $request->edit_beacon_uuid,
                'title' => $request->edit_beacon_title,
                'mac_address' => $request->edit_beacon_mac_address,
                'tagline' => 'null',
                'description' => $request->edit_beacon_description,
                'distance' => "null",
            );
            Beacon::whereId($request->beacon_id)->update($form_data);
            Session::put('success', 'Beacon Updated Successfully.');

            return response()->json(['response' => 'Data is Successfully updated']);
        }
    }


    public function deleteGateway($gateway_id)
    {

        $data = Gateway::findOrFail($gateway_id);
        $beacons = Beacon::where('gateway_id', $gateway_id)->get();

        foreach ($beacons as $beacon) {
            $beacon->delete();
        }
        $data->delete();

        Session::put('success', 'Gateway Deleted Successfully.');
        return response()->json(['response' => 'Deleted Successfully']);
    }


    public function deleteOrganization($id)
    {

        $data = Organization::findOrFail($id);

        if ($data->delete()) {

            $users = User::where('company', $data->id)->get();
            $venues = Venue::where('organization', $data->id)->get();
            $gateways = Gateway::where('organization', $data->id)->get();
            $beacons = Beacon::where('organization', $data->id)->get();

            foreach ($users as $user) {
                $user->delete();
            }
            foreach ($venues as $venue) {
                $venue->delete();
            }
            foreach ($gateways as $gateway) {
                $gateway->delete();
            }
            foreach ($beacons as $beacon) {
                $beacon->delete();
            }

            Session::put('success', 'Organization Deleted Successfully.');
            return response()->json(['response' => 'Deleted Successfully.']);
        } else {
            return response()->json(['response' => 'Something went wrong']);
        }
    }

    public function deleteAdmin($id)
    {

        $data = User::findOrFail($id);
        if ($data->role == "moderator") {
            $message = "Moderator Deleted Successfully.";
        } else {
            $message = "Admin Deleted Successfully.";
        }
        if ($data->delete()) {
            Session::put('success', $message);
            return response()->json(['response' => 'Deleted Successfully']);
        } else {
            return response()->json(['response' => 'Something went wrong']);
        }
    }

    public function deleteBeacon($beacon_id)
    {

        $data = Beacon::findOrFail($beacon_id);
        $data->delete();
        Session::put('success', 'Beacon Deleted Successfully.');
        return response()->json(['response' => 'Deleted Successfully']);
    }

    public function deletevenue($id)
    {

        $data = Venue::findOrFail($id);
        $data->delete();

        $gateways = Gateway::where('venue_id', $data->id)->get();

        foreach ($gateways as $gateway) {

            $beacons = Beacon::where('gateway_id', $gateway->id)->get();

            foreach ($beacons as $beacon) {

                $beacon->delete();
            }

            $gateway->delete();
        }
        Session::put('success', 'Venue Deleted Successfully.');
        return response()->json(['response' => 'Deleted Successfully.']);
    }

    public function sendMail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email'
        ]);
        $userdata = User::where('email', $request->email)->first();
        $name = $userdata->first_name . $userdata->last_name;
        $link = url('/reset/password/' . $userdata->id . '/' . time() . 'g56f7da9s8d76f5gg6h5j4kl34k5j6');

        Mail::to($request->email)->send(new SendMail($link, $name));
        Session::put('success', 'Check your email to reset password');
        return back()->with('success', 'Check your email to reset password');
    }

    public function sendLink($id)
    {
        $userdata = User::find($id);
        $name = $userdata->first_name . $userdata->last_name;
        $link = url('/reset/password/' . $userdata->id . '/' . time() . 'g56f7da9s8d76f5gg6h5j4kl34k5j6');

        Mail::to($userdata->email)->send(new SendMail($link, $name));
        Session::put('success', 'Password reset link sent Successfully.');
        return response()->json(['response' => 'Password reset link sent Successfully...']);
    }

    public function resetPassword($id, $email)
    {
        return view('password_reset', compact('id', 'email'));
    }
    public function updatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //            'password' => 'required|min:8|confirmed',
            'password' => 'required|string|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        } else {

            try {

                $getmail = User::find($request->id);

                if (
                    User::where('id', $request->id)->where('email', $getmail->email)
                        ->update([
                            'password' => bcrypt($request->password)
                        ])
                ) {

                    //                    return back()->with('success', 'Password updated');
                    return redirect('/')->with('success', 'Password Reset Successfully.');
                    ;
                } else {
                    return back()->with('error', 'Password not updated..user may be deleted ');
                }
            } catch (Throwable $e) {
                console . log('Project update error ' . $e);
            }
        }
    }

    public function VerifyAccount($time, $id)
    {

        if (
            User::where('id', $id)->where('created_at', $time)
                ->update([
                    'active' => 1
                ])
        ) {

            return redirect('/')->with('success', 'Account verified Successfully. You can now login!');
        } else {

            echo "Something went wrong";
        }
    }

    public function getVenues(Request $request)
    {
        $data = Venue::where('vendor_id', Auth::id())->get();
        $venues = Venue::where('vendor_id', Auth::id())->get();
        $categories = Category::all();
        return view('venues', compact('data', 'venues', 'categories'));
    }

    //    public function getGateways(Request $request){
    //        $gateways = Gateway::where('vendor_id', Auth::id())->get();
    //        $locations = Venue::where('vendor_id', Auth::id())->get();
    //        $beacons = Beacon::all();
    //        return view('gateways', compact('gateways','locations', 'beacons'));
    //    }
    public function getGateways(Request $request)
    {

        $loggedInAdmin = User::find(Auth::id());
        $gateways = Gateway::where('organization', $loggedInAdmin->company)->get();
        $locations = Venue::where('organization', $loggedInAdmin->company)->get();
        $beacons = Beacon::where('organization', $loggedInAdmin->company)->get();

        return view('gateways', compact('gateways', 'locations', 'beacons'));
    }

    public function getGatewaysForModerators(Request $request)
    {

        $loggedInAdmin = User::find(Auth::id());

        $gateways = Gateway::where('organization', $loggedInAdmin->company)->get();
        $locations = Venue::where('organization', $loggedInAdmin->company)->get();
        $beacons = Beacon::where('organization', $loggedInAdmin->company)->get();

        return view('gatewaysFoModerators', compact('gateways', 'locations', 'beacons'));
    }

    public function scrapping(Request $request)
    {

        $curl = curl_init('http://www.flipkart.com/apple-iphone-5s/p/itmdv6f75dyxhmt4?pid=MOBDPPZZDX8WSPAT');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $page = curl_exec($curl);

        if (!empty($curl)) { //if any html is actually returned

            $pokemon_doc = new DOMDocument;
            libxml_use_internal_errors(true);
            $pokemon_doc->loadHTML($page);
            libxml_clear_errors();

            $pokemon_xpath = new DOMXPath($pokemon_doc);

            $price = $pokemon_xpath->evaluate('string(//div[@class="prices"]/meta[@itemprop="price"]/@content)');
            echo $price;

            $rupees = $pokemon_xpath->evaluate('string(//div[@class="prices"]/div/span)');
            echo $rupees;
        } else {
            print "Not found";
        }
    }

    public function organizations(Request $request)
    {
        //        $organizations = Organization::all();
        $organizations = Organization::latest()->get();
        return view('superadminDashboard', compact('organizations'));
    }

    public function admins(Request $request)
    {
        //        $admins = User::where('role', 'admin')->get();
        $admins = User::where('role', 'admin')->latest()->get();
        $organizations = Organization::all();
        return view('admins', compact('admins', 'organizations'));
    }

    public function editOrganization($id)
    {
        $data = Organization::find($id);
        return response()->json(['data' => $data]);
    }

    public function editAdmin($id)
    {
        $data = User::find($id);
        return response()->json(['data' => $data]);
    }

    public function updateOrganization(Request $request)
    {

        $form_data = array(
            'name' => $request->edit_org_name,
            'contact' => $request->edit_contact,
            'address' => $request->edit_address
        );
        Organization::whereId($request->edit_org_id)->update($form_data);

        return response()->json(['response' => 'Data is Successfully updated.']);
    }

    public function updateAdmin(Request $request)
    {

        $form_data = array(
            'first_name' => $request->edit_first_name,
            'last_name' => $request->edit_last_name,
            'email' => $request->edit_email,
            'phone' => $request->edit_phone,
            'company' => $request->edit_organization,
            'address' => $request->edit_address,
        );
        User::whereId($request->edit_admin_id)->update($form_data);

        return response()->json(['response' => 'Data is Successfully updated']);
    }

    public function updateSelfAdmin(Request $request)
    {

        $loggedInAdmin = User::find(Auth::id());

        $form_data = array(
            'first_name' => $request->edit_first_name,
            'last_name' => $request->edit_last_name,
            'email' => $request->edit_email,
            'phone' => $request->edit_phone,
            'company' => $loggedInAdmin->company,
            'address' => $request->edit_address,
        );
        User::whereId($request->edit_admin_id)->update($form_data);

        return response()->json(['response' => 'Data is Successfully updated']);
    }
    public function addOrganization(Request $request)
    {

        $rules = array(
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        if (!isset($request->edit_org_id) && empty($request->edit_org_id)) {
            $form_data = array(
                'name' => $request->name,
                'code' => $request->get_c_code,
                'contact' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            );

            Organization::create($form_data);
            Session::put('success', 'Organization Added Successfully.');
            return response()->json(['success' => 'Organization Added Successfully.']);
        } else {

            $form_data = array(
                'name' => $request->name,
                'code' => $request->get_c_code,
                'contact' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            );
            Organization::whereId($request->edit_org_id)->update($form_data);
            Session::put('success', 'Organization Updated Successfully.');
            return response()->json(['updated' => 'Data Update Successfully.']);
        }
    }



    public function addAdmin(Request $request)
    {

        if (!isset($request->getadmin_id) && empty($request->getadmin_id)) {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role = 'admin';
            $password = $this->randomPassword();

            $form_data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'company' => $request->organization,
                'role' => $role,
                'password' => Hash::make($password),
                'code' => $request->get_c_code,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            );


            $newUser = User::create($form_data);

            if ($newUser) {

                $username = $request->email;
                $link = url('/verify/account/' . $newUser->created_at . '/' . $newUser->id);

                Mail::to($request->email)->send(new SendMail2($username, $password, $link));

                Session::put('success', 'Admin Added Successfully.');
                return response()->json(['success' => 'Admin Added Successfully.']);
            } else {
                return response()->json(['errors' => 'Unable to create new admin']);
            }
        } else {

            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role = 'admin';

            $form_data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'company' => $request->organization,
                'role' => $role,
                'code' => $request->get_c_code,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            );
            $check = User::whereId($request->getadmin_id)->update($form_data);

            if ($check) {
                Session::put('success', 'Admin Updated Successfully.');
                return response()->json(['updated' => 'Data is Successfully updated']);
            } else {
                return response()->json(['errors' => 'Unable to update']);
            }
        }
    }

    public function addSelfAdmin(Request $request)
    {

        if (!isset($request->self_admin_id) && empty($request->self_admin_id)) {
            // dd($request->all());
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role = 'admin';
            $password = $this->randomPassword();
            $loggedInAdmin = User::find(Auth::id());

            $form_data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'company' => $loggedInAdmin->company,
                'role' => $role,
                'password' => Hash::make($password),
                'code' => $request->get_c_code,
                'phone' => $request->get_c_code . $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            );


            $newUser = User::create($form_data);

            if ($newUser) {

                $username = $request->email;
                $link = url('/verify/account/' . $newUser->created_at . '/' . $newUser->id);

                Mail::to($request->email)->send(new SendMail2($username, $password, $link));
                Session::put('success', 'Admin Added Successfully.');
                return response()->json(['success' => 'Admin Added Successfully.']);
            }

            return response()->json(['errors' => 'Unable to create new admin']);
        } else {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role = 'admin';
            $password = $this->randomPassword();
            $loggedInAdmin = User::find(Auth::id());

            $form_data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'company' => $loggedInAdmin->company,
                'role' => $role,
                'code' => $request->get_c_code,
                'phone' => $request->get_c_code . $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            );


            $checkUpdate = User::whereId($request->self_admin_id)->update($form_data);

            if ($checkUpdate) {
                Session::put('success', 'Admin Updated Successfully.');
                return response()->json(['updated' => 'Admin updated Successfully.']);
            }

            return response()->json(['errors' => 'Unable to update admin']);
        }
    }


    public function addModerator(Request $request)
    {

        if (!isset($request->get_moderator_id) && empty($request->get_moderator_id)) {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role22 = 'moderator';

            $password = $this->randomPassword();
            $loggedInAdmin = User::find(Auth::id());

            $form_data22 = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => $role22,
                'company' => $loggedInAdmin->company,
                'password' => Hash::make($password),
                'code' => $request->get_c_code,
                'phone' => $request->get_c_code . $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            );


            $newUser = User::create($form_data22);

            if ($newUser) {

                $username = $request->email;
                $link = url('/verify/account/' . $newUser->created_at . '/' . $newUser->id);

                Mail::to($request->email)->send(new SendMail2($username, $password, $link));

                Session::put('success', 'Moderator Added Successfully.');
                return response()->json(['success' => 'Moderator Added Successfully.']);
            }

            return response()->json(['errors' => 'Unable to create new moderator']);
        } else {

            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'organization' => 'required',
                'phone' => 'required',
                'address' => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $role22 = 'moderator';

            $password = $this->randomPassword();
            $loggedInAdmin = User::find(Auth::id());

            $form_data22 = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => $role22,
                'company' => $loggedInAdmin->company,
                'code' => $request->get_c_code,
                'phone' => $request->get_c_code . $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            );


            $checkUpdate = User::whereId($request->get_moderator_id)->update($form_data22);

            if ($checkUpdate) {
                Session::put('success', 'Moderator Updated Successfully.');
                return response()->json(['updated' => 'Data updated Successfully.']);
            }

            return response()->json(['errors' => 'Unable to update moderator']);
        }
    }


    public function self_admins(Request $request)
    {

        $loggedInAdmin = User::find(Auth::id());

        $organization = Organization::find($loggedInAdmin->company);

        $admins = User::where('role', 'admin')->where('company', $organization->id)->get();

        return view('self_admins', compact('admins', 'organization'));
    }

    public function moderators(Request $request)
    {


        $loggedInAdmin = User::find(Auth::id());

        $organization = Organization::where('id', $loggedInAdmin->company)->first();

        $moderators = User::where('role', 'moderator')->where('company', $loggedInAdmin->company)->get();

        return view('moderators', compact('moderators', 'organization'));
    }

    public function moderatorsDashboard(Request $request)
    {
        return view('moderatorDashboard');
    }

    public function LoadcreateOrgView(Request $request)
    {

        if ($request->id) {
            $data = Organization::find($request->id);
            return view('create_organization', compact('data'));
        } else {
            return view('create_organization');
        }
    }

    public function LoadcreateAdminView(Request $request)
    {
        $organizations = Organization::all();
        if ($request->id) {
            $data = User::find($request->id);
            return view('create_admin', compact('data', 'organizations'));
        } else {
            return view('create_admin', compact('organizations'));
        }
    }
    public function LoadcreateSelfAdminView(Request $request)
    {
        $data2 = User::find(Auth::id());
        if ($request->id) {
            $data = User::find($request->id);
            return view('create_selfadmin', compact('data', 'data2'));
        } else {
            return view('create_selfadmin', compact('data2'));
        }
    }
    public function LoadcreateModeratorView(Request $request)
    {
        $data2 = User::find(Auth::id());
        if ($request->id) {
            $data = User::find($request->id);
            return view('create_moderators', compact('data', 'data2'));
        } else {
            return view('create_moderators', compact('data2'));
        }
    }
}