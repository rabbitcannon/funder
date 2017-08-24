<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Gumdrop;
use App\User;
use Validator;

class GumdropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // index returns all the whole gumdrops in the world
        return response()->json(Gumdrop::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // do some validation
        $validator = Validator::make( $request->all(),
            ['name' => 'required', 'color' => 'required']);
        if( $validator->fails() )
        {
            return response()->json( ['error' => 'VALIDATION',
                'message' => $validator->errors()->messages()] , 400 );
        }
        // store me a new gumdrop for some user
        $user_id = $request->get('user_id');
        $name = $request->get('gumdrop_name');
        $color = $request->get('gumdrop_color');
        $gumdrop = new Gumdrop(['name' => $name, 'color' => $color]);
        $gumdrop->save();
        // better link it to my user!
        $user = User::findOrFail($user_id);
        DB::table('gumdrop_user')->insert([
            'gumdrop_id' => $gumdrop->id,
            'user_id' => $user->id]);
        // woot! nailed it
        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gumdropsForUser(Request $request, $user_id)
    {
        // awesomely, I get $user_id from my route
        $user = User::findOrFail($user_id);
        // now I gotta go find all his gumdrops
        $gumdrops = Gumdrop::all();
        $my_gumdrops = $user->gumdrops()->get();
        return response()->json($my_gumdrops);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // do some validation
        $validator = Validator::make( $request->all(),
            ['name' => 'required', 'color' => 'required']);
        if( $validator->fails() )
        {
            return response()->json( ['error' => 'VALIDATION',
                'message' => $validator->errors()->messages()] , 400 );
        }
        // that's my little gumdrop
        $gumdrop = Gumdrop::findOrFail($id);
        $name = $request->get('gumdrop_name');
        $color = $request->get('gumdrop_color');
        $gumdrop->update( ['name' => $name, 'color' => $color ] );
        return response()->json( $gumdrop );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // goodbye my little gumdrop
        $gumdrop = Gumdrop::find($id);
        $gumdrop->delete();
        return response()->json([]);
    }
}
