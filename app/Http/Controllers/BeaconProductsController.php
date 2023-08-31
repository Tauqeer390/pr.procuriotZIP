<?php

namespace App\Http\Controllers;

use App\Beacon;
use App\BeaconProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BeaconProductsController extends Controller
{
  public function  showBeacon($id)
  {
    $beacon = Beacon::findOrFail($id);
    $products = BeaconProduct::where('beacon_id', $id)->get();
    $user = auth()->user();
    return view('beacons.index', compact('beacon','products'));
  }
  public function  showBeaconModerator($id)
  {
    $beacon = Beacon::findOrFail($id);
    $products = BeaconProduct::where('beacon_id', $id)->get();

    return view('beacons.moderatorShow', compact('beacon','products'));
  }


  public function  addBeaconProduct(Request $request)
  {
        $rules = array(
            'title'     =>  'required|string|max:100',
            'tag_line'     =>  'required|string|max:100',
            'price'     =>  'required',
            'description'     =>  'required|string|max:200',
        );
        $error = Validator::make($request->all(), $rules);
    //    array:1 [
//   0 => "The description field is required."
// ]
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $product = new BeaconProduct();

        $product->title = $request->title;
        $product->description = $request->description;
        $product->tagline = $request->tag_line;
        $product->price = $request->price;
        $product->beacon_id = $request->hidden_beacon_id;
        $product->tags = $request->product_tags;
        $file = $request->file('image');
        if($file)
        {
        $destinationPath =public_path() . '/beacons_images/';
        $imagename = time().$file->getClientOriginalName();
        $image_path = '/public/beacons_images/'.$imagename;
        $file->move($destinationPath,$imagename);
        $product->image = $image_path;
        }

        $product->save();
        Session::put('success', 'Product Added Successfully.');
        return response()->json(['success' => 'Product Added Successfully.']);
  }


    public function  getBeaconProduct($id)
  {
    $data = BeaconProduct::findOrFail( $id);
    return response()->json(['data' => $data]);
  }
    public function  updateBeaconProduct(Request $request)
  {
    $product = BeaconProduct::findOrFail($request->product_id);
     $rules = array(
            'edit_product_title'    =>  'required|string|max:100',
            'edit_product_tagline'     =>  'required',
            'edit_product_description'     =>  'required',
            'edit_product_price'     =>  'required',
        );
        $error = Validator::make($request->all(), $rules);
         if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $file = $request->file('edit_product_image');
          if($file){
             $destinationPath =public_path() . '/beacons_images/';
            $imagename = time().$file->getClientOriginalName();
            $image_path = '/public/beacons_images/'.$imagename;
            $file->move($destinationPath,$imagename);
            $product->image = $image_path;

        }
        if($request->edit_product_tags != null )
        {
            $product->tags = $request->edit_product_tags;
        }

        $product->title = $request->edit_product_title;
        $product->description = $request->edit_product_description;
        $product->tagline = $request->edit_product_tagline;
        $product->price = $request->edit_product_price;
        $product->beacon_id = $product->beacon_id;
        $product->save();
            Session::put('success', 'Product Updated Successfully.');
            return response()->json(['response' => 'Product is Successfully updated']);

  }
    public function  deleteBeaconProduct($id)
  {
    $beacon = BeaconProduct::findOrFail($id);
     $beacon->delete();
        Session::put('success', 'Product Deleted Successfully.');
        return response()->json(['response' => 'Product Deleted Successfully']);
  }
}
