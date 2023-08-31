<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('image');
        $request->validate([
            'name' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $destinationPath = public_path() . '/category_images/';

        $imagename = time() . $file->getClientOriginalName();

        $image_path = 'https://pr.procuriot.com/public/category_images/' . $imagename;
        $file->move($destinationPath, $imagename);
        $category = new Category();
        $category->category_name = $request->name;

        $category->image = $image_path;
        $category->save();
        return redirect()->route('categories.index')->with('success', 'Category Added Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
        ]);
        $category = Category::find($id);
        $category->category_name = $request->name;
        if ($request->file('image')) {
            $file = $request->file('image');

        $destinationPath = public_path() . '/category_images/';

        $imagename = time() . $file->getClientOriginalName();

        $image_path = 'https://pr.procuriot.com/public/category_images/' . $imagename;
        $file->move($destinationPath, $imagename);
        $category->image = $image_path;
        }
        $category->save();
        return redirect()->route('categories.index')->with('success', 'Category Updated Successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        Session::put('success', 'Category Deleted Successfully.');
          return response()->json(['status' => 'success']);
    }
}
