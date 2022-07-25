<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:re_accounts,email,'
                .$user->id,
            'phone'      => 'nullable|numeric|unique:re_accounts,phone,'
                .$user->id,
            'dob'        => 'nullable|date',
        ]);

        if ($user->confirmed_at) {
            $request->request->remove('email');
        }
        if ($user->confirmed_phone_at
            && $request->get('phone', '') != $user->phone
        ) {
            $user->forceFill([
                'confirmed_phone_at' => null,
            ])->save();
        }

        $user->update($request->only('first_name', 'last_name', 'description',
            'gender', 'email', 'dob', 'phone'));


        return response()->json($user);
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $name = $request->file('avatar')->getClientOriginalName();
        $file = Storage::disk('avatar')->put('', $request->file('avatar'));

        $save = new MediaFile();
        $save->user_id = $user->id;
        $save->name = $name;
        $save->folder_id = 0;
        $save->mime_type = $request->file('avatar')->getMimeType();
        $save->size = $request->file('avatar')->getSize();
        $save->url = Storage::disk('avatar')->url($file);
        $save->options = '[]';
        $save->save();

        $user->avatar_id = $save->id;
        $user->save();

        return response()->json($user);
    }
}

