<?php

namespace App\Controllers;

use App\Libraries\Weather;
use CodeIgniter\Config\Factories;

class Home extends BaseController
{
    /**
     * Renderiza a view para consultar os dados de clima e geolocalização
     *
     * @return view
     */
    public function index()
    {
        return view('welcome_message');
    }

    /**
     * Processa a requisição advinda da view 'welcome_message'
     *
     * @throws Exception
     * @return mixed
     */
    public function weather()
    {

        if (!$this->request->isAJAX()) {

            return redirect()->back();
        }

        /** @var string latitude enviada no request */
        $latitude = $this->request->getGet('latitude');

        /** @var string longitude enviada no request */
        $longitude = $this->request->getGet('longitude');

        // realizamos a requisição à API openweathermap
        $weather = Factories::class(Weather::class)->get(latitude: $latitude, longitude: $longitude);

        // retornamos para o front
        return $this->response->setStatusCode(200)->setJSON([
            'cardsWeather'       => $weather['cardsWeather'],
            'cardsWeatherAlerts' => $weather['cardsWeatherAlerts']
        ]);
    }
}
