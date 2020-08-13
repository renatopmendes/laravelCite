<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Http\Resources\Collection;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role === 9) {
            return new Collection(Notification::with([ 'user'])->orderByDesc('id')->get());
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $user = null)
    {
        if (auth()->user()->role === 9) {
            $validateData = $request->validate([
                'subject'=>'required|min:3',
                'message'=>'required|min:10'
            ]);

            if (is_null($user)) {
                $users = User::all();
                foreach ($users as $user) {
                    $validateData['user_id'] = $user->id;
                    $notification = Notification::create($validateData);
                }
            } else {
                $validateData['user_id'] = $user;
                $notification = Notification::create($validateData);
            }

            return response(['message' => 'Notificado com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        if (auth()->user()->role === 9) {
            return response(['notification' => $notification]);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display my listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        return new Collection(Notification::where('user_id', auth()->user()->id)->orderByDesc('id')->get());
    }

    /**
     * Display a listing of the resource by user.
     *
     * @return \Illuminate\Http\Response
     */
    public function user(int $user)
    {
        if (auth()->user()->role === 9) {
            return new Collection(Notification::where('user_id', $user)->orderByDesc('id')->get());
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    public function readed(Notification $notification)
    {
        if ($notification->user_id === auth()->user()->id) {
            $notification->readed_at = now();
            $notification->save();
            return response(['notification' => $notification]);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        if (($notification->user_id === auth()->user()->id || auth()->user()->role === 9) && $notification->delete()) {
            return response(['message' => 'Mensagem excluída com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function forcedestroy(Notification $notification)
    {
        if (auth()->user()->role === 9 && $notification->forceDelete()) {
            return response(['message' => 'Mensagem excluída com sucesso.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }
}
