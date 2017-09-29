<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Gumdrop;
use App\User;
use Illuminate\Validation\Validator;

class GumdropController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/gumdrops",
     *   summary="Get all the gumdrops",
     *   operationId="getAllGumdrops",
     *   tags={"gumdrops"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/Gumdrop"))
     *   ),
     *   @SWG\Response(response=500, description="System error")
     *  )
     **/
    public function index()
    {
        // index returns all the whole gumdrops in the world
        return response()->json(Gumdrop::all());
    }

    /**
     * @SWG\Post(
     *   path="/gumdrops",
     *   summary="Add a new gumdrop",
     *   operationId="createGumdrop",
     *   tags={"gumdrops"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Gumdrop Parameters.",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/GumdropAndUser")
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=400, description="Validation error"),
     *   @SWG\Response(response=404, description="User not found"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     **/

    public function store(Request $request)
    {
        // do some validation
        $this->validate( $request,
            ['name' => 'required', 'color' => 'required']);

        // store me a new gumdrop for some user
        $user_id = $request->get( 'user_id' );
        $name = $request->get( 'name' );
        $color = $request->get( 'color' );
        $gumdrop = new Gumdrop( ['name' => $name, 'color' => $color] );
        $gumdrop->save();
        // link it to my user!
        $user = User::findOrFail( $user_id );
        $user->gumdrops()->attach($gumdrop);
        return response()->json([]);
    }

    /**
     * @SWG\Get(
     *   path="/users/{id}/gumdrops",
     *   summary="Get gumdrops for a user",
     *   operationId="getUserGumdrops",
     *   tags={"users"},
     * @SWG\Parameter(
     *   in="path",
     *   name="id",
     *   description="User Id",
     *   required=true,
     *   type="integer",
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/Gumdrop"))
     *   ),
     * @SWG\Response(response=404, description="User not found"),
     * @SWG\Response(response=500, description="System error")
     *  )
     **/
    public function gumdropsForUser(Request $request, $user_id)
    {
        // awesomely, I get $user_id from my route.
        // another way I can do this is to inject the user:
        // public function gumdropsForUser(Request $request, User $user)
        // that way the route actually does this findOrFail.
        // be aware that doesn't work for phpunit if you drop the middleware, though.
        $user = User::findOrFail($user_id);
        // now go find all his gumdrops
        $my_gumdrops = $user->gumdrops()->get();
        return response()->json($my_gumdrops);
    }

    /**
     * @SWG\Put(
     *   path="/gumdrops/{id}",
     *   summary="Modify a gumdrop",
     *   operationId="modifyGumdrop",
     *   tags={"gumdrops"},
     * @SWG\Parameter(
     *   in="path",
     *   name="id",
     *   description="Gumdrop Id",
     *   required=true,
     *   type="integer",
     *   ),
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Gumdrop Parameters.",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Gumdrop")
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=400, description="Validation error"),
     *   @SWG\Response(response=404, description="Gumdrop not found"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     **/
    public function update(Request $request, $id)
    {
        // do some validation
        $this->validate( $request,
            ['name' => 'required', 'color' => 'required']);
        // that's my little gumdrop
        $gumdrop = Gumdrop::findOrFail($id);
        $name = $request->get('gumdrop_name');
        $color = $request->get('gumdrop_color');
        $gumdrop->update( ['name' => $name, 'color' => $color ] );
        return response()->json( $gumdrop );
    }

    /**
     * @SWG\Delete(
     *   path="/gumdrops/{id}",
     *   summary="Delete a gumdrop",
     *   operationId="deleteGumdrop",
     *   tags={"gumdrops"},
     * @SWG\Parameter(
     *   in="path",
     *   name="id",
     *   description="Gumdrop Id",
     *   required=true,
     *   type="integer",
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=404, description="Gumdrop not found"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     **/
    public function destroy($id)
    {
        // goodbye my little gumdrop
        $gumdrop = Gumdrop::find($id);
        $gumdrop->delete();
        return response()->json([]);
    }
}
