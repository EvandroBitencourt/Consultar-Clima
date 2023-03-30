<?php

namespace App\Libraries;

class Weather
{

    /**
     *  URL base da API
     * @link https://openweathermap.org/api/one-call-api
     */
    private const BASE_URL = 'https://api.openweathermap.org/data/2.5/onecall';

    /** @var string chave de acesso ao openweathermap api */
    private string $apiKey;

    /**
     * Construindo a classe
     */
    public function __construct()
    {
        // definimos o valor da chave de acesso
        $this->apiKey = env('WEATHER_API_KEY');

        // helper para montarmos a tag img do ícone
        helper('html');
    }


    /**
     * Realiza a chamada à API Openweathermap
     *
     * @param string $latitude do ponto geográfico
     * @param string $longitude do ponto geográfico
     * @return string|array
     */
    public function get(string $latitude, string $longitude): string|array
    {
        try {

            // parâmtros da URL
            $params['lat'] = $latitude;
            $params['lon'] = $longitude;
            $params['units'] = 'metric'; // Para temperatura em Celsius e velocidade do vento em metro/segundo, useunits=metric
            $params['lang'] = 'pt_br';
            $params['appid'] = $this->apiKey;

            // Montamos o endponit a ser requisitado

            /**@var string endPoint que será requisitado */
            $endPoint = self::BASE_URL . '?' . http_build_query($params);

            // instanciando a classe Currequest
            $client = \Config\Services::curlrequest();

            // realizamos a requisição
            $response = $client->request(method: 'GET', url: $endPoint);


            // tivemos algum erro na requisição?
            if ($response->getStatusCode() !== 200) {

                return $response->getReasonPhrase();
            }

            // nesse ponto, tivemos sucesso na requisição. Portanto, damos sequência.

            // receberá o body da resposta como array para manipularmos mais facilmente
            $data = json_decode($response->getBody(), true);

            // retornamos os cards
            return [
                'cardsWeather'       => $this->renderDayCard($data['daily'] ?? []),
                'cardsWeatherAlerts' => $this->renderCardAlert($data['alerts'] ?? [])
            ];
        } catch (\Throwable $th) {

            echo '<pre>';
            print_r($th);
            exit;

            // ATENÇÃO: o adequado é exibir uma mensagem genérica de erro
        }
    }


    /**
     * Renderiza os cards dos dias do clima
     *
     * @param array $daily array contento as informções diárias do clima
     * @return string
     */
    public function renderDayCard(array $daily): string
    {
        // temos algum dado no array diário?
        if (empty($daily)) {

            return '<div class="col-md-12">
                        <div class="alert alert-info">Não foram encontrados dados de clima para as coordenadas informadas.</div>
                   </div>';
        }


        /**@var string receberá os cards do clima */
        $cardDiv = '';

        // percorremos o array
        foreach ($daily as $day) {

            // datas
            $date = date('j, F, Y', $day['dt']); // 27, February, 2023
            $sunrise = date('d/m/Y H:i', $day['sunrise']);
            $sunset = date('d/m/Y H:i', $day['sunset']);

            // chance de chuva
            $preciptation = $day['pop'] * 100;

            // velocidade do vento em km/h
            $kmph = number_format(3.6 * $day['wind_speed']);

            // começamos a montar o card (concatenamos)
            $cardDiv .= '<div class="col-md-3 mb-2">'; // abertura da col-md-3

            $cardDiv .= '<div class="card">'; // abertura do card

            $cardDiv .= "<h5 class='card-title p-2'>{$date}</h5>";

            // crio o ícone para o clima do dia
            $cardDiv .= $this->renderIcon($day['weather'][0]);

            $cardDiv .= '<div class="card-body">'; // abertura do card-body

            $cardDiv .= "<h3 class='card-title'>{$day['weather'][0]['description']}</h3>";

            $cardDiv .= "<p class='card-text'>Máxima de: {$day['temp']['max']}&deg;C</p>";
            $cardDiv .= "<p class='card-text'>Mínima de: {$day['temp']['min']}&deg;C</p>";
            $cardDiv .= "<p class='card-text'>Sensação térmica de: {$day['feels_like']['day']}&deg;C</p>";
            $cardDiv .= "<p class='card-text'>Pressão: {$day['pressure']}mb</p>";
            $cardDiv .= "<p class='card-text'>Umidade do ar: {$day['humidity']}%</p>";
            $cardDiv .= "<p class='card-text'>Index UV: {$day['uvi']}</p>";
            $cardDiv .= "<p class='card-text'>Chance de chuva: {$preciptation}%</p>";
            $cardDiv .= "<p class='card-text'>Velocidade do vento: {$kmph} km/h</p>";

            $cardDiv .= "<p class='card-text'>Nascer do sol: {$sunrise}</p>";
            $cardDiv .= "<p class='card-text'>Pôr do sol: {$sunset}</p>";

            $cardDiv .= '</div>'; // fechamento do card-body

            $cardDiv .= '</div>'; // fechamento do card

            $cardDiv .= '</div>'; // fechamento da col-md-3

        } // final foreach


        // retornamos os cards
        return $cardDiv;
    }


    /**
     * Renderiza os cards dos alertas meteorológicos
     *
     * @param array $alerts
     * @return string
     */
    public function renderCardAlert(array $alerts): string
    {
        // temos alertas para tratar?
        if (empty($alerts)) {

            // temos algum dado no array diário?
            if (empty($daily)) {

                return '<div class="col-md-12">
                            <div class="alert alert-info">não há alertas registrados.</div>
                        </div>';
            }
        }


        // valor inicial dos cards
        $cardDiv = '<div class="col-md-12">
                        <div class="alert alert-warning">Alertas meteorológicos.</div>
                    </div>';


        foreach ($alerts as $alert) {

            $start = date('d/m/Y H:i', $alert['start']);
            $end = date('d/m/Y H:i', $alert['end']);


            // começamos a montar o card (concatenamos)
            $cardDiv .= '<div class="col-md-6 mb-2">'; // abertura da col-md-3

            $cardDiv .= '<div class="card">'; // abertura do card

            $cardDiv .= "<h5 class='card-title p-2'>{$alert['sender_name']} informa: </h5>";

            $cardDiv .= '<div class="card-body">'; // abertura do card-body

            $cardDiv .= "<h3 class='card-title'>{$alert['event']}</h3>";

            $cardDiv .= "<p class='card-text'>{$alert['description']}</p>";
            $cardDiv .= "<p class='card-text'>Iniciando na data de: {$start}</p>";
            $cardDiv .= "<p class='card-text'>Finalizando na date de: {$end}</p>";

            $cardDiv .= '</div>'; // fechamento do card-body

            $cardDiv .= '</div>'; // fechamento do card

            $cardDiv .= '</div>'; // fechamento da col-md-3

        }


        return $cardDiv;
    }


    /**
     * Renderiza a imagem do ícone
     *
     * @param array $iconInformation informações do ícone
     * @link https://openweathermap.org/weather-conditions#How-to-get-icon-URL
     * @return string
     */
    public function renderIcon(array $iconInformation): string
    {
        return img(src: "http://openweathermap.org/img/wn/{$iconInformation['icon']}@4x.png");
    }
}
