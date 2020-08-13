<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Http\Resources\Collection;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $page)
    {
        return new Collection(Following::where('page_id', $page)->orderByDesc('id')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $page)
    {
        $thepage = \App\Models\Page::find($page);
        if ($thepage->user_id !== auth()->user()->id) {
            Following::where('page_id', $page)->where('user_id', auth()->user()->id)->delete();

            $following = Following::create([
            'user_id' => auth()->user()->id,
            'page_id' => $page
        ]);

            return response(['message' => 'Seguindo']);
        }

        return response(['error' => 'Você não pode seguir a própria página'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Following  $following
     * @return \Illuminate\Http\Response
     */
    public function show(int $user)
    {
        return new Collection(Following::where('user_id', $user)->orderByDesc('id')->get());
    }

    public function notifications()
    {
        return new Collection(Following::with('page:id,fcm_topic')->where('user_id', auth()->user()->id)->where('notification', 1)->select('id', 'user_id', 'page_id', 'notification')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $page)
    {
        $validateData = $request->validate([
            'notification'=>'boolean',
        ]);

        Following::where('page_id', $page)->where('user_id', auth()->user()->id)->update(['notification' => $validateData['notification']]);

        if ($validateData['notification'] === 0) {
            return response(['message' => 'Notificações desta página estão canceladas.']);
        }
        return response(['message' => 'Notificações desta página estão ativas.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Following  $following
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $page)
    {
        if (Following::where('page_id', $page)->where('user_id', auth()->user()->id)->delete()) {
            return response(['message' => 'Deixou de seguir']);
        }

        return response(['error' => 'Erro ao deixar de seguir.'], 500);
    }
}
