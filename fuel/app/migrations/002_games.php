<?php

namespace Fuel\Migrations;

class Games
{

    function up()
    {
        \DBUtil::create_table('games', 
            array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'titulo' => array('type' => 'varchar', 'constraint' => 50),
            'vidas' => array('type' => 'int', 'constraint' => 2),
            'posicion' => array('type' => 'varchar', 'constraint' => 50),
            'progreso' => array('type' => 'int', 'constraint' => 3),
            'id_usuario' => array('type' => 'int', 'constraint' => 11),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
                array(
                  array(
                    'constraint' => 'claveAjenaPartidasAUsuarios',
                    'key' => 'id_usuario',
                    'reference' => array(
                        'table' => 'usuarios',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE'
                )
            )
        );
    }

    function down()
    {
       \DBUtil::drop_table('games');
    }

}