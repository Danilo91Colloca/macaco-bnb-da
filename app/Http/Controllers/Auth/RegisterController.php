<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
    protected $redirectTo = RouteServiceProvider::DASHBOARD;

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
        $validator= Validator::make($data, [
            'name' => ['required', 'string', 'max:64'],
            'surname' => ['required', 'string', 'max:64'],
            'birth' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        //queste verifiche andranno a stampare i dati caricati dell'utente nel mio file log userRegistration.log in storage/logs/userRegistration
        if(strlen($data['name']) < 3){
            Log::channel('userRegistration')->info(date("m/d/Y h:i:s a", time()) . 'IP Utente: ' . $_SERVER['REMOTE_ADDR'] . ' ' . 'ERRORE INSERIMENTO l\'utente ha inserito un nome inferiore a 3 cifre: ' . $data['name']);
        }
        if(strlen($data['surname']) < 3){
            Log::channel('userRegistration')->info(date("m/d/Y h:i:s a", time()) . 'IP Utente: ' . $_SERVER['REMOTE_ADDR'] . ' ' . 'ERRORE INSERIMENTO: l\'utente ha inserito un cognome inferiore a 3 cifre: ' . $data['surname']);
        }
        if(strlen($data['birth']) > 10 || strlen($data['birth']) < 10 ) {
            Log::channel('userRegistration')->info(date("m/d/Y h:i:s a", time()) . 'IP Utente: ' . $_SERVER['REMOTE_ADDR'] . ' ' . 'ERRORE INSERIMENTO:: la data inserita dall\'utente non è valida: ' . $data['birth']);
        }
        if(strpos($data['email'], '.it')== false && strpos($data['email'], '.com') == false && strpos($data['email'], '.net') == false) {
            Log::channel('userRegistration')->info(date("m/d/Y h:i:s a", time()) . 'IP Utente: ' . $_SERVER['REMOTE_ADDR'] . ' ' . 'ERRORE INSERIMENTO: l\'utente non ha inserito una mail valida: ' . $data['email']);
        }
        if(strlen($data['password']) < 8){
            Log::channel('userRegistration')->info(date("m/d/Y h:i:s a", time()) . 'IP Utente: ' . $_SERVER['REMOTE_ADDR'] . ' ' . 'ERRORE INSERIMENTO: l\'utente ha inserito una password inferiore alle 8 cifre');
        }
        return $validator;

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'birth' => $data['birth'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
