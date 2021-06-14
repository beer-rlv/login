<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRiquest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return View
     */
    public function showLogin()
    {
        return view('login.login_form');
    }

    /**
     * param App\Http\Requests\LoginFormRiquest
     $request
     */
    public function login(LoginFormRiquest $request)
    {
        $credentials = $request->only('email','password');

        //①アカウントがロックされていたら弾く
        $user = $this->user->getUSerByEmail($credentials['email']);
        
        if (!is_null($user)){
            if($this->user->isAccountLocked($user)){
                return back()->withErrors([
                'login_error' => 'アカウントがロックされています。',
                ]);   
            }

            if (Auth::attempt($credentials)){
                $request->session()->regenerate();
                //②成功したらエラーカウントを0にする
                $this->user->resetErrorCount($user);
                    $user->save();

                return redirect()->route('home')->with('login_success','ログイン成功しました★');
            }

            //③ログイン失敗したらエラーカウントを1増やす
            $user->error_count = $this->user->addErrorCount($user->error_count);
            //④エラーカウントが6以上の場合はアカウントをロックする
            if($this->user->lockAccount($user)){
                return back()->withErrors(['login_error' => 'アカウントがロックされました。解除したい場合は運営に連絡してください。',
                ]);
            }
            $user->save();
        }

        return back()->withErrors([
            'login_error' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login.show')->with('logout','ログアウトしました！');
    }
}
