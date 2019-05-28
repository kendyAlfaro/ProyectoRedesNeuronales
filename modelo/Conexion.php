<?php

class Conexion {

    // Contenedor de la instancia del singleton
    private static $instancia;
    
    private static $server = "localhost";
    private static $database = "rna"; //el nombre de tu base de datos
    private static $user = "root";
    private static $password = "";

    // Un constructor privado evita la creación de un nuevo objeto
    public static function getInstance() {
        try {
            $dbh = new PDO("mysql:host=" . self::$server . ";dbname=" . self::$database, self::$user, self::$password);
           return $dbh;
        ///return printf("se conecto") ;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    }
    

//echo Conexion::getInstance();


    