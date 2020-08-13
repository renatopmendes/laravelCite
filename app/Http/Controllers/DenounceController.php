<?php

namespace App\Http\Controllers;

use App\Models\Denounce;
use App\Models\Page;
use App\Http\Resources\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DenounceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $qty, int $index = 0)
    {
        if (auth()->user()->role === 9) {
            return new Collection(Denounce::with(['page', 'user', 'denouncer'])->skip($index * $qty)->take($qty)->orderByDesc('id')->get());
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Page $page)
    {
        $validateData = $request->validate([
            'denounce'=>'required',
        ]);
        $validateData['user_id'] = $page->user_id;
        $validateData['page_id'] = $page->id;

        if (Auth::guard('api')->check()) {
            if ($page->user_id !== Auth::guard('api')->user()->id) {
                $validateData['denouncer_id'] = Auth::guard('api')->user()->id;
            } else {
                return response(['error' => 'Não pode denunciar a si mesmo.'], 403);
            }
        }

        $denounce = Denounce::create($validateData);

        return response(['message' => 'Recebemos sua denúncia e a analisaremos. Obrigado!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Denounce  $denounce
     * @return \Illuminate\Http\Response
     */
    public function show(Denounce $denounce)
    {
        if (auth()->user()->role === 9) {
            $denounce->load(['page', 'user', 'denouncer']);
            return response(['denounce' => $denounce]);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Denounce  $denounce
     * @return \Illuminate\Http\Response
     */
    public function destroy(Denounce $denounce)
    {
        if (auth()->user()->role === 9 && $denounce->delete()) {
            return response(['message' => 'Denúncia excluída com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }
}
