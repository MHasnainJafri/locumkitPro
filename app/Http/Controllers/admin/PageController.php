<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;


class PageController extends Controller
{
    public function index()
    {
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);
        foreach ($filesWithParents as &$file) {
            $file = str_replace('.blade.php', '', $file);
        }
        return view('admin.pages.index',compact('filesWithParents'));
    }
    
    public function updatePage(Request $request)
    {
        $oldName = $request->input('old_name');
        $newName = $request->input('document-name');
        $description = $request->input('description');
    
        $oldFilePath = resource_path('views/' . str_replace('.', '/', $oldName) . '.blade.php');
        $newFilePath = resource_path('views/' . str_replace('.', '/', $newName) . '.blade.php');
    
        // if (!File::exists($oldFilePath)) {
        //     return back()->with('message', 'Old file does not exist, cannot rename.');
        // }
    
        // if (File::exists($newFilePath)) {
        //     return back()->with('message', 'A file with the new name already exists, cannot rename.');
        // }
    
        if ($oldName !== $newName) {
            File::move($oldFilePath, $newFilePath);
        }
    
        $filePathToUpdate = $oldName === $newName ? $oldFilePath : $newFilePath;
        $content = "@extends('layouts.user_profile_app')\n\n@section('content')\n $description\n@endsection\n\n";
        File::put($filePathToUpdate, $content);
        return redirect()->route('admin.page.index');
    }



    public function create()
    {
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);
        foreach ($filesWithParents as &$file) {
            $file = str_replace('.blade.php', '', $file);
        }
        
        return view('admin.pages.create', compact('filesWithParents'));
    }

    public function store(Request $request){
        // dd($request->all());
        // $data = [
        //     'name' => $request['document-name'],
        //     'url_key' => $request['document-url_key'],
        //     'status' => 1,
        //     'sort_order' => 10,
        //     'show_in_nav' => 1,
        //     'can_be_cached' => 1,
        //     'document_type_id' => $request['document_type'],
        //     'view_id' => 1,
        //     'user_id' => Auth::user()->id,
        //     'parent_id' => $request['parent_id'],
        //     'layout_id' => 1,
        // ];


        $name=$request->name;
        $description=$request->description;
        $bladeFilePath = resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');
        $content="@extends('admin.layout.app')\n\n@section('content')\n $description\n@endsection\n\n
        ";
        if (File::exists($bladeFilePath)) {
            return back()->with('message','File already exists.');
        }
        File::put($bladeFilePath,$content);
        return back()->with('message','File created Succesfully...!');

    }

    public function getFilesWithParents($directory)
    {
        $filesByParents = [];

        $allFiles = File::allFiles($directory);
        // dd($allFiles);

        foreach ($allFiles as $file) {
            $relativePath = $file->getRelativePath();
            $filename = $file->getFilename();

            $parentKey = $relativePath ?: 'root';
            if (!isset($filesByParents[$parentKey])) {
                $filesByParents[$parentKey] = [];
            }

            $filesByParents[$parentKey][] = $filename;
        }
        //  dd($filesByParents);
        return $filesByParents;
    }
    
    public function deletePage(Request $request)
    { 
        $pageName = $request->input('page_name');
        $viewsDirectory = resource_path('views');
        $matchingFiles = glob($viewsDirectory . '/*' . str_replace(' ', '*', $pageName) . '*.*');
        if (empty($matchingFiles)) {
            return response()->json([
                'status' => 'error',
                'message' => "No files found containing the name '{$pageName}' in the views directory."
            ]);
        }
        foreach ($matchingFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        return redirect()->route('admin.page.index');
        return response()->json([
            'status' => 'success',
            'message' => "File(s) matching '{$pageName}' have been deleted successfully.",
            'deleted_files' => array_map(fn($file) => str_replace(base_path(), '', $file), $matchingFiles)
        ]);
    }


}
