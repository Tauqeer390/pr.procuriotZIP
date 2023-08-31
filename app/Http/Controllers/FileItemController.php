<?php

namespace App\Http\Controllers;

use App\FileItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class FileItemController extends Controller
{
    public function index()
    {
        $fileItems = FileItem::all();
        return view('index', compact('fileItems'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
    
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random(40) . '.' . $extension;
            $filePath = $file->storeAs('uploads', $fileName);
            FileItem::create([
                'title' => $request->input('title'),
                'file_name' => $fileName, // Store the generated filename in the database
                'file_path' => $filePath,
            ]);
        }
    
        return redirect()->route('file-items.index');
    }
    

    public function edit($id)
    {
        $fileItem = FileItem::findOrFail($id);
        return view('edit', compact('fileItem'));
    }

    public function update(Request $request, $id)
    {
        $fileItem = FileItem::findOrFail($id);

        $file = $request->file('file');
        if ($file) {
            Storage::delete($fileItem->file_path); // Delete the old file
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('uploads');
            $fileItem->update([
                'title' => $request->input('title'),
                'file_name' => $fileName,
                'file_path' => $filePath,
            ]);
        } else {
            $fileItem->update([
                'title' => $request->input('title'),
            ]);
        }

        return redirect()->route('index');
    }

}