<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);
        $blogs = Blog::all();
        return view('admin.blog.index', compact('blogs','filesWithParents'));
    }
    public function Create()
    {
        return view('admin.blog.create');
    }
    
    public function Store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $directory = 'public/media/files/industry_news';
    
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
    
        $imageFile = $request->file('image');
        $path = $imageFile->store($directory);
    
        $newPath = str_replace("public/", "", $path);
    
        $data = [
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'blog_category_id' => $request->category_id,
            'metatitle' => $request->metatitle,
            'metadescription' => $request->metadescription,
            'metakeywords' => $request->metakeywords,
            'status' => $request->status,
            'image_path' => $newPath,
        ];
    
        $blog = Blog::create($data);
    
        if ($blog) {
            return redirect()->route('blog.index')->with('success', 'Blog Added Successfully');
        } else {
            abort(500);
        }
    }
    public function edit(Request $request, $id)
    {
        $blog = Blog::where('id', $id)->first();
        return view('admin.blog.edit', compact('blog'));
    }
    public function update(Request $request, $id)
    {
        $blog = Blog::where('id', $id)->first();
        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageFile = $request->file('image');

            $path = $imageFile->store('public/media/files/industry_news');
            $newPath = str_replace("public/", "", $path);
        } else {
            $newPath = $blog->image_path;
        }
        $data = [
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'blog_category_id' => $request->category_id,
            'metatitle' => $request->metatitle,
            'metadescription' => $request->metadescription,
            'metakeywords' => $request->metakeywords,
            'status' => $request->status,
            'image_path' => $newPath,
        ];
        $blog->update($data);
        if ($blog) {
            return redirect()->route('blog.index')->with('success', 'Blog Updated Successfully');
        } else {
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
