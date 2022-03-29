<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $loggedUser;

    public function read()
    {
        $array = ['error' => ''];


        $this->loggedUser = auth()->user();
        $info = $this->loggedUser;
        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $array['data'] = $info;


        return $array;
    }


}
