<?php
namespace Vgplay\ApiHelpers\Helpers;

use Illuminate\Support\Facades\Session;

class SessionHelper
{
    public static function store($user)
    {
        Session::put('session_user', [
            'id' => $user->id,
            'username' => $user->username,
        ]);
    }

    public static function get()
    {
        return Session::get('session_user');
    }
}
