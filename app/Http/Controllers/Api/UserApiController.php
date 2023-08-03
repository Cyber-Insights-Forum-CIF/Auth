<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Models\Category;
use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
use Illuminate\Support\Facades\Gate;

class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {




        $users = User::with(['articles','categories'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();;
        return UserResource::collection($users);
        // return User::findOrFail(12)->categories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $request->validate(([
        "name" => "required",
        "email" => "required",
        "phone" => "nullable",
        "address" => "nullable",
        "role" => "required",
        "membership" => "nullable",
        "gender" => "required",
        "password" => "required",
      ]));

      $user = User::create([
        "name" => $request->name,
        "email" => $request->email,
        "phone" => $request->phone,
        "address" => $request->address,
        "role" => $request->role,
        "membership" => $request->membership,
        "gender" => $request->gender,
        "password" =>  $request->password,

      ]);

      return new UserDetailResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['categories','articles'])->find($id);
        if(is_null($user)){
            return response()->json([
                // "success" => false,
                "message" => "User not found",

            ],404);
        }

        // return response()->json([
        //     "data" => $user
        // ]);
        return new UserDetailResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


      $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                // "success" => false,
                "message" => "User not found",
            ],404);
        }
        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('email')){
            $user->email = $request->email;
        }

        if($request->has('phone')){
            $user->phone = $request->phone;
        }

        if($request->has('address')){
            $user->address = $request->address;
        }
        if($request->has('role')){
            $user->role = $request->role;
        }

        if($request->has('membership')){
            $user->membership = $request->membership;
        }

        if($request->has('gender')){
            $user->gender = $request->gender;
        }

        if($request->has('password')){
            $user->password = $request->password;
        }

        $user->update();


        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                // "success" => false,
                "message" => "User not found",

            ],404);
        }
        $user->delete();

        // return response()->json([],204);
        return response()->json([
            "message" => "User is deleted",
        ]);
    }
}
