<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\App\models\page $page, int $qty, int $index = 0)
    {
        $posts = Post::select('id', 'page_id', 'color', 'family', 'textSize', 'message', 'youtube', 'image', 'video', 'commentary', 'views')->where('page_id', $page->id)->skip($index * $qty)->take($qty)->orderByDesc('id')->get();

        if (!(Auth::guard('api')->check() && $page->user_id === Auth::guard('api')->user()->id)) {
            $page->views = $page->views + count($posts);
            $page->save();

            foreach ($posts as $post) {
                $post->views = $post->views + 1;
                $post->save();
            }
        }

        return new Collection($posts);
    }

    public function search(string $q, int $qty, int $index = 0)
    {
        $posts = Post::with(['page:id,name,avatar,views'])->select('id', 'page_id', 'page_id', 'color', 'family', 'textSize', 'message', 'youtube', 'image', 'video', 'commentary', 'views');
        // if (Auth::guard('api')->check()) {
        //     $myPages = \App\Models\Page::select('id')->where('user_id', Auth::guard('api')->user()->id)->get();
        //     if (count($myPages)) {
        //         $posts = $posts->whereNotIn('page_id', $myPages);
        //     }
        // }
        $posts = $posts->where('message', 'like', '%' . $q . '%')->orWhere('commentary', 'like', '%' . $q . '%')->skip($index * $qty)->take($qty)->orderByDesc('id')->get();

        foreach ($posts as $key => $post) {
            $post->page->views = $post->page->views + 1;
            $post->page->save();

            $post->views = $post->views + 1;
            $post->save();
        }

        $pages = [];
        if ($index === 0) {
            $pages = \App\Models\Page::select('id', 'avatar', 'name')->where('name', 'like', '%' . $q . '%')->orderByDesc('id')->get();
        }
        return response(['pages' => $pages, 'posts' => $posts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Models\Page $page)
    {
        $page->load('user');
        if ($page->user->punished_at > now()) {
            return response(['error' => 'Por sua punição você ainda não pode postar.'], 401);
        }

        if ($page->user_id === auth()->user()->id) {
            $validateData = $request->validate([
                'color'=>'nullable|String',
                'family'=>'nullable|String',
                'textSize'=>'nullable|String',
                'message'=>'nullable|max:512',
                'youtube'=>'nullable|String',
                'image'=>array('nullable', base64_encode(base64_decode($request->image, true)) === $request->image),
                'video'=>'nullable|String',
                'commentary'=>'nullable|max:255'
            ]);
            $validateData['page_id'] = $page->id;

            if (isset($validateData['message'])) {
                $validateData['message'] = trim($validateData['message']);
            }
            if (isset($validateData['commentary'])) {
                $validateData['commentary'] = preg_replace('/\s{2,}/', ' ', trim($validateData['commentary']));
            }

            if (isset($validateData['image'])) {
                $image_64 = $validateData['image'];
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
                $validateData['image'] = 'image.' . now()->isoFormat('X') . '.' . $extension;
            }

            if (isset($validateData['video'])) {
                $video_64 = $validateData['video'];
                $extension = explode('/', explode(':', substr($video_64, 0, strpos($video_64, ';')))[1])[1];   // .jpg .png .pdf
                $validateData['video'] = 'video.' . now()->isoFormat('X') . '.' . $extension;
            }

            try {
                DB::beginTransaction();

                $page->posted_at = now();
                $page->save();

                $post = Post::create($validateData);

                if (isset($validateData['image'])) {
                    \App\Http\Helpers::save64('pages/' . $page->id . '/' . $post->id . '/' . $post->image, $image_64);
                }

                if (isset($validateData['video'])) {
                    \App\Http\Helpers::save64('pages/' . $page->id . '/' . $post->id . '/' . $post->video, $video_64);
                }

                DB::commit();

                return response(['post' => $post, 'message' => 'Post criado com sucesso.']);
            } catch (Throwable $e) {
                DB::rollback();
            }
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response(['post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->load('page');
        if ($post->page->user_id === auth()->user()->id || auth()->user()->role === 9) {
            $post->delete();

            return response(['message' => 'Removido com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }
}
