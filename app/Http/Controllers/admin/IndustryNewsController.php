<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\CronJobNewIndustryNewsReminder;
use App\Models\IndustryNews;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IndustryNewsController extends Controller
{
    public function index(){
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);
        $values = IndustryNews::all();
        return view('admin.industryNews.index',compact('values','filesWithParents'));
    }
    public function create(){
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);
        return view('admin.industryNews.create', compact('filesWithParents'));
    }
    public function store(Request $request){

        // $request->validate([
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);

        $imageFile = $request->file('image');
        $path = $imageFile->store('public/media/files/industry_news');
        $newPath = str_replace("public/", "", $path);
        $data = [
            'title' => $request -> title ?? '',
            'slug' => $request -> slug ?? '',
            'description' => $request -> description ?? '',
            'image_path' => $newPath,
            'status' => $request->status,
            'user_type' => implode(',',  $request -> user_type ?? []),
            'category_id' => implode( ',', $request -> profession_type ?? []),
            'metatitle' => $request -> metatitle ?? '',
            'metadescription' => $request -> metadescription ?? '',
            'metakeywords' => $request -> metakeywords ?? '',
        ];
        $news = IndustryNews::create($data);
        dispatch(new CronJobNewIndustryNewsReminder($news));
        if($news){
            return redirect()->route('industry.index')->with('success','Industry News Added Successfully');
        }
        else{
            abort(500);
        }
    }
    public function edit($id){
        $news = IndustryNews::where('id', $id)->first();
        return view('admin.industryNews.edit', compact('news'));
    }
    public function update(Request $request,$id){
        $news = IndustryNews::where('id', $id)->first();

        if($request -> image){
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageFile = $request->file('image');

            $path = $imageFile->store('public/media/files/industry_news');
            $newPath = str_replace("public/", "", $path);
        }
        else{
            $newPath = $news -> image_path;
        }
        $data = [
            'title' => $request -> title ?? '',
            'slug' => $request -> slug ?? '',
            'description' => $request -> description ?? '',
            'image_path' => $newPath,
            'user_type' => implode(',',  $request -> user_type ?? []),
            'status' => $request -> status ?? '',
            'category_id' => implode( ',', $request -> profession_type ?? []),
            'metatitle' => $request -> metatitle ?? '',
            'metadescription' => $request -> metadescription ?? '',
            'metakeywords' => $request -> metakeywords ?? '',
        ];
        $news -> update($data);
        if($news){
            return redirect()->route('industry.index')->with('success','Industry News Updated Successfully');
        }
        else{
            abort(500);
        }
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

}
