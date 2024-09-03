<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as Basecontroller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return $this->sendResponse($data,'All Post Data.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $data)
    {   
        $validateUser =  Validator::make($data->all(),[
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif',
        ]);

        if ($validateUser->fails()) {

        return $this->sendError('Validation Error',$validateUser->errors()->all());
            
        }

        $img = $data->image;
        $imageName = $img->getClientOriginalName();
        $img->move(public_path().'/uploads',$imageName);

        $post = Post::create([
            'title' => $data->title,
            'description' => $data->description,
            'image' => $imageName,
        ]);

        return $this->sendResponse($post,'Post Created Successfully');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )->where(['id' => $id])->get();

        return $this->sendResponse($data,'Your Single Post');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $data, $id)
    {
        $validateUser =  Validator::make($data->all(),[
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif',
        ]);

        if ($validateUser->fails()) {
            
            return $this->sendError('Validation Error',$validateUser->errors()->all());
        }

        $postImage = Post::select('id','image')->where(['id' => $id])->get();

        if ($data->image != '') {
            $path = public_path().'/uploads';

            if ($postImage[0]->image != '' && $postImage[0]->image != null) {
                $old_file = $path. $postImage[0]->image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $img = $data->image;
            $imageName = $img->getClientOriginalName();
            $img->move(public_path().'/uploads',$imageName);
        } else {
            $imageName = $postImage->image;
        }


        $post = Post::where(['id' => $id])->update([
            'title' => $data->title,
            'description' => $data->description,
            'image' => $imageName,
        ]);

        return $this->sendResponse($post,'Post Updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {   

        $imagePath = Post::select('image')->where('id',$id)->get();
        $filePath = public_path() . '/uploads/' . $imagePath[0]['image'];

        unlink($filePath);

        $post = Post::where('id',$id)->delete();

        return $this->sendResponse($post,'Your Post has been removed.');

    }

}