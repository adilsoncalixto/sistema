<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';


    public function login(Request $request)
    {
        if ($request->method() == 'GET') {

            $erro = false;

        return view('login',compact('erro'));
    }
        else {
            $username = $request->input('email');
            $password = $request->input('password');

            /*if ($username != 'admin@dominio.com.br' && $username != 'equipe@dominio.com.br') {
                $erro = true;
                return view('login',compact('erro'));

            }*/


            if (Auth::attempt(['email' => $username, 'password' => $password])) {

                // Authentication passed...
                //return redirect()->intended('dashboard');
                return redirect($this->redirectTo);
            } else {
                $erro = true;
                return view('login',compact('erro'));
            }
        }
    }

    public  function postlogin () {


    }

    public function logout()
    {
        Auth::logout();
        return redirect('/auth/login');
    }
}
