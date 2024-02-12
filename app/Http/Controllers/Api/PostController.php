<?php

namespace App\http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
//import Resource "PostResource"

use App\Http\Resources\PostResource;
//import Facade "Validator"

use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $posts = Post::latest()->paginate(5);
        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
    /**
     * store
     *
     * @param mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);
        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        //create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * show
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        $post = Post::find($id);

        return new PostResource(true, 'Data Post Ditemukan!', $post);
    }

    /**
     * update
     *
     * @param mixed $request
     * @param mixed $post
     * @return void
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        // Validate the request data
        $request->validate([
            'title' => 'required|string', // Adjust the validation rule as needed
            'content' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Update the post with the new image
            $post->update([
                'image' => $image->hashName(),
            ]);
        }

        // Update other fields of the post
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * destroy
     *
     * @param mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        return new PostResource(true, 'Data Post Berhasil Dihapus!', $post);
    }
}
