<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function current(Request $request)
    {
        return response()->json($request->user());
    }

    public function sendEmailVerification(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->confirmed_at != null) {
                return Response::error(trans('verification.already_verified'));
            }
            $user->sendEmailVerificationNotification();
            return Response::success(trans('verification.sent'),
                $request->user());
        } catch (Exception $e) {
            return Response::error($e);
        }
    }

    public function sendPhoneVerification(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->confirmed_phone_at != null) {
                return Response::error(trans('verification.already_verified_phone'));
            }
            if (is_null($user->phone)) {
                return Response::error(trans('verification.invalid_phone'));
            }
            $user->sendPhoneVerificationNotification();

            $phone = $user->phone;
            $count = strlen($phone);
            $mid = $count / 2;

            for (
                $i = ($mid) - ($mid / 2);
                $i < (2 * ($mid)) - ($mid / 2) - 1; $i++
            ) {
                try {
                    $phone[$i] = '*';
                } catch (Exception $e) {
                }
            }
            return Response::success(trans('verification.sent_phone_sms'),
                ['phone' => $phone, 'expire' => 60 * 2]);
        } catch (Exception $e) {
            return Response::error($e);
        }
    }
}
