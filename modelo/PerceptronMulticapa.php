<?php

include_once 'pesos.php';
include_once 'umbral.php';
include_once 'rna.php';

/**
 *
 */
class PerceptronMulticapa extends rna {

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
        // inicializacion de pesos y umbrales
        $w = array();
        $u = array();
        $wAnt = array();
        $uAnt = array();

        if ($this->getW() == NULL) {
            for ($i = 0; $i <= count($numNeuronaCapas); $i++) {
                if ($i == 0) {
                    //inicializacion de pesos
                    $objW = new Pesos($m, $numNeuronaCapas[$i]);
                    $w[$i] = $objW->inicializacion();
                    $wAnt[$i] = $w[$i];

                    //inicializacion de umbrales
                    $objU = new Umbral($numNeuronaCapas[$i]);
                    $u[$i] = $objU->inicializacion();
                    $uAnt[$i] = $u[$i];
                } elseif ($i == count($numNeuronaCapas)) {
                    //inicializacion de pesos
                    $objW = new Pesos($numNeuronaCapas[$i - 1], $n);
                    $w[$i] = $objW->inicializacion();
                    $wAnt[$i] = $w[$i];

                    //inicializacion de umbrales
                    $objU = new Umbral($n);
                    $u[$i] = $objU->inicializacion();
                    $uAnt[$i] = $u[$i];
                } else {
                    //inicializacion de pesos
                    $objW = new Pesos($numNeuronaCapas[$i - 1], $numNeuronaCapas[$i]);
                    $w[$i] = $objW->inicializacion();
                    $wAnt[$i] = $w[$i];

                    //incializacion de umbrales
                    $objU = new Umbral($numNeuronaCapas[$i]);
                    $u[$i] = $objU->inicializacion();
                    $uAnt[$i] = $u[$i];
                }
            }
        } else {
            $u = $this->getU();
            $w = $this->getW();
            $uAnt = $this->getU();
            $wAnt = $this->getW();
        }
//        echo "<br>Pesos<br>";
//        foreach ($w as $value) {
//            echo #echo print_r($value);
//        }
//
//        echo "<br>umbrales<br>";
//
//        foreach ($u as $value) {
//            echo #echo print_r($value);
//        }
        //echo "<br>Finalizan Umbrales<br>";
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
        try {
            for ($ite = 1; $ite <= $noiter; $ite++) {
                //echo '                                ', "<br>";
                //echo '                                ', "<br>";
                //echo '******** ITERACION // ', $ite, ' ******', "<br>";
                //echo '                                 ', "<br>";
                $sumep = 0;
                // ciclo patron
                for ($p = 0; $p < $nopat; $p++) {

                    $suma = 0;
                    //********************para las entradas y la primera capa oculta*****************//
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

                    //********************para las capas  oculta*****************//
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

                    //******************** para la capa de salida ********************
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
                    }


                    // calculo del Error por Patron
                    $ep[$p] = $sumel / $n;
                    $sumep = $sumep + $ep[$p];
                    //echo "<br> salidas este es h " . $h . "<br>";
                    $yAux[$h] = $yr;
                    //echo "<br> salidas este es yr <br>";
                    //echo////#echo print_r($yr);
                    //echo "<br>";
                    //echo "<br> SALIDAS DE LAS REDES <br>";
                    //foreach ($yAux as $value) {
                    //echo "<br>estas son mis salidas <br>";
                    ////#echo print_r($value);
                    //}
                    //echo"<br>";
                    //echo '<br>***Actualizacion de pesos y umbrales***', "<br>";

                    if ($algoritmo == "Delta") {
                        $h = 0;
                        //echo "<br> O: " . $numNeuronaCapas[$h] . "<br>";
                        //************para los pesos de la capa de entrada y la intermedia***********
                        for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                            $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p];
                            for ($j = 0; $j < $m; $j++) {
                                $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $x[$p][$j];
                            }
                        }
                        //************para los pesos entre capas intermedias***********
                        for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                            //echo "<br> Ñ: " . $numNeuronaCapas[$h] . "<br>";
                            for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                                $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p];
                                for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                    $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $yAux[$h - 1][$j];
                                }
                            }
                        }
                        //************para los pesos de la capa de salida y la ultima intermedia***********
                        //echo "<br> Ñ-1: " . $numNeuronaCapas[$h - 1] . "<br>";
                        //echo "<br>h: ".$h."<br>";
                        for ($i = 0; $i < $n; $i++) {
                            $u[$h][$i] = $u[$h - 1][$i] + $rata * $el[$i];
                            for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                $w[$h][$j][$i] = $w[$h][$j][$i] + ($rata * $el[$i] * $yAux[$h - 1][$j]);
                            }
                        }
                    } else {
                        $h = 0;
                        $rd = 1 / $ite;
                        if ($ite == 1) {

                            //************para los pesos de la capa de entrada y la intermedia***********
                            for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                                $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                for ($j = 0; $j < $m; $j++) {
                                    $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $x[$p][$j] + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                }
                            }
                            //************para los pesos entre capas intermedias***********
                            for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                                //echo "<br> Ñ: " . $numNeuronaCapas[$h] . "<br>";
                                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                                    $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                    for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                        $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $yAux[$h - 1][$j] + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                    }
                                }
                            }
                            //************para los pesos de la capa de salida y la ultima intermedia***********
                            //echo "<br> Ñ-1: " . $numNeuronaCapas[$h - 1] . "<br>";
                            for ($i = 0; $i < $n; $i++) {
                                $u[$h][$i] = $u[$h - 1][$i] + $rata * $el[$i] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                    $w[$h][$j][$i] = $w[$h][$j][$i] + ($rata * $el[$i] * $yAux[$h - 1][$j]) + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                }
                            }
                        } else {
                            $wAux = $w;
                            $uAux = $u;
                            //************para los pesos de la capa de entrada y la intermedia***********
                            for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                                $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                for ($j = 0; $j < $m; $j++) {
                                    $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $x[$p][$j] + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                }
                            }
                            //************para los pesos entre capas intermedias***********
                            for ($h = 1; $h < count($numNeuronaCapas); $h++) {
                                //echo "<br> Ñ: " . $numNeuronaCapas[$h] . "<br>";
                                for ($i = 0; $i < $numNeuronaCapas[$h]; $i++) {
                                    $u[$h][$i] = $u[$h][$i] + $rata * $ep[$p] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                    for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                        $w[$h][$j][$i] = $w[$h][$j][$i] + $rata * $ep[$p] * $yAux[$h - 1][$j] + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                    }
                                }
                            }
                            //************para los pesos de la capa de salida y la ultima intermedia***********
                            //echo "<br> Ñ-1: " . $numNeuronaCapas[$h - 1] . "<br>";
                            for ($i = 0; $i < $n; $i++) {
                                $u[$h][$i] = $u[$h - 1][$i] + $rata * $el[$i] + $rd * ($u[$h][$i] - $uAnt[$h][$i]);
                                for ($j = 0; $j < $numNeuronaCapas[$h - 1]; $j++) {
                                    $w[$h][$j][$i] = $w[$h][$j][$i] + ($rata * $el[$i] * $yAux[$h - 1][$j]) + $rd * ($w[$h][$j][$i] - $wAnt[$h][$j][$i]);
                                }
                            }
                            $wAnt = $wAux;
                            $uAnt = $uAux;
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
                $tipo = "Perceptron Multicapa";
                $confi[0] = $numNeuronaCapas;
                $confi[1] = $funActivacionCapas;
                $confi[2] = $algoritmo;
                //echo #echo print_r(json_encode($w));
                //echo '<br><br>';
                if ($erms <= $go) {
                    $g = rna::guardar($nombre, $tipo, json_encode($confi), json_encode($w), json_encode($u), json_encode($x), json_encode($yd));
                    if ($g == true) {
                        return $array = array('guardado' => true, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
                    }
                    return $array = array('guardado' => false, 'estado' => true, 'entrenado' => true, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'entrenamiento finalizado con exito😁😁');
                }
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        return $array = array('estado' => true, 'entrenado' => false, 'iteracion' => $ite - 1, "erms" => $erms, "w" => $w, "u" => $u, "listadoErms" => $arrayErms, "goal" => $arrayGoal, 'mensaje' => 'Intentalo Nuevamente 😱😥😅');
    }

    function simulacion($m, $n, $confi, $u, $w, $x) {
        // Definicion de variables
        $yr = array();
        $yk = array();
        $yh = array();

        $suma = 0;
        $numNeuronaCapas = $confi[0];
        $funActivacionCapas = $confi[1];

        for ($p = 0; $p < count($x); $p++) {
            //********************para las entradas y la primera capa oculta*****************//

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

            //********************para las capas  oculta*****************//
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

            //******************** para la capa de salida ********************
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
                //echo 'salida deseada: ', $yd[$p][$i], '  salida de la red: ', $yr[$p][$i], "<br>";
            }
        }
        return array('yr' => $yr);
    }

