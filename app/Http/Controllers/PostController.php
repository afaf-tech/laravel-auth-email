<?php

namespace App\Http\Controllers;


use App\Mail\Welcome;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$posts = Post::paginate(5);
    	return view( 'show-posts', compact( 'posts' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'create-posts' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
	    $user_id = auth()->id();
	    $user = User::find( $user_id );

    	$this->validate_form();
	    $category = ( $request->category ) ? $request->category : 'General';
	    $post = new \App\Post();
	    $post->title = $request->title;
	    $post->content = $request->content;
	    $post->author = $request->author;
	    $post->category = $category;
	    $post->save();

	    $message = 'Post successfully created';
	    Mail::to( $user )->send( new Welcome( $user, $message ) );

	    return redirect( '/posts/create' )->with( 'success', 'Post successfully created' );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
    	return view( 'update-post', compact( 'post' ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
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
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
	    $this->validate( request(), [      // validates the form inputs and makes $errors->all() array available to you container errors if not filled.
		    'title' => 'required',
		    'content' => 'required',
		    'author' => 'required',
		    'category' => 'required'
	    ] );

	    $post->title = $request->title;
	    $post->content = $request->content;
	    $post->author = $request->author;
	    $post->category = $request->category;
	    $post->save();

	    return redirect( "posts/$post->id" )->with( 'success', 'Post successfully updated' );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect( '/posts' )->with( 'success', 'Post Successfully deleted' );
    }

    function validate_form() {
	    $this->validate( request(), [      // validates the form inputs and makes $errors->all() array available to you container errors if not filled.
		    'title' => 'required',
		    'content' => 'required',
		    'author' => 'required',
	    ] );
    }
}
