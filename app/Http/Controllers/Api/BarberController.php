<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\UserAppointment;
use App\Models\Api\UserFavorite;
use App\Models\Api\Barber;
use App\Models\Api\BarberAvailability;
use App\Models\Api\BarberPhotos;
use App\Models\Api\BarberServices;
use App\Models\Api\BarberTestimonial;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function createRandom()
    {
        $array = ['error' => ''];
        for ($q = 0; $q < 15; $q++) {
            $names = ['Boniek', 'Paulo', 'Pedro', 'Amanda', 'Leticia', 'Gabriel', 'Gabriela', 'Thais', 'Luiz', 'Diogo', 'José', 'Jeremias', 'Francisco', 'Dirce', 'Marcelo'];
            $lastnames = ['Santos', 'Silva', 'Santos', 'Silva', 'Alvaro', 'Sousa', 'Diniz', 'Josefa', 'Luiz', 'Diogo', 'Limoeiro', 'Santos', 'Limiro', 'Nazare', 'Mimoza'];
            $servicos = ['Corte', 'Pintura', 'Aparação', 'Unha', 'Progressiva', 'Limpeza de Pele', 'Corte Feminino'];
            $servicos2 = ['Cabelo', 'Unha', 'Pernas', 'Pernas', 'Progressiva', 'Limpeza de Pele', 'Corte Feminino'];
            $depos = [
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.'
            ];
            $newBarber = new Barber();
            $newBarber->name = $names[rand(0, count($names) - 1)] . ' ' . $lastnames[rand(0, count($lastnames) - 1)];
            $newBarber->avatar = rand(1, 4) . '.png';
            $newBarber->stars = rand(2, 4) . '.' . rand(0, 9);
            $newBarber->latitude = '-23.5' . rand(0, 9) . '30907';
            $newBarber->longitude = '-46.6' . rand(0, 9) . '82759';
            $newBarber->save();
            $ns = rand(3, 6);
            for ($w = 0; $w < 4; $w++) {
                $newBarberPhoto = new BarberPhotos();
                $newBarberPhoto->id_barber = $newBarber->id;
                $newBarberPhoto->url = rand(1, 5) . '.png';
                $newBarberPhoto->save();
            }
            for ($w = 0; $w < $ns; $w++) {
                $newBarberService = new BarberServices();
                $newBarberService->id_barber = $newBarber->id;
                $newBarberService->name = $servicos[rand(0, count($servicos) - 1)] . ' de ' . $servicos2[rand(0, count($servicos2) - 1)];
                $newBarberService->price = rand(1, 99) . '.' . rand(0, 100);
                $newBarberService->save();
            }
            for ($w = 0; $w < 3; $w++) {
                $newBarberTestimonial = new BarberTestimonial();
                $newBarberTestimonial->id_barber = $newBarber->id;
                $newBarberTestimonial->name = $names[rand(0, count($names) - 1)];
                $newBarberTestimonial->rate = rand(2, 4) . '.' . rand(0, 9);
                $newBarberTestimonial->body = $depos[rand(0, count($depos) - 1)];
                $newBarberTestimonial->save();
            }
            for ($e = 0; $e < 4; $e++) {
                $rAdd = rand(7, 10);
                $hours = [];
                for ($r = 0; $r < 8; $r++) {
                    $time = $r + $rAdd;
                    if ($time < 10) {
                        $time = '0' . $time;
                    }
                    $hours[] = $time . ':00';
                }
                $newBarberAvail = new BarberAvailability();
                $newBarberAvail->id_barber = $newBarber->id;
                $newBarberAvail->weekday = $e;
                $newBarberAvail->hours = implode(',', $hours);
                $newBarberAvail->save();
            }
        }
        return $array;
    }

    private function searchGeo($address)
    {
        $key = env('MAPS_KEY', null);

        $address = urlencode($address);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '$key=' . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    public function list(Request $request)
    {
        $array = ['error' => ''];

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $city = $request->input('city');
        $offset = $request->input('offset');
        if (!$offset) {
            $offset = 0;
        }

        if (!empty($city)) {
            $res = $this->searchGeo($city);

            if (count($res['results']) > 0) {
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }
        } elseif (!empty($lat) && !empty($lng)) {
            $res = $this->searchGeo($lat . '' . $lng);

            if (count($res['results']) > 0) {
                $city = $res['results'][0]['formatted_address'];
            }
        } else {
            $lat = '-25.495032';
            $lng = '-54.660470';
            $city = 'CDE';
        }

        $barbers = Barber::select(Barber::raw('*, SQRT(POW(69.1 * (latitude - ' . floatval($lat) . '), 2)
            + POW(69.1 * (' . floatval($lng) . ' - longitude)
            * COS(latitude / 57.3), 2)) AS distance'))
//            ->havingRaw('distance < ?', [10]) distancia em kilometros = {10}
//            ->orderBy('distance', 'ASC')
            ->offset($offset)
            ->limit(5)
            ->get();

        $array['data'] = $barbers;
        $array['loc'] = 'CDE';

        return $array;
    }

    public function one($id)
    {
        $array = ['error' => ''];

        $barber = Barber::find($id);

        if ($barber) {
            $barber['avatar'] = url('media/avatars' . $barber['avatar']);
            $barber['favorite'] = false;
            $barber['photos'] = [];
            $barber['services'] = [];
            $barber['testimonials'] = [];
            $barber['available'] = [];

            // Verificando favorito
            $countFavorite = UserFavorite::where('id_user', auth()->user()->getAuthIdentifier())
                ->where('id_barber', $barber->id)
                ->count();
            if ($countFavorite > 0) {
                $barber['favorite'] = true;
            }

            // Pegando as fotos do barberiro
            $barber['photos'] = BarberPhotos::where('id_barber', $barber->id)
                ->get();

            // Pegando os serviços do barbeiro
            $barber['services'] = BarberServices::select(['id', 'name', 'price'])
                ->where('id_barber', $barber->id)
                ->get();

            // Pegando os depoimentos do barbeiro
            $barber['testimonials'] = BarberTestimonial::select(['id', 'name', 'rate', 'body'])
                ->where('id_barber', $barber->id)
                ->get();

            // Pegando disponibilidade do Barbeiro
            $availability = [];

            // - Pegando a disponibilidade crua
            $avails = BarberAvailability::where('id_barber', $barber->id)->get();
            $availWeekdays = [];
            foreach ($avails as $item) {
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            // - Pegar os agendamentos dos próximos 20 dias
            $appointments = [];
            $appQuery = UserAppointment::where('id_barber', $barber->id)
                ->whereBetween('ap_datetime', [
                    date('Y-m-d') . ' 00:00:00',
                    date('Y-m-d', strtotime('+20 days')) . ' 23:59:59'
                ])
                ->get();
            foreach ($appQuery as $appItem) {
                $appointments[] = $appItem['ap_datetime'];
            }

            // - Gerar disponibilidade real
            for ($q = 0; $q < 20; $q++) {
                $timeItem = strtotime('+' . $q . ' days');
                $weekday = date('w', $timeItem);

                if (in_array($weekday, array_keys($availWeekdays))) {
                    $hours = [];

                    $dayItem = date('Y-m-d', $timeItem);

                    foreach ($availWeekdays[$weekday] as $hourItem) {
                        $dayFormatted = $dayItem . ' ' . $hourItem . ':00';
                        if (!in_array($dayFormatted, $appointments)) {
                            $hours[] = $hourItem;
                        }
                    }

                    if (count($hours) > 0) {
                        $availability[] = [
                            'date' => $dayItem,
                            'hours' => $hours
                        ];
                    }

                }
            }

            $barber['available'] = $availability;

            $array['data'] = $barber;

        } else {
            $array['error'] = 'Barbeiro não encontrado';
        }

        return $array;
    }

    public function setAppointment($id, Request $request)
    {
        // service, year, month, day, hour
        $array = ['error' => ''];

        $service = $request->input('service');
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));
        $day = intval($request->input('day'));
        $hour = intval($request->input('hour'));

        $month = ($month < 10) ? '0' . $month : $month;
        $day = ($day < 10) ? '0' . $day : $day;
        $hour = ($hour < 10) ? '0' . $hour : $hour;

        $barberservice = BarberServices::select()
            ->where('id', $service)
            ->where('id_barber', $id)
            ->first();

        if ($barberservice) {
            //  verificar se a data é real
            $apDate = $year . '-' . $month . '-' . $day . ' ' . $hour . ':00:00';
            if (strtotime($apDate) > 0) {
                //  verificar se o barbeiro já possui agendamento neste dia/hora
                $apps = UserAppointment::select()
                    ->where('id_barber', $id)
                    ->where('ap_datetime', $apDate)
                    ->count();
                if ($apps === 0) {
                    // verificar se o barbeiro atende nesta data
                    $weekday = date('w', strtotime($apDate));
                    $avail = BarberAvailability::select()
                        ->where('id_barber', $id)
                        ->where('weekday', $weekday)
                        ->first();
                    if ($avail) {
                        // verificar se o barbeiro atende nesta hora
                        $hours = explode(',', $avail['hours']);
                        if (in_array($hour . ':00', $hours)) {
                            $newApp = new UserAppointment();
                            $newApp->id_user = auth()->id();
                            $newApp->id_barber = $id;
                            $newApp->id_service = $service;
                            $newApp->ap_datetime = $apDate;
                            $newApp->save();
                        } else {
                            $array['error'] = 'Barbeiro não atende nesta hora';
                        }
                    } else {
                        $array['error'] = 'Barbeiro não atende neste dia';
                    }
                } else {
                    $array['error'] = 'Barbeiro já possui agendamento neste dia/hora';
                }
            } else {
                $array['error'] = 'Data inválida';
            }
        } else {
            $array['error'] = 'Serviço inexistente!';
        }
        return $array;
    }

    public function search(Request $request)
    {
        $array = ['error' => '', 'list' => []];
        $param = $request->input('param');

        if ($param) {
            $barbers = Barber::select()
                ->where('name', 'LIKE', '%' . $param . '%')
                ->get();

            $array['list'] = $barbers;
        } else {
            $array['error'] = 'Digite algo para buscar';
        }

        return $array;
    }
}
