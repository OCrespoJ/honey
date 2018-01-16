<?php 

use Firebase\JWT\JWT;

class Controller_Games extends Controller_Rest
{
    private $key = '53jDgdTf5efGH54efef978';

    private function authorization($token)
    {

        $decoded = JWT::decode($token, $this->key, array('HS256'));

        $userId = $decoded->id;

        $users = Model_users::find('all', array(
                'where' => array(
                    array('id', $userId)
                ),
        ));

        if ($users != null) {
            return true;
        }
        else 
        {
           return false; 
        }
    }

    public function post_save()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                if (! isset($_POST['titulo']) or $_POST['titulo'] == "" or
                    ! isset($_POST['vidas']) or $_POST['vidas'] == "" or 
                    ! isset($_POST['posicion']) or $_POST['posicion'] == "" or 
                    ! isset($_POST['progreso']) or $_POST['progreso'] == "") 
                {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios'
                ));

                return $json;
                }

                //Buscar partida
                $game = Model_Games::find('first', array(
                    'where' => array(
                        array('titulo', $_POST['titulo']),
                        array('id_usuario', $id)
                    ),
                ));

                if (empty($game)) {

                   //crear partida
                    $game = new Model_Games();
                    $game->titulo = $_POST['titulo'];
                    $game->vidas = $_POST['vidas'];
                    $game->posicion = $_POST['posicion'];
                    $game->progreso = $_POST['progreso'];
                    $game->id_usuario = $id;
                    $game->save();

                    $json = $this->response(array(
                        'code' => 201,
                       'message' => 'Partida creada'
                    ));
                    return $json;
                }
                else
                {
                    //guardar partida
                    $game->vidas = $_POST['vidas'];
                    $game->posicion = $_POST['posicion'];
                    $game->progreso = $_POST['progreso'];
                    $game->save();

                    $json = $this->response(array(
                        'code' => 201,
                       'message' => 'Partida guardada'
                    ));
                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
            ));

            return $json;
        }

            
    }

    public function get_games()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                $games = Model_Games::find('all', array(
                'where' => array(
                    array('id_usuario', $id)
                    ),
                ));

            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Partidas mostradas',
                    'data' => $games
                ));

                return $json;

            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }

    public function post_delete()
    {
        try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                $game = Model_Games::find('first', array(
                'where' => array(
                    array('titulo', $_POST['titulo'])
                    ),
                ));


                if ( ! isset($_POST['id']) or
                 $_POST['id'] == "") 
                {
                   $json = $this->response(array(
                        'code' => 401,
                        'message' => 'parametros incorrectos/Los campos no pueden estar vacios'
                    ));

                    return $json; 
                }
                else
                {
                    $game->delete();
                    $json = $this->response(array(
                        'code' => 201,
                        'message' => 'Partida borrada'
                    ));
                
                return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }

    public function get_load()
    {
        try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));

                $game = Model_Games::find('first', array(
                'where' => array(
                    array('titulo', $_GET['titulo'])
                    ),
                ));
                $json = $this->response(array(
                'code' => 200,
                'data' => $game,
            ));
                return $json;

            }
            else
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }
}