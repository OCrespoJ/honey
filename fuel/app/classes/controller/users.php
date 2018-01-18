<?php 

use Firebase\JWT\JWT;

class Controller_Users extends Controller_Rest
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

    public function get_login()
    {
        try {
            if ( ! isset($_GET['username']) or
                 ! isset($_GET['pass']) or
                 $_GET['username'] == "" or
                 $_GET['pass'] == "") 
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));

                return $json;
            }

            $users = Model_users::find('first', array(
                'where' => array(
                    array('username', $_GET['username']),
                    array('pass', $_GET['pass'])
                ),
            ));
            
            //Validación usuario
            if (!empty($users)) {
               //Generar token
                $token = array(
                    'id'  => $users['id'],
                    'username' => $_GET['username'],
                    'pass' => $_GET['pass']
                );
            
            $jwt = JWT::encode($token, $this->key);

            $json = $this->response(array(
                    'code' => 201,
                    'message' => 'usuario logeado',
                    'data' => array(
                        'token' => $jwt,
                        'username' => $token['username']   
                    )
                ));
            return $json;
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'El usuario no existe o contraseña incorrecta',
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
    
    public function post_create()
    {
        try {
            //Validar campos rellenos y nombre correcto
            if ( ! isset($_POST['username']) or
                 ! isset($_POST['email']) or
                 ! isset($_POST['pass']) or
                 $_POST['username'] == "" or
                 $_POST['email'] == "" or
                 $_POST['pass'] == "") 
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));

                return $json;
            }

            //Validar usuario no existe
            $userName = Model_users::find('all', array(
                'where' => array(
                    array('username', $_POST['username']),
                ),
            ));

            if (! empty($userName)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'Ya existe un usuario con este username',
                    'data' => null
                ));
               return $json;
            }

            //Validar email no existe
            $userEmail = Model_users::find('all', array(
                'where' => array(
                    array('email', $_POST['email']),
                ),
            ));

            if (! empty($userEmail)) {
               $json = $this->response(array(
                    'code' => 404,
                    'message' => 'Ya existe un usuario con este email',
                    'data' => null
                ));
               return $json;
            }

            $input = $_POST;
            $user = new Model_Users();
            $user->username = $input['username'];
            $user->email = $input['email'];
            $user->pass = $input['pass'];
            $user->numPartidas = 0;
            $user->save();
            $json = $this->response(array(
                'code' => 202,
                'message' => 'usuario creado',
                'data' => null
            ));

            return $json;

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
                $user = Model_Users::find($id);

                $user->delete();
                $json = $this->response(array(
                    'code' => 201,
                    'message' => 'usuario borrado',
                    'data' => null
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
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function get_checkEmail()
    {
        if (! isset($_GET['email']) or $_GET['email'] == "")
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));
                return $json;
            }

            //Validar usuario no existe
            $user = Model_users::find('all', array(
                'where' => array(
                    array('email', $_GET['email']),
                ),
            ));

            if (empty($user)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'No existe un usuario con este correo',
                    'data' => null
                ));
                return $json;
            } else {
                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'El correo existe',
                    'data' => $user
                ));
                return $json;
            }
        }

    public function post_recoverPass()
    {
        try {
            //Validar campos rellenos y nombre correcto
            if (! isset($_POST['email']) or $_POST['email'] == "" or 
                ! isset($_POST['pass']) or $_POST['pass'] == "")
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));
                return $json;
            }

            //Validar usuario no existe
            $user = Model_users::find('first', array(
                'where' => array(
                    array('email', $_POST['email']),
                ),
            ));

            if (empty($user)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'No existe un usuario con este correo',
                    'data' => null
                ));
               return $json;
            }

            $user->pass = $_POST['pass'];
            $user->save();
            $json = $this->response(array(
                'code' => 201,
                'message' => 'Contraseña cambiada',
                'data' => null
            ));

            return $json;

        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => $user
            ));

            return $json;
        }
    }
}