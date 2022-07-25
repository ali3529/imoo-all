<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\EmailTakenException;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\OAuthProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        config([
            'services.github.redirect'   => route('oauth.callback', 'github'),
            'services.google.redirect'   => route('oauth.callback', 'google'),
            'services.apple.redirect'    => route('oauth.callback', 'apple'),
            'services.twitter.redirect'  => route('oauth.callback', 'twitter'),
            'services.facebook.redirect' => route('oauth.callback', 'facebook'),
        ]);
    }

    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider)
    {
        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()
                ->getTargetUrl(),
        ]);
    }

    /**
     * Obtain the user information from the provider.
     */
    public function handleCallback(string $provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        $user = $this->findOrCreateUser($provider, $user);

        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );

        return view('oauth/callback', [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->getPayload()->get('exp') - time(),
        ]);
    }

    /**
     * Find or create a user.
     */
    protected function findOrCreateUser(
        string $provider,
        SocialiteUser $user
    ): Account {
        $oauthProvider = OAuthProvider::where('provider', $provider)
            ->where('provider_account_id', $user->getId())
            ->first();

        if ($oauthProvider) {
            $oauthProvider->update([
                'access_token'  => $user->token,
                'refresh_token' => $user->refreshToken,
            ]);

            return $oauthProvider->account;
        }

        $account = Account::where('email', $user->getEmail())->first();
        if ($account) {
            return $account;
//            throw new EmailTakenException;
        }

        return $this->createUser($provider, $user);
    }

    /**
     * Create a new user.
     */
    protected function createUser(
        string $provider,
        SocialiteUser $sUser
    ): Account {
        $user = Account::create([
            'first_name'   => $sUser->getName(),
//            'last_name' => $sUser->getNickname(),
            'email'        => $sUser->getEmail()
        ]);
        $user->markEmailAsVerified();
        $user->makeUsername();

        $user->oauthProviders()->create([
            'provider'            => $provider,
            'provider_account_id' => $sUser->getId(),
            'access_token'        => $sUser->token,
            'details'             => $sUser->getAvatar(),
            'refresh_token'       => $sUser->refreshToken,
        ]);

        return $user;
    }
}
