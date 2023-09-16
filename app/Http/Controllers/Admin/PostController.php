<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::query()->latest()->paginate();
        return view('admin.post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::query()->latest()->get();
        return view('admin.post.create',compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = auth()->user()->posts()->create([
            'title'=>$request->input('title'),
            'description'=>$request->input('description'),
            'image'=>$request->input('image'),
        ]);

        $post->tags()->attach($request->input('tags'));

        return redirect(route('post.index'))->with('message','new post has been created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $tags = Tag::query()->latest()->get();
        return view('admin.post.edit',compact('tags','post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $post->update([
            'title'=>$request->input('title'),
            'description'=>$request->input('description'),
            'image'=>$request->input('image'),
        ]);
        $post->tags()->sync($request->input('tags'));
        return redirect(route('post.index'))->with('message', 'the post has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->tags()->detach();
        $post->comments()->delete();
        $post->delete();
        return redirect(route('post.index'))->with('message','the post has been deleted');
    }
}
