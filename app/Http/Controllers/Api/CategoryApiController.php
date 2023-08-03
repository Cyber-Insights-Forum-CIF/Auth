<?php

namespace App\Http\Controllers\Api;
use App\Policies\CategoryPolicy;
namespace App\Http\Controllers\Api;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Http\Resources\CategoryResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\RedirectResponse;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //  public function __construct()
    //  {
    //      $this->authorizeResource(Category::class, 'category');
    //  }



    public function index()

    {
        $category = Category::with(['user','articles'])
        ->when(Auth::user()->role !== 'admin', function ($query) {
            $query->where("user_id", Auth::id());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)->withQueryString();

        // $category = Category::first()->paginate(6)->withQueryString();

        return CategoryResource::collection($category);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Category $category)
    {


        $this->authorize('create', $category);


        $category = Category::create([
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "user_id" => Auth::id(),
        ]);
        response()->json([
            'success' => true
        ]);
        return new CategoryDetailResource($category);

    }



    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $category = Category::with(['user','articles'])->find($id);
        if(is_null($category)){
            return response()->json([
                // "success" => false,
                "message" => "Category not found",

            ],404);
        }

        if ($request->user()->cannot('update', $category)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Category",

            ],403);
        }

        // $this->authorize('update', $category);

       return new CategoryResource($category);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( string $id, Request $request)
    {

        $category = Category::find($id);
        if(is_null($category)){
            return response()->json([
                // "success" => false,
                "message" => "Category not found",
            ],404);
        }

        if ($request->user()->cannot('update', $category)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Category",

            ],403);
        }

        // $this->authorize('update', $category);

        return response()->json([
            "success" => "Fake Update",
            "message" => "Ok",
        ]);

        if($request->has('title')){
            $category->title = $request->title;
        }

        if($request->has('slug')){
            $category->slug = Str::slug($request->title);
        }

        if($request->has('user_id')){
            $category->user_id = Auth::id();
        }
        $category->update();


        return new CategoryDetailResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Category $category, Request $request)
    {

        if(is_null($category)){
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ],404);
        }


        if ($request->user()->cannot('update', $category)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Category or Admin",

            ],403);
        }

        $this->authorize('delete', $category);

        // return response()->json([
        //     // "success" => false,
        //     "success" => "အောင်မြင်ပါတယ်။",
        //     "message" => "Fake Delete",
        // ],200);


        $category->delete();

        // return response()->json([],204);
        return response()->json([
            "message" => "Category is deleted",
        ]);
    }
}

