<?php 

class Model_Games extends Orm\Model
{
    protected static $_table_name = 'games';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'titulo' => array(
            'data_type' => 'varchar'   
        ),
        'vidas' => array(
            'data_type' => 'int'   
        ),
        'posicion' => array(
            'data_type' => 'varchar'   
        ),
        'progreso' => array(
            'data_type' => 'int'   
        ),
        'id_usuario' => array(
            'data_type' => 'int'   
        )
    );
}