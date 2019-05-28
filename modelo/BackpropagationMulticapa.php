<?php

include_once 'pesos.php';
include_once 'umbral.php';
include_once 'rna.php';

/**
 *
 */
class BackpropagationMulticapa extends rna {

    private $algoritmo;
    private $funActivacionCapas;
    private $numNeuronaCapas;
    private $w;
    private $u;

    public function __construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd, $algoritmo, $numNeuronaCapas, $funActivacionCapas) {
        parent::__construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd);

        $this->algoritmo = $algoritmo;
        $this->funActivacionCapas = $funActivacionCapas;
        $this->numNeuronaCapas = $numNeuronaCapas;
        $this->w = NULL;
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

    function getAlgoritmo() {
        return $this->algoritmo;
    }

    function getFunActivacionCapas() {
        return $this->funActivacionCapas;
    }

    function getNumNeuronaCapas() {
        return $this->numNeuronaCapas;
    }

    function setAlgoritmo($algoritmo) {
        $this->algoritmo = $algoritmo;
    }

    function setFunActivacionCapas($funActivacionCapas) {
        $this->funActivacionCapas = $funActivacionCapas;
    }

    function setNumNeuronaCapas($numNeuronaCapas) {
        $this->numNeuronaCapas = $numNeuronaCapas;
    }

    /**
     * @inheritDoc
     */
    function entrenar() {
        $algoritmo = $this->getAlgoritmo();
        $funActivacionCapas = $this->getFunActivacionCapas();
        $numNeuronaCapas = $this->getNumNeuronaCapas();
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

        $funActivacionCapas = $this->getFunActivacionCapas();
        $numNeuronaCapas = $this->getNumNeuronaCapas();
        // Definicion de variables
        $yr = array();
        $el = array();
        $ep = array();
        $erms = 0;

        // inicializacion de pesos y umbrales 
        $w = array();
        $u = array();



        if ($this->getW() == NULL) {
            for ($i = 0; $i <= count($numNeuronaCapas); $i++) {
                if ($i == 0) {
                    //inicializacion de pesos
                    $objW = new Pesos($m, $numNeuronaCapas[$i]);
                    $w[$i] = $objW->inicializacion();

                    //inicializacion de umbrales
                    $objU = new Umbral($numNeuronaCapas[$i]);
                    $u[$i] = $objU->inicializacion();
                } elseif ($i == count($numNeuronaCapas)) {
                    //inicializacion de pesos
                    $objW = new Pesos($numNeuronaCapas[$i - 1], $n);
                    $w[$i] = $objW->inicializacion();


                    //inicializacion de umbrales
                    $objU = new Umbral($n);
                    $u[$i] = $objU->inicializacion();
                } else {
                    //inicializacion de pesos
                    $objW = new Pesos($numNeuronaCapas[$i - 1], $numNeuronaCapas[$i]);
                    $w[$i] = $objW->inicializacion();


                    //incializacion de umbrales
                    $objU = new Umbral($numNeuronaCapas[$i]);
                    $u[$i] = $objU->inicializacion();
                }
            }
        } else {
            $u = $this->getU();
            $w = $this->getW();
        }


        //$w[0][0] = 1;
        //$w[0][1] = 1;
        //$u[0] = 2;
        // Para i<-1 Hasta n Con Paso 1 Hacer
        // u[i]<-azar(1)-1
        // Para j<-1 Hasta m Con Paso 1 Hacer
        // w[j,i]<-azar(1)-1
        // Fin Para
        // Fin Para
        // muestra lista de umbrales
        #echo '                                ', "<br>";
        #echo '                                ', "<br>";
        /*       //echo '******** LISTA DE UMBRALES ******', "<br>";
          for ($i = 0; $i < $n; $i++) {
         //echo 'u[', $i, ']: ', $u[$i], "<br>";
          }
          // muestra lista de pesos sinaptico
         //echo '                                ', "<br>";
         //echo '******** LISTA DE PESOS SINAPTICOS ******', "<br>";
         //echo '                                ', "<br>";
          for ($i = 0; $i < $n; $i++) {
          for ($j = 0; $j < $m; $j++) {
         //echo 'w[', $j, '][', $i, ']: ', $w[$j][$i], "<br>";
          }
          } */

        // Definicion de variables
        $yr = array();
        $yh = array();
        $yk = array();
        $yAux = array();
        $el = array();
        $ep = array();
        $erms = 0;
        $rd = 1; // rata dinamica para actualizacion de pesos algoritmo delta modificada
        $arrayErms = array();
        $arrayGoal = array();

        // ciclo de iteraciones
        for ($ite = 1; $ite <= $noiter; $ite++) {
            //echo '                                ', "<br>";
            //echo '                                ', "<br>";
            //echo '******** ITERACION # ', $ite, ' ******', "<br>";
            //echo '                                ', "<br>";
            $sumep = 0;
            // ciclo patron
            for ($p = 0; $p < $nopat; $p++) {

                $suma = 0;
                //******************** entrenamiento para las entradas y la primera capa oculta*****************//
                $h = 0;
                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                    for ($j = 0; $j < $m; $j++) {
                        $suma = $suma + ($x[$p][$j] * $w[$h][$j][$i]);
                    }
                    // atenuacion
                    $suma = $suma - $u[$h][$i];
                    //echo '                                ', "<br>";
                    //echo 'funcion soma: ', $suma, "<br>";
                    // funcion de activacion y salida de la red
                    $yh[$i] = rna::activacion($funActivacionCapas[$h], $suma);
                }
                //echo "<br> entradas este es h " . $h . "<br>";
                //echo "<br> entradas este es yh <br>";
                ////#echo print_r($yh);
                //echo "<br>";
                $yAux[$h] = $yh;

                //********************entrenamiento para las capas  oculta*****************//
                for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                    $suma = 0;
                    for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                        for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                            $suma = $suma + ($yh[$j] * $w[$h][$j][$i]);
                        }
                        // atenuacion
                        $suma = $suma - $u[$h][$i];
                        //echo '                                ', "<br>";
                        //echo 'funcion soma: ', $suma, "<br>";
                        // funcion de activacion y salida de la red
                        $yk[$i] = rna::activacion($funActivacionCapas[$h], $suma);
                    }
                    $yh = $yk;
                    $yAux[$h] = $yk;
                    //echo "<br> en medio de este es h " . $h . "<br>";
                    //echo "<br> en medio de este es yk <br>";
                    ////#echo print_r($yk);
                    //echo "<br>";
                    $yk = array();
                }

                //********************entrenamiento para la capa de salida ********************
                $suma = 0;
                $sumel = 0;
                // calculo de la funcion soma
                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                        $suma = $suma + ($yh[$j] * $w[$h][$j][$i]);
                    }
                    // atenuacion
                    $suma = $suma - $u[$h][$i];
                    //echo '                                ', "<br>";
                    //echo 'funcion soma: ', $suma, "<br>";
                    // funcion de activacion y salida de la red
                    $yr[$i] = rna::activacion($funActivacionCapas[$h], $suma);
                    //echo 'salida deseada: ', $yd[$p][$i], '  salida de la red: ', $yr[$p][$i], "<br>";
                    // calculo del Error Lineal
                    $el[$i] = $yd[$p][$i] - $yr[$i];
                    $sumel = $sumel + abs($el[$i]);
                    //echo 'Error lineal ', $el[$i], "<br>"; 
                    //echo "<br> salidas este es h " . $h . "<br>";
                    $yAux[$h] = $yr;
                }


                //*******Errores No lineales*********************
               //echo "<b> Pesos:" . count($w) . "<br>";
                foreach ($w as $value) {
                   //echo #echo print_r($value), "<br>";
                }

                $h = count($numNeuronaCapas) - 1;
               //echo "<br> h: " . $h . "<br>";
                $enl = array();
                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                    $suma = 0;
                    for ($j = 0; $j < $n; $j++) {
                        $suma = $suma + $el[$j] * $w[$h + 1][$i][$j];
                    }
                    $enl[$h][$i] = $suma;
                }

                for ($k = $h - 1; $k >= 0; $k--) {
                   //echo "<br> k: " . $k . "<br>";
                    for ($i = 0; $i < $numNeuronaCapas[$k]; $i++) {
                        $suma = 0;
                        for ($j = 0; $j < $numNeuronaCapas[$k + 1]; $j++) {
                           //echo "<br>k: " . $k . "<br>";
                           //echo "<br>enl[" . $j . "]" . $enl[$k + 1][$j] . "<br>";
                           //echo "<br>w[" . $i . "][" . $j . "]" . $w[$k + 1][$i][$j] . "<br>";
                            $suma = $suma + $enl[$k + 1][$j] * $w[$k + 1][$i][$j];
                        }
                        $enl[$k][$i] = $suma;
                    }
                }

                // calculo del Error por Patron
                $ep[$p] = $sumel / $n;
                $sumep = $sumep + $ep[$p];

                //echo "<br> salidas este es yr <br>";
                //echo////#echo print_r($yr);
                //echo "<br>";
                //echo "<br> SALIDAS DE LAS REDES <br>";
                //foreach ($yAux as $value) {
                //echo "<br>estas son mis salidas <br>";
                ////#echo print_r($value);
                //}
                //echo"<br>";
                //
               //echo "<br>Errores No lineales<br>";
                foreach ($enl as $value) {
                   //echo #echo print_r($value), "<br>";
                }
                #echo print_r($enl);

               //echo "<br>YAUX<br>";
                foreach ($yAux as $value) {
                   //echo #echo print_r($value), "<br>";
                }
               //echo "<br>YAUX<br>";
                #echo print_r($yAux);
                //echo '<br>***Actualizacion de pesos y umbrales***', "<br>";

                $h = 0;
                //echo "<br> O: " . $numNeuronaCapas[$h] . "<br>";
                //************para los pesos de la capa de entrada y la intermedia***********
                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                    $u[$h][$i] = $u[$h][$i] + 2 * $rata * $enl[$h][$i] * $this->derivada($funActivacionCapas[$h], $yAux[$h][$i]);
                    for ($j = 0; $j < $m; $j++) {
                        $w[$h][$j][$i] = $w[$h][$j][$i] + 2 * $rata * $enl[$h][$i] * $this->derivada($funActivacionCapas[$h], $yAux[$h][$i]) * $x[$p][$j];
                    }
                }

                //************para los pesos entre capas intermedias***********
                for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                   //echo "<br> h: " . $h . "<br>";
                    for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                        $u[$h][$i] = $u[$h][$i] + 2 * $rata * $enl[$h][$i] * $this->derivada($funActivacionCapas[$h], $yAux[$h][$i]);
                        for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                            $w[$h][$j][$i] = $w[$h][$j][$i] + 2 * $rata * $enl[$h][$i] * $this->derivada($funActivacionCapas[$h], $yAux[$h][$i]) * $yAux[$h - 1][$j];
                        }
                    }
                }
                //************para los pesos de la capa de salida y la ultima intermedia***********
                //echo "<br> Ñ-1: " . $numNeuronaCapas[$h - 1] . "<br>";
                //echo "<br>h: ".$h."<br>";
                for ($i = 0; $i < $n; $i++) {
                    $u[$h][$i] = $u[$h][$i] + 2 * $rata * $el[$i];
                    for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                        $w[$h][$j][$i] = $w[$h][$j][$i] + 2 * $rata * $el[$i] * $yAux[$h - 1][$i];
                    }
                }
            }
            // calculo de Erms
            $erms = $sumep / $nopat;
            $arrayErms[$ite] = $erms;
            $arrayGoal[$ite] = $go * 1;
            //echo '', "<br>";
            //echo '', "<br>";
            //echo '', "<br>";
            //echo 'Error ERMS: ', $erms, "<br>"; 
            $tipo = "Backpropagation Multicapa";
            $confi[0] = $numNeuronaCapas;
            $confi[1] = $funActivacionCapas;
            $confi[2] = $algoritmo;
            if ($erms <= $go) {
               //echo "<br>entrenado<br>";
                $g = rna::guardar($nombre, $tipo, json_encode($confi), json_encode($w), json_encode($u), json_encode($x), json_encode($yd));
                if ($g == true) {
                    return $array = array('guardado' => true, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => (array) $arrayErms, "goal" => (array) $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
                }
                return $array = array('guardado' => false, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => (array) $arrayErms, "goal" => (array) $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
            }
        }
        return $array = array('estado' => true, 'entrenado' => false, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => (array) $arrayErms, "goal" => (array) $arrayGoal, 'mensaje' => 'Intentalo Nuevamente 😱😥😅');
    }

    function simulacion($m, $n, $confi, $u, $w, $x) {

        // Definicion de variables
        $yr = array();
        $yk = array();
        $yh = array();
        $numNeuronaCapas = $confi[0];
        $funActivacionCapas = $confi[1];
        //echo "Numero de patrones: ".count($x)."<br>";
        for ($p = 0; $p < count($x); $p++) {
            $suma = 0;
            //******************** entrenamiento para las entradas y la primera capa oculta*****************//
            $h = 0;
            for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                for ($j = 0; $j < $m; $j++) {
                    $suma = $suma + ($x[$p][$j] * $w[$h][$j][$i]);
                }
                // atenuacion
                $suma = $suma - $u[$h][$i];
                //echo '                                ', "<br>";
                //echo 'funcion soma: ', $suma, "<br>";
                // funcion de activacion y salida de la red
                $yh[$i] = rna::activacion($funActivacionCapas[$h], $suma);
            }


            //********************entrenamiento para las capas  oculta*****************//
            for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                $suma = 0;
                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                    for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                        $suma = $suma + ($yh[$j] * $w[$h][$j][$i]);
                    }
                    // atenuacion
                    $suma = $suma - $u[$h][$i];
                    //echo '                                ', "<br>";
                    //echo 'funcion soma: ', $suma, "<br>";
                    // funcion de activacion y salida de la red
                    $yk[$i] = rna::activacion($funActivacionCapas[$h], $suma);
                }
                $yh = $yk;

                $yk = array();
            }

            //********************entrenamiento para la capa de salida ********************
            $suma = 0;
            $sumel = 0;
            // calculo de la funcion soma
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                    $suma = $suma + ($yh[$j] * $w[$h][$j][$i]);
                }
                // atenuacion
                $suma = $suma - $u[$h][$i];
                //echo '                                ', "<br>";
                //echo 'funcion soma: ', $suma, "<br>";
                // funcion de activacion y salida de la red
                $yr[$p][$i] = rna::activacion($funActivacionCapas[$h], $suma);
            }
        }

        return array('yr' => $yr);
    }

}

/*$x[0][0] = 1;
$x[0][1] = 1;
$x[1][0] = 1;
$x[1][1] = 0;
$x[2][0] = 0;
$x[2][1] = 1;
$x[3][0] = 0;
$x[3][1] = 0;


$yd[0][0] = 1;
$yd[1][0] = 0;
$yd[2][0] = 0;
$yd[3][0] = 0;
echo #echo print_r($x);
echo '<br>';
##echo print_r($yd);
echo '<br>';
$funcion = ["TanSigmoidal", "TanHiperbolica", "Sigmoidal"];
$numNeuronaCapas = ["4", "2"];
$algoritmo = "";
$obj = new BackpropagationMulticapa("andBack", count($x[0]), count($yd[0]), count($x), 10, 0.00050, 1, $x, $yd, $algoritmo, $numNeuronaCapas, $funcion);

$cont = 0;

$ent = $obj->entrenar();
echo #echo print_r(json_encode($ent));
*/




//$u = json_decode("[2.866]");
//$w= json_decode("[[1.613],[1.375]]");
//#echo print_r($u);
//echo '<br>';
//#echo print_r($w);
//echo '<br>';
//#echo print_r(PerceptronSimple::simulacion(2, 1, "", $u, $w, $x));

