<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Resources\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Http\Helpers;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function favorites(int $qty, int $posts, int $index = 0, string $whereIn = null)
    {
        $pages = Page::has('posts')->select('id', 'name', 'avatar');
        if (Auth::guard('api')->check()) {
            $myPages = Page::select('id')->where('user_id', Auth::guard('api')->user()->id)->get();
            if (count($myPages)) {
                $pages = $pages->whereNotIn('id', $myPages);
            }
        }
        if (!is_null($whereIn)) {
            $pages = $pages->whereIn('id', explode(',', $whereIn));
        }
        $pages = $pages->skip($index * $qty)->take($qty)->orderByDesc('posted_at')->get();

        if (count($pages)) {
            foreach ($pages as $page) {
                $page->load(['posts' => function ($query) use ($posts) {
                    $query->select('id', 'page_id', 'color', 'family', 'textSize', 'message', 'youtube', 'image', 'video', 'commentary', 'views')->take($posts)->orderByDesc('id');
                }]);

                foreach ($page->posts as $post) {
                    $post->views = $post->views + 1;
                    $post->save();
                }
            }
        }

        return new Collection($pages);
    }

    public function recommended(int $qty, int $posts, int $index = 0, string $whereNotIn = null)
    {
        $pages = Page::has('posts')->select('id', 'name', 'avatar');
        // if (Auth::guard('api')->check()) {
        //     $myPages = Page::select('id')->where('user_id', Auth::guard('api')->user()->id)->get();
        //     if (count($myPages)) {
        //         $pages = $pages->whereNotIn('id', $myPages);
        //     }
        // }
        if (!is_null($whereNotIn)) {
            $pages = $pages->whereNotIn('id', explode(',', $whereNotIn));
        }
        $pages = $pages->skip($index * $qty)->take($qty)->orderByDesc('posted_at')->get();

        if (count($pages)) {
            foreach ($pages as $page) {
                $page->load(['posts' => function ($query) use ($posts) {
                    $query->select('id', 'page_id', 'color', 'family', 'textSize', 'message', 'youtube', 'image', 'video', 'commentary', 'views')->take($posts)->orderByDesc('id');
                }]);

                foreach ($page->posts as $post) {
                    $post->views = $post->views + 1;
                    $post->save();
                }
            }
        }

        return new Collection($pages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name'=>'required|min:3|max:32',
            'avatar'=>array(base64_encode(base64_decode($request->avatar, true)) === $request->avatar),
            'link'=>'nullable|URL',
            'about'=>'nullable|max:2056'
        ]);
        $validateData['user_id'] = auth()->user()->id;
        $validateData['name'] = preg_replace('/\s{2,}/', ' ', trim($validateData['name']));
        if (!is_null($validateData['about'])) {
            $validateData['about'] = trim($validateData['about']);
        }

        $image_64 = $validateData['avatar'];
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $validateData['avatar'] = 'avatar.' . now()->isoFormat('X') . '.' . $extension;

        $page = Page::create($validateData);

        Helpers::save64('pages/' . $page->id . '/' . $page->avatar, $image_64);

        return response(['page' => $page, 'message' => 'Página criada com sucesso.']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page, int $posts = 0)
    {
        if ($posts) {
            $page->load('posts')->take($posts)->orderByDesc('id');
        }

        return response(['page' => $page]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        if ($page->user_id === auth()->user()->id) {
            $validateData = $request->validate([
                'name'=>'min:3|max:32',
                'avatar'=>array(base64_encode(base64_decode($request->avatar, true)) === $request->avatar),
                'link'=>'nullable|URL',
                'about'=>'nullable|max:2056'
            ]);
            $validateData['name'] = preg_replace('/\s{2,}/', ' ', trim($validateData['name']));
            if (!is_null($validateData['about'])) {
                $validateData['about'] = trim($validateData['about']);
            }

            if (isset($validateData['avatar'])) {
                $image_64 = $validateData['avatar'];
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
                $validateData['avatar'] = 'avatar.' . now()->isoFormat('X') . '.' . $extension;

                Helpers::delete('pages/' . $page->id . '/' . $page->avatar);
            }

            $page->update($validateData);

            if (isset($validateData['avatar'])) {
                Helpers::save64('pages/' . $page->id . '/' . $page->avatar, $image_64);
            }

            return response(['page' => $page, 'message' => 'Página atualizada com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        if ($page->user_id === auth()->user()->id || auth()->user()->role === 9) {
            if (Page::where('user_id', auth()->user()->id)->count() > 1 || auth()->user()->role === 9) {
                $page->delete();

                return response(['message' => 'Página removida com sucesso.']);
            } else {
                return response(['error' => 'Não pode excluir a única página.'], 403);
            }
        }

        return response(['error' => 'Unauthorized'], 401);
    }
}
