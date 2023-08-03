<?php

namespace App\Http\Controllers\Api;

use App\Policies\ArticlePolicy;;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleDetailResource;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Http\Resources\ArticleResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class ArticleApiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }
    // public function __construct()
    // {
    //     $this->middleware('verified');
    // }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $articles = Article::with(['user', 'category'])->orderBy('created_at', 'desc')->take(6)
            ->paginate(10)->withQueryString();
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article)
    {

        $this->authorize('create', $article);



        $article = Article::create([
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "description" => $request->description,
            "excerpt" => Str::words($request->description, 30, "..."),
            "category_id" => $request->category_id,
            "user_id" => Auth()->id(),
        ]);
        response()->json([
            'success' => true
        ]);

        return new ArticleDetailResource($article);
    }


    public function showCreated()
    {
        $article = Article::with(['user', 'category'])
            ->when(Auth::user()->id == Auth::id(), function ($query) {
                $query->where("user_id", Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)->withQueryString();

        // $article = Category::first()->paginate(6)->withQueryString();

        return ArticleResource::collection($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $article = Article::with(['user', 'category'])->find($id);
        if (is_null($article)) {
            return response()->json([
                // "success" => false,
                "message" => "Article not found",

            ], 404);
        }


        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        $article = Article::find($id);
        if (is_null($article)) {
            return response()->json([
                // "success" => false,
                "message" => "Article not found",

            ], 404);
        }

        if ($request->user()->cannot('update', $article)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Article",

            ], 403);
        }

        if ($request->has('title')) {
            $article->title = $request->title;
        }

        if ($request->has('slug')) {
            $article->slug = Str::slug($request->title);
        }

        if ($request->has('user_id')) {
            $article->user_id = Auth::id();
        }


        // return response()->json([
        //     // "success" => false,
        //     "error" => "Fake Updated",

        // ],200);

        $article->update();


        return new ArticleDetailResource($article);
    }


    public function trash()
    {
        $article = Article::onlyTrashed()
         ->when(Auth::user()->id == Auth::id(), function ($query) {
                $query->where("user_id", Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return ArticleResource::collection($article);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Article $article)
    {


        if (is_null($article)) {
            return response()->json([
                "success" => false,
                "message" => "Article not found",
            ], 404);
        }

        if ($request->user()->cannot('delete', $article)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Article",

            ], 403);
        }
        // $this->authorize('delete', $article);


        $article->delete();

        // return response()->json([],204);
        return response()->json([
            "message" => "Article is soft deleted",
        ]);
    }

    public function forceDelete($id, Request $request)
    {

        $article = Article::withTrashed()->find($id);

        if (is_null($article)) {
            return response()->json([
                "message" => "Article is not in trash",
            ], 404);
        }

        if ($request->user()->cannot('forceDelete', $article)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Article",

            ], 403);
        }


        if ($article) {
            if ($article->trashed()) {

                $article->forceDelete();
                return response()->json(['message' => 'Article fully delete']);

            } else {
                return response()->json(['message' => 'Article is not in trash.']);
            }
        }

        return response()->json(['message' => 'Article not found.'], 404);
    }



    public function restore($id, Request $request)
    {

        $article = Article::withTrashed()->find($id);

        if (is_null($article)) {
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ], 404);
        }

        if ($request->user()->cannot('restore', $article)) {
            return response()->json([
                // "success" => false,
                "error" => "You are not owner of the Article",

            ], 403);
        }


        if ($article) {
            if ($article->trashed()) {
                $article->restore();
                return response()->json(['message' => 'Article restored successfully.']);
            } else {
                return response()->json(['message' => 'Article is not in trash.']);
            }
        }

        return response()->json(['message' => 'Article not found.'], 404);
    }


}
