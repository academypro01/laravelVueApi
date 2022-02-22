<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollectionResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user')->orderBy('id', 'desc')->paginate(10);

        return PostCollectionResource::collection($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['user_id' => Auth::id()]);
        $data = $this->validate($request, [
            'title' => ['required'],
            'description' => ['required'],
            'user_id' => ['required']
        ]);

        $post = Post::create($data);

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        return new PostCollectionResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if(Auth::user()->can('update', $post)) {
            $data = $this->validate($request, [
                'title' => ['required'],
                'description' => ['required']
            ]);

            $post->update($data);

            return response()->json($post);
        }else {
            return response()->json([
                'data' => [
                    'error' => "You don't have access to update this post"
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if(Auth::user()->can('delete', $post)) {
            $post->delete();

            return response()->json([
                'data' => [
                    'deleted' => 'true'
                ]
            ], 200);
        }else{
            return response()->json([
                'data' => [
                    'error' => "You don't have access to delete this post"
                ]
            ]);
        }
    }
}
