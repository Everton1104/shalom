<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $response = $this->requestAPI([
            'method' => 'POST',
            'url' => 'https://www.google.com/recaptcha/api/siteverify',
            'conteudo' => [
                'secret' => env("RECAPTCHA_SECRET"),
                'response' => $data["recaptcha_token"]
            ],
        ]);
        if (json_decode($response)->success) {
            return Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }
        return redirect()->back()->with('erroMsg', 'Marque a opção não sou um robô.');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function requestAPI(array $dados = ['method' => 'GET', 'url' => '', 'conteudo' => ['']])
    {
        $context  = stream_context_create(
            array(
                'http' =>
                array(
                    'method'  => $dados['method'],
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query($dados['conteudo'])
                )
            )
        );
        return file_get_contents($dados['url'], false, $context);
    }
}
