<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Angler;
use Fishinglog\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Validation rules
    protected $rules = [
        'date' => 'date|required',
        'description' => 'string|required',
        'expeditions_id' => 'integer|required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $post = new Post;

        $expedition = \Fishinglog\Expedition::find($request->expeditions_id);

        return view('expedition.post.create', [
            'post' => $post,
            'expedition' => $expedition,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate($this->rules);

        $post = new Post;
        $post->date = $request->date;
        $post->description = $request->description;
        $post->expeditions_id = $request->expeditions_id;
        $post->anglers_id = Angler::where('user_id', Auth::user()->id)->first()->id;

        $post->save();

        return redirect()->back(); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
