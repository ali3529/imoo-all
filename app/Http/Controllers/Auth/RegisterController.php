<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, Account $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['status' => trans('verification.sent')]);
        } else {
            $user->sendEmailVerificationNotification();
        }
        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );
        $user['token'] = $token;
        return Response::success('', $user);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|max:25',
            'email'      => 'required|email:filter|max:255|unique:re_accounts',
            'password'   => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data): Account
    {
        $account = Account::create([
            'email'      => $data['email'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'],
            'password'   => bcrypt($data['password']),
        ]);
        $account->makeUsername();
        return $account;
    }
}
