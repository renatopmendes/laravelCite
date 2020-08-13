<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Mail\Registered;
use App\Mail\Newpassword;

class AuthController extends Controller
{
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
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
            'following'=>'array',
            'document'=>'string'
            // 'role' => 'integer'
            // 'pro' => 'boolean'
        ]);
        $validateData['name'] = preg_replace('/\s{2,}/', ' ', trim($validateData['name']));
        $validateData['email'] = strtolower($validateData['email']);
        $validateData['password'] = Hash::make($validateData['password']);
        $validateData['remember_token'] = substr(md5(uniqid(rand(), true)), 0, 10);

        try {
            DB::beginTransaction();

            $user = User::create($validateData);
            $accessToken = $user->createToken('Personal Access Token')->accessToken;

            if (!empty($validateData['following'])) {
                foreach ($validateData['following'] as $value) {
                    \App\Models\Following::create([
                        'user_id' => $user->id,
                        'page_id' => $value['page'],
                        'notification' => $value['notification']
                    ]);
                }
            }

            DB::commit();

            $user = User::with(['following:user_id,page_id,notification', 'pages:id,user_id,name,avatar,link,about,fcm_topic'])->select('id', 'name', 'email', 'role', 'pro')->find($user->id);

            $us = User::find($user->id);
            Mail::to($us)->send(new Registered($us));

            return response(['user' => $user, 'access_token' => $accessToken, 'message' => 'Conta criada com sucesso!']);
        } catch (Throwable $e) {
            DB::rollback();
        }

        return response(['error' => 'Erro ao cadastrar usuário, por favor, tente novamente.'], 500);
    }

    public function tokendestroy($token)
    {
        $user = User::where('remember_token', $token)->first();
        if ($user) {
            try {
                DB::beginTransaction();

                $pages = \App\Models\Page::where('user_id', $user->id)->get();
                foreach ($pages as $page) {
                    $page->delete();
                }

                $user->forceDelete();

                DB::commit();

                echo 'Conta removida com sucesso.';
            } catch (Throwable $e) {
                DB::rollback();

                echo 'Conta não pode ser removida.';
            }
        } else {
            echo 'Usuário inexistente.';
        }

        return;
    }

    public function login(Request $request)
    {
        $validateData = $request->validate([
            'email'=>'required|min:3|max:32',
            'password'=>'required|min:8',
            'following'=>'array'
        ]);
        $validateData['email'] = strtolower($validateData['email']);

        $loginData = ['email' => $validateData['email'], 'password' => $validateData['password']];
        if (!auth()->attempt($loginData)) {
            return response(['error' => 'E-mail ou senha inválidos!'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        if (!empty($validateData['following'])) {
            foreach ($validateData['following'] as $value) {
                \App\Models\Following::create([
                        'user_id' => auth()->user()->id,
                        'page_id' => $value['page'],
                        'notification' => $value['notification']
                    ]);
            }
        }

        $user = User::with(['following:user_id,page_id,notification', 'pages:id,user_id,name,avatar,link,about,fcm_topic', 'notifications:id,user_id,subject,message,readed_at'])->select('id', 'name', 'email', 'role', 'pro')->find(auth()->user()->id);

        return response(['user' => $user, 'access_token' => $accessToken, 'message' => 'Logado com sucesso!']);
    }

    public function infos()
    {
        if (auth()->user()->role === 9) {
            $users = User::count();
            $pages = \App\Models\Page::count();
            $posts = \App\Models\Post::count();

            return response(['users' => $users, 'pages' => $pages, 'posts' => $posts]);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        return response(['user' => User::with(['following:user_id,page_id,notification', 'pages:id,user_id,name,avatar,link,about'])->select('id', 'name', 'email', 'role', 'pro')->find(auth()->user()->id)]);
    }

    public function pro()
    {
        if (User::where('id', auth()->user()->id)->update(['pro' => 1])) {
            return response(['message' => 'Parabéns!!!']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * Change password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validateData = $request->validate([
            'oldpassword'=>'required|min:6',
            'password'=>'required|min:6'
        ]);

        $user = User::find(auth()->user()->id);
        if (Hash::check($validateData['oldpassword'], $user->password)) {
            if ($user->update(['password' => Hash::make($validateData['password'])])) {
                return response(['message' => 'Senha atualizada.']);
            }

            return response(['error' => 'Erro. Por favor, tente novamente.'], 500);
        }

        return response(['error' => 'Senha incorreta.'], 401);
    }

    public function recovery(Request $request)
    {
        $validateData = $request->validate([
            'email'=>'required|email'
        ]);

        if (User::where('email', $validateData['email'])->update(['remember_token' => md5(uniqid(rand(), true))])) {
            //TODO: ENVIAR EMAIL
            return response(['message' => 'E-mail enviado']);
        }

        return response(['error' => 'Erro. Por favor, tente novamente.'], 500);
    }

    public function newPassword(Request $request, $token)
    {
        $validateData = $request->validate([
            'password'=>'required|min:6'
        ]);

        if (User::where('remember_token', $token)->update(['password' => Hash::make($validateData['password'])])) {
            //TODO: ENVIAR RESPOSTA
            echo 'Senha atualizada';
        } else {
            echo 'Erro. Por favor, tente novamente.';
        }

        return;
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->validate([
            'email'=>'required|email'
        ]);

        $newpassword = substr(md5(uniqid(rand(), true)), 0, 8);
        $user = User::where('email', $email);
        if ($user) {
            if ($user->update(['password' => Hash::make($newpassword)])) {
                $us = $user->first();
                $us->password = $newpassword;

                Mail::to($us)->send(new Newpassword($us));

                return response(['message' => 'A nova senha foi enviada ao seu e-mail. Verifique a caixa de SPAM.']);
            }
        }

        return response(['error' => 'Este e-mail não existe.'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (auth()->user()->role === 9) {
            $user->delete();

            return response(['message' => 'Conta removida soft.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    public function forcedestroy(Request $request)
    {
        $validateData = $request->validate([
            'password'=>'required|min:6'
        ]);

        $user = User::find(auth()->user()->id);
        if (Hash::check($validateData['password'], $user->password)) {
            try {
                DB::beginTransaction();

                $pages = \App\Models\Page::where('user_id', $user->id)->get();
                foreach ($pages as $page) {
                    $page->delete();
                }

                $user->forceDelete();

                DB::commit();

                return response(['message' => 'Conta removida.']);
            } catch (Throwable $e) {
                DB::rollback();
            }
            return response(['error' => 'Conta não pode ser removida.'], 500);
        }

        return response(['error' => 'Senha incorreta.'], 401);
    }

    public function punish(User $user)
    {
        if (auth()->user()->role === 9) {
            $days = $user->punished_days > 0 ? $user->punished_days * 2 : 3;

            if ($days > 20) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'subject' => 'Exclusão por violação das regras',
                    'message' => 'Olá. Você foi punido por reiscindir e violar as regras. Nunca mais poderá postar em suas páginas.'
                ]);

                $user->delete();

                return response(['message' => 'Usuário removido.']);
            } else {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'subject' => 'Punição por violação das regras',
                    'message' => 'Olá. Você foi punido por violar as regras. Não poderá postar dentro de ' . $days . ' dias. Não reiscinda para não correr o risco de perder a sua conta.'
                ]);

                $user->punished_at = now()->add($days, 'days');
                $user->punished_days = $days;
                $user->save();
            }

            return response(['message' => 'Usuário punido, não postará por ' . $days . ' dias.']);
        }

        return response(['error' => 'Unauthorized'], 401);
    }
}