//fin simulacion 
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
//////echo////#echo print_r($x);
//////echo '<br>';
//////#echo print_r($yd);
//////echo '<br>';
//////
//$funcion = [ "TanSigmoidal", "TanHiperbolica","Sigmoidal" ];
//$numNeu = [ "3", "4" ];
//$algoritmo = "Delta";
//$obj = new PerceptronMulticapa("andMulticapa", count($x[0]), count($yd[0]), count($x), 1000, 0, 0.001, $x, $yd, $algoritmo, $numNeu, $funcion);
//#echo print_r(json_encode($obj->entrenar()));
//$u = json_decode("[2.866]");
//$w= json_decode("[[1.613],[1.375]]");
//#echo print_r($u);
//echo '<br>';
//#echo print_r($w);
//echo '<br>';
//#echo print_r(PerceptronSimple::simulacion(2, 1, "", $u, $w, $x));
//Array (
//    [0] => Array (  [0] => Array ( [0] => 0.131 [1] => -0.601 [2] => -0.439 [3] => -0.543 ) }
//                    [1] => Array ( [0] => -0.287 [1] => 0.572 [2] => -0.132 [3] => 0.085 ) )
//
//    [1] => Array ( [0] => Array ( [0] => -0.408 [1] => 0.809 [2] => -0.895 )
//                   [1] => Array ( [0] => 0.424 [1] => 0.093 [2] => 0.148 )
//                   [2] => Array ( [0] => -0.596 [1] => -0.061 [2] => 0.936 )
//                   [3] => Array ( [0] => -0.43 [1] => -0.697 [2] => -0.007 ) )
//
//    [2] => Array ( [0] => Array ( [0] => 0.251 )
//                   [1] => Array ( [0] => 0.701 )
//                   [2] => Array ( [0] => 0.376 ) ) ) 1



