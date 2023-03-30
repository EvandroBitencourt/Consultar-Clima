# Consulta climática e geolocalização com Codeigniter 4

- Nesse projeto foi realizada a integração à API Openweathermap para obtenção dos dados climáticos de acordo com as coordenadas de latitude e longitude.
- Uma vez que os dados de clima são obtidos, é feita a geolocalização desse ponto no mapa na aplicação.

## Dados do ambiente

- Utilizado o composer como gerenciador de dependências PHP
- Desenvolvido com o PH 8.1
- Codeigniter 4.3.2
- Laragon como Servidor Local

## Instalação

- Realize o clone desse projeto dentro do diretório `www` do Laragon
- Agora entre na raiz do projeto, nesse caso `clima`
- Para instalar o Codeigniter, rode na raiz do projeto o seguinde comando: `composer install`
- Renomeie o arquivo `env-exemple` para `.env`
- No arquivo `.env`, coloque a sua chave da API (https://openweathermap.org/) e altere a o valor de `WEATHER_API_KEY`: 
- Em seguida, com o Laragon iniciado, acesse no seu navegador `http://clima.test/`


