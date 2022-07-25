<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\VerifyMobile;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('throttle:6,1')
            ->only('verify', 'resend', 'verifyPhone');
    }

    /**
     * Mark the user's email address as verified.
     */
    public function verify(Request $request, Account $user)
    {
        if (!URL::hasValidSignature($request)) {
            return response()->json([
                'status' => trans('verification.invalid'),
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => trans('verification.already_verified'),
            ], 400);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'status' => trans('verification.verified'),
        ]);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'recaptcha' => 'required|captcha',
        ]);

        $user = Account::where('email', $request->email)->first();

        if (is_null($user)) {
            throw ValidationException::withMessages([
                'email' => [trans('verification.user')],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [trans('verification.already_verified')],
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => trans('verification.sent')]);
    }

    /**
     * Mark the user's phone number as verified.
     */
    public function verifyPhone(Request $request)
    {
        $user = $request->user();
        $code = $request->get('code', 0);
        if (strlen($code) != 6) {
            return Response::error(trans('messages.invalid_code'));
        }

        $hasCode = VerifyMobile::where('user_id', '=', $user->id)
            ->orderBy('id', 'desc')
            ->first();

        $vCode = $hasCode
            ->where('expire_date', '>=', Carbon::now())
            ->where('code', '=',
                Str::substr($code, 0, 3).'-'.Str::substr($code, 3, 6))
            ->first();

        if ($vCode) {
            $user->markPhoneAsVerified();
            return Response::success(trans('verification.verified_phone'),
                $vCode);
        }

        return Response::error(trans('verification.invalid_code'));
    }
}
