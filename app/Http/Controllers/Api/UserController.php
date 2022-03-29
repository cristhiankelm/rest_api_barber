<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Api\UserAppointment;
use App\Models\Api\UserFavorite;
use App\Models\Api\Barber;
use App\Models\Api\BarberServices;

class UserController extends Controller
{
    public function read()
    {
        $array = ['error' => ''];

        $info = $this->loggedUser = auth()->user();
        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $array['data'] = $info;

        return $array;
    }

    public function toggleFavorite(Request $request)
    {
        $array = ['error' => ''];

        $id_barber = $request->input('barber');

        $barber = Barber::find($id_barber);

        if ($barber) {
            $fav = UserFavorite::select()
                ->where('id_user', auth()->user()->getAuthIdentifier())
                ->where('id_barber', $id_barber)
                ->first();

            if ($fav) {
                // remover
                $fav->delete();
                $array['have'] = false;
            } else {
                // adicionar
                $newFav = new UserFavorite();
                $newFav->id_user = auth()->user()->getAuthIdentifier();
                $newFav->id_barber = $id_barber;
                $newFav->save();
                $array['have'] = true;
            }
        } else {
            $array['error'] = 'Barbeiro inexistente';
        }

        return $array;
    }

    public function getFavorites() {
        $array = ['error'=>'', 'list'=>[]];

        $favs = UserFavorite::select()
            ->where('id_user', auth()->user()->getAuthIdentifier())
            ->get();

        if($favs) {
            foreach($favs as $fav) {
                $barber = Barber::find($fav['id_barber']);
                $array['list'][] = $barber;
            }
        }
        return $array;
    }

    public function getAppointments() {
        $array = ['error'=>'', 'list'=>[]];

        $apps = UserAppointment::select()
            ->where('id_user', auth()->user()->getAuthIdentifier())
            ->orderBy('ap_datetime', 'DESC')
            ->get();

        if($apps) {
            foreach($apps as $app) {
                $barber = Barber::find($app['id_barber']);

                $service = BarberServices::find($app['id_service']);

                $array['list'][] = [
                    'id' => $app['id'],
                    'datetime' => $app['ap_datetime'],
                    'barber' => $barber,
                    'service' => $service
                ];
            }
        }

        return $array;
    }

}
