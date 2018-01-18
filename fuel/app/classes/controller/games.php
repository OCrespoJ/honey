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
                $numPartidas = $decoded->numPartidas;

                if (! isset($_POST['titulo']) or $_POST['titulo'] == "" or
                    ! isset($_POST['vidas']) or $_POST['vidas'] == "" or 
                    ! isset($_POST['posicion']) or $_POST['posicion'] == "" or 
                    ! isset($_POST['progreso']) or $_POST['progreso'] == "") 
                {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
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
                    if($numPartidas < 3){
                        //crear partida
                        $game = new Model_Games();
                        $game->titulo = $_POST['titulo'];
                        $game->vidas = $_POST['vidas'];
                        $game->posicion = $_POST['posicion'];
                        $game->progreso = $_POST['progreso'];
                        $game->id_usuario = $id;
                        $game->save();

                        $decoded->numPartidas = $numPartidas + 1;

                        $decoded->save();

                        $json = $this->response(array(
                            'code' => 201,
                            'message' => 'Partida creada',
                            'data' => null
                        ));
                        return $json;
                    } else {
                        $json = $this->response(array(
                        'code' => 402,
                        'message' => 'No puedes guardar mas de 3 partidas',
                        'data' => null
                    ));
                    return $json;
                    }
                }
                else
                {
                    //guardar partida
                    $game->vidas = $_POST['vidas'];
                    $game->posicion = $_POST['posicion'];
                    $game->progreso = $_POST['progreso'];
                    $game->save();

                    $json = $this->response(array(
                        'code' => 202,
                        'message' => 'Partida guardada',
                        'data' => null
                    ));
                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => null
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
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => null
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
                    array('id', $_POST['id'])
                    ),
                ));


                if ( ! isset($_POST['id']) or
                 $_POST['id'] == "") 
                {
                   $json = $this->response(array(
                        'code' => 401,
                        'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                        'data' => null
                    ));

                    return $json; 
                }
                else
                {
                    if (empty($game)) {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'No hay ninguna partida con ese id',
                        'data' => null
                    ));
                    return $json;
                    } 
                    else
                    {
                        $game->delete();
                        $json = $this->response(array(
                            'code' => 201,
                            'message' => 'Partida borrada',
                            'data' => null
                        ));
                    return $json;
                    }
                    
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
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
                $id = $decoded->id;

                if ( ! isset($_GET['titulo']) or
                 $_GET['titulo'] == "") 
                {
                   $json = $this->response(array(
                        'code' => 401,
                        'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                        'data' => null
                    ));

                    return $json; 
                }

                $game = Model_Games::find('first', array(
                'where' => array(
                    array('titulo', $_GET['titulo']),
                    array('id_usuario', $id)
                    ),
                ));
                if (empty($game)) {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'No hay ninguna partida con ese nombre',
                        'data' => null
                    ));
                return $json;
                }
                else
                {
                    
                   $json = $this->response(array(
                        'code' => 200,
                        'message' => 'Partida cargada',
                        'data' => $game
                    ));
                return $json;
                }
                

            }
            else
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function get_ranking()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                $games = Model_Games::find('all', array(
                    'order_by' => array('progreso' => 'desc')
                ));

                $ranking;
                $progreso;

                foreach ($games as $key => $value) {
                    $user = Model_Users::find('first', array(
                        'where' => array(
                        array('id', $value->id_usuario)
                        ),
                    ));

                    $progreso[] = $value->progreso;
                    $ranking[] = array("username"=>$user->username,"progreso"=>$value->progreso);
                }
                //rsort($progreso);

                /*foreach ($progreso as $key => $value) {
                    $user = Model_Users::find('first', array(
                        'where' => array(
                        array('id', $value->id_usuario)
                        ),
                    ));
                    $ranking[] = array("username"=>$user->username,"progreso"=>$value);
                }
                */

            $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Ranking mostrado',
                    'data' => $ranking
                ));

                return $json;

            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }
}