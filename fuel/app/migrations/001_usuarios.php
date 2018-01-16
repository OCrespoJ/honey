<?php

namespace Fuel\Migrations;

class Usuarios
{

    function up()
    {
        \DBUtil::create_table('usuarios', 
            array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'username' => array('type' => 'varchar', 'constraint' => 50),
            'email' => array('type' => 'varchar', 'constraint' => 50),
            'pass' => array('type' => 'varchar', 'constraint' => 50)
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

    }

    function down()
    {
       \DBUtil::drop_table('usuarios');
    }

}