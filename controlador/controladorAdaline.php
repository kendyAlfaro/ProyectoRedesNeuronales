<?php
include_once("../modelo/Adaline.php");
header('Content-type: application/json');
header('Access-Control-Allow-Origin:*');
 
$resultado = array();

class controladorAdaline {

    public function validarNombre() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST["nombre"])) {
                return Adaline::validarNombre($_POST["nombre"]);
            } else {
                return null;
            }
        } else {
            return undefined;
        }
    }

    public function consultaIndividual() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST["nombre"])) {
                return Adaline::consultaIndividual($_POST["nombre"]);
            } else {
                return null;
            }
        } else {
            return undefined;
        }
    }

    public function lista() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            return Adaline::consultaGeneral();
        } else {
            return null;
        }
    }

    public function entrenar() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!isset($_POST["iteraciones"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de iteraciones esta vacio");
            } elseif (!isset($_POST["nombre"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de nombre esta vacio");
            } elseif (!isset($_POST["goal"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de goal esta vacio");
            } elseif (!isset($_POST["rata"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de rata esta vacio");
            } elseif (!isset($_POST['funcion'])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de funcion esta vacio");
            } elseif (!isset($_POST["entradas"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de entradas esta vacio");
            } elseif (!isset($_POST["salidas"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de salidas esta vacio");
            } elseif (!isset($_POST["patrones"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de patrones esta vacio");
            } elseif (!isset($_POST["x"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de x esta vacio");
            } elseif (!isset($_POST["yd"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de yd esta vacio");
            } else {

                $obj = new Adaline($_POST["nombre"], $_POST["entradas"], $_POST["salidas"], $_POST["patrones"], $_POST["iteraciones"], $_POST["goal"], $_POST["rata"], $_POST["x"], $_POST["yd"],$_POST["funcion"]);
                
                return $obj->entrenar();
            }
        } else {
            $resultado = array('estado' => false, 'mensaje' => "servidor encontrado");
        }
        return $resultado;
    }

  public function reentrenar() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!isset($_POST["iteraciones"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de iteraciones esta vacio");
            } elseif (!isset($_POST["nombre"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de nombre esta vacio");
            } elseif (!isset($_POST["goal"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de goal esta vacio");
            } elseif (!isset($_POST["rata"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de rata esta vacio");
            } elseif (!isset($_POST['funcion'])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de funcion esta vacio");
            } elseif (!isset($_POST["entradas"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de entradas esta vacio");
            } elseif (!isset($_POST["salidas"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de salidas esta vacio");
            } elseif (!isset($_POST["patrones"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de patrones esta vacio");
            } elseif (!isset($_POST["x"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de x esta vacio");
            } elseif (!isset($_POST["yd"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de yd esta vacio");
            } else {

                $obj = new Adaline($_POST["nombre"], $_POST["entradas"], $_POST["salidas"], $_POST["patrones"], $_POST["iteraciones"], $_POST["goal"], $_POST["rata"], $_POST["x"], $_POST["yd"],$_POST["funcion"]);
                $obj->setW($_POST["w"]);
                $obj->setU($_POST["u"]);
                return $obj->entrenar();
            }
        } else {
            $resultado = array('estado' => false, 'mensaje' => "servidor encontrado");
        }
        return $resultado;
    }
    public function simular() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!isset($_POST["m"])) {
                $resultado = array('estado' => false, 'mensaje' => "campo de numero de entradas esta vacio");
            } elseif (!isset($_POST["n"])) {
                $resultado = array('estado' => false, 'mensaje' => "el campo numero de salidas estan vacio");
            }elseif (!isset($_POST["u"])) {
                $resultado = array('estado' => false, 'mensaje' => "los umbrales estan vacio");
            } elseif (!isset($_POST["w"])) {
                $resultado = array('estado' => false, 'mensaje' => "los pesos sinapticos estan vacios ");
            } elseif (!isset($_POST["x"])) {
                $resultado = array('estado' => false, 'mensaje' => "Los datos a simular estan vacios");
            } else {
                return Adaline::simulacion($_POST["m"],$_POST["n"],"",$_POST["u"],$_POST["w"],$_POST["x"]);
            }
        } else {
            $resultado = array('estado' => false, 'mensaje' => "servidor encontrado");
        }
        return $resultado;
    }

}

//este codigo verifica la accion y la ejecuta 
switch ($_POST['accion']) {
    case 'entrenar':
        return print(json_encode(controladorAdaline::entrenar()));
        break;
    case 'reentrenar':
        return print(json_encode(controladorAdaline::reentrenar()));
        break;
    case 'validarNombre':
        return print(json_encode(controladorAdaline::validarNombre()));
        break;
    case 'lista':
        return print(json_encode(controladorAdaline::lista()));
        break;
    case 'consultaIndividual':
        return print(json_encode(controladorAdaline::consultaIndividual()));
        break;
    case 'simulacion':
        return print(json_encode(controladorAdaline::simular()));
        break;
    default:
        break;
}
