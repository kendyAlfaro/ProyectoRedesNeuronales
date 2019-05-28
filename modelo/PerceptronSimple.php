<?php

include_once 'pesos.php';
include_once 'umbral.php';
include 'rna.php';
session_start();

/**
 *
 */
class PerceptronSimple extends rna {

    private $w;
    private $u;

    public function __construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd) {
        parent::__construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd);
        $this->w = NULL;
        $this->u = NULL;
    }

    function getU() {
        return $this->u;
    }

    function setU($u) {
        $this->u = $u;
    }

    function getW() {
        return $this->w;
    }

    function setW($w) {
        $this->w = $w;
    }

    /**
     * @inheritDoc
     */
    function entrenar() {

        //nombre de la red
        $nombre = $this->getNombre();

        // declaracion de entradas,salidas y patrones 
        $m = $this->getM();
        $n = $this->getN();
        $nopat = $this->getPat();
        // inicializacion de parametros de entrenamiento
        $noiter = $this->getIter();
        $go = $this->getGoal();
        $rata = $this->getRata();

        //matriz de entradas y salidas
        $x = $this->getX();
        $yd = $this->getYd();


        // Definicion de variables
        $yr = array();
        $el = array();
        $ep = array();
        $erms = 0;


        $w = array();
        $u = array();
        // inicializacion de pesos y umbrales 
        if ($this->getW() == NULL) {
            $objU = new umbral($n);
            $u = $objU->inicializacion();
            $objW = new pesos($m, $n);
            $w = $objW->inicializacion();
        } else {

            $u = $this->getU();
            $w = $this->getW();
        }


// $w[0][0] = 1;
// $w[0][1] = 1;
// $u[0] = 2;
        // Para i<-1 Hasta n Con Paso 1 Hacer
        // u[i]<-azar(1)-1
        // Para j<-1 Hasta m Con Paso 1 Hacer
        // w[j,i]<-azar(1)-1
        // Fin Para
        // Fin Para
        // muestra lista de umbrales
        #echo '                                ', "<br>";
        #echo '                                ', "<br>";
        /*        #echo '******** LISTA DE UMBRALES ******', "<br>";
          for ($i = 0; $i < $n; $i++) {
          #echo 'u[', $i, ']: ', $u[$i], "<br>";
          }
          // muestra lista de pesos sinaptico
          #echo '                                ', "<br>";
          #echo '******** LISTA DE PESOS SINAPTICOS ******', "<br>";
          #echo '                                ', "<br>";
          for ($i = 0; $i < $n; $i++) {
          for ($j = 0; $j < $m; $j++) {
          #echo 'w[', $j, '][', $i, ']: ', $w[$j][$i], "<br>";
          }
          } */
        $arrayErms = array();
        $arrayGoal = array();
        // ciclo de iteraciones
        for ($ite = 0; $ite < $noiter; $ite++) {
            #echo '                                ', "<br>";
            #echo '                                ', "<br>";
            #echo '******** ITERACION # ', $ite, ' ******', "<br>";
            #echo '                                ', "<br>";
            $sumep = 0;
            // ciclo patron
            for ($p = 0; $p < $nopat; $p++) {
                $suma = 0;
                $sumel = 0;
                // calculo de la funcion soma
                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $m; $j++) {
                        $suma = $suma + ($x[$p][$j] * $w[$j][$i]);
                    }
                    // atenuacion
                    $suma = $suma - $u[$i];
                    #echo '                                ', "<br>";
                    #echo 'funcion soma: ', $suma, "<br>";
                    // funcion de activacion y salida de la red
                    $yr[$p][$i] = rna::activacion("Escalon", $suma);
                    #echo 'salida deseada: ', $yd[$p][$i], '  salida de la red: ', $yr[$p][$i], "<br>";
                    // calculo del Error Lineal
                    $el[$i] = $yd[$p][$i] - $yr[$p][$i];
                    $sumel = $sumel + abs($el[$i]);
                    #echo 'Error lineal ', $el[$i], "<br>";
                }
                // calculo del Error por Patron
                $ep[$p] = $sumel / $n;
                $sumep = $sumep + $ep[$p];
                // Actualizacion de pesos y umbrales
                #echo '', "<br>";
                #echo '***Actualizacion de pesos y umbrales***', "<br>";
                for ($i = 0; $i < $n; $i++) {
                    $u[$i] = $u[$i] + $rata * $el[$i];
                    for ($j = 0; $j < $m; $j++) {
                        $w[$j][$i] = $w[$j][$i] + $rata * $el[$i] * $x[$p][$j];
                    }
                }
                /*                #echo '', "<br>";
                  #echo 'Umbrales', "<br>";
                  for ($i = 0; $i < $n; $i++) {
                  #echo 'u[', $i, ']: ', $u[$i], "<br>";
                  }
                  #echo '', "<br>";
                  #echo 'Pesos Sinapticos', "<br>";
                  for ($i = 0; $i < $n; $i++) {
                  for ($j = 0; $j < $m; $j++) {
                  #echo 'w[', $j, ',', $i, ']:', $w[$j][$i], "<br>";
                  }
                  } */
            }
            // calculo de Erms
            $erms = $sumep / $nopat;
            $arrayErms[$ite] = $erms;
            $arrayGoal[$ite] = $go * 1;
            $_SESSION["erms"] = array();
            $_SESSION["erms"] = $arrayErms;
            $_SESSION["goal"] = array();
            $_SESSION["goal"] = $arrayGoal;
            #echo '', "<br>";
            #echo '', "<br>";
            #echo '', "<br>";
            #echo 'Error ERMS: ', $erms, "<br>";
            $tipo = "Perceptron Simple";
            //echo print_r($tipo);
            $confi = $this->getRata();
            if ($erms <= $go) {
                $g = rna::guardar($nombre, $tipo, $confi, json_encode($w), json_encode($u), json_encode($x), json_encode($yd));
                if ($g == true) {
                    return $array = array('guardado' => true, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
                }
                return $array = array('guardado' => false, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
            }
        }
        return $array = array('estado' => true, 'entrenado' => false, 'iteracion' => $ite, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'Intentalo Nuevamente 😱😥😅');
    }

    function simulacion($m, $n, $confi, $u, $w, $x) {

        // Definicion de variables
        $yr = array();

        //echo "Numero de patrones: ".count($x)."<br>";
        for ($p = 0; $p < count($x); $p++) {
            $suma = 0;
            // calculo de la funcion soma

            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $m; $j++) {
                    //echo " suma = ".$suma." + x: ".$x[$p][$j] ." * w: ". $w[$j][$i]. "<br>";
                    $suma = $suma + ($x[$p][$j] * $w[$j][$i]);
                }
                // atenuacion
                //echo "suma = suma".$suma." - u".$u[$i];
                $suma = $suma - $u[$i];
                //salida de la red
                $yr[$p][$i] = rna::activacion("Escalon", $suma);
            }
        }
        return array('yr' => $yr);
    }

}

//$x[0][0] = 1;
//$x[0][1] = 1;
//$x[1][0] = 1;
//$x[1][1] = 0;
//$x[2][0] = 0;
//$x[2][1] = 1;
//$x[3][0] = 0;
//$x[3][1] = 0;
//
//
//$yd[0][0] = 1;
//$yd[1][0] = 0;
//$yd[2][0] = 0;
//$yd[3][0] = 0;
#echo print_r($x);
#echo '<br>';
#print_r($yd);
#echo '<br>';
//
//$obj = new PerceptronSimple("and",count($x[0]), count($yd[0]), count($x), 100, 0, 1, $x, $yd);
//print_r(json_encode($obj->entrenar()));

//$u = json_decode("[2.866]");
//$w= json_decode("[[1.613],[1.375]]");
//print_r($u);
//echo '<br>';
//print_r($w);
//echo '<br>';
//print_r(PerceptronSimple::simulacion(2, 1, "", $u, $w, $x));

