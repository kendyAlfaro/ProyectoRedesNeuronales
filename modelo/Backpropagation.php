<?php

include_once 'pesos.php';
include_once 'umbral.php';
include 'rna.php';

/**
 *
 */
class Backpropagation extends rna {

    private $activacion;
    private $w;
    private $u;

    public function __construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd, $activacion) {
        parent::__construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd);
        $this->activacion = $activacion;
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

    function getActivacion() {
        return $this->activacion;
    }

    static function setActivacion($activacion) {
        $this->activacion = $activacion;
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
        $activacion = $this->getActivacion();

        // Definicion de variables
        $yr = array();
        $el = array();
        $ep = array();
        $erms = 0;



        // inicializacion de pesos y umbrales 
        $w = array();
        $u = array();
        //echo #echo print_r($this->getW());
        if ($this->getW() == NULL) {
            $objU = new umbral($n);
            $u = $objU->inicializacion();
            $objW = new pesos($m, $n);
            $w = $objW->inicializacion();
        } else {

            $u = $this->getU();
            $w = $this->getW();
        }

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
                    $suma = 0;
                    for ($j = 0; $j < $m; $j++) {
                        $suma = $suma + ($x[$p][$j] * $w[$j][$i]);
                    }
                    // atenuacion
                    $suma = $suma - $u[$i];
                    #echo '                                ', "<br>";
                    #echo 'funcion soma: ', $suma, "<br>";
                    // funcion de activacion y salida de la red
                    $yr[$p][$i] = rna::activacion($activacion, $suma);
                    #echo 'salida deseada: ', $yd[$p][$i], '  salida de la red: ', $yr[$p][$i], "<br>";
                    // calculo del Error Lineal
                    $el[$i] = $yd[$p][$i] - $yr[$p][$i];
                    $sumel = $sumel + abs($el[$i]);
                    #echo 'Error lineal ', $el[$i], "<br>";
                }
                // calculo del Error por Patron
                $ep[$p] = $sumel / $n;
                $sumel = 0;
                $sumep = $sumep + $ep[$p];
                // Actualizacion de pesos y umbrales
                #echo '', "<br>";
                #echo '***Actualizacion de pesos y umbrales***', "<br>";
                for ($i = 0; $i < $n; $i++) {
                    $u[$i] = $u[$i] + 2 * $rata * $el[$i] * rna::derivada($activacion, $yr[$p][$i]);
                    for ($j = 0; $j < $m; $j++) {
                        $w[$j][$i] = $w[$j][$i] + 2 * $rata * rna::derivada($activacion, $yr[$p][$i]) * $el[$i] * $x[$p][$j];
                    }
                }
            }
            // calculo de Erms
            $erms = $sumep / $nopat;
            $sumep = 0;
            $arrayErms[$ite] = $erms;
            $arrayGoal[$ite] = $go * 1;
            #echo '', "<br>";
            #echo '', "<br>";
            #echo '', "<br>";
            #echo 'Error ERMS: ', $erms, "<br>";
            $tipo = "Backpropagation";
            $confi[0] = $this->getRata();
            $confi[1] = $this->getActivacion();
            if ($erms <= $go) {
                #echo "<br>entrenado<br>";
                $g = rna::guardar($nombre, $tipo, json_encode($confi), json_encode($w), json_encode($u), json_encode($x), json_encode($yd));
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
        $activacion = $confi[1];

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
                $yr[$p][$i] = rna::activacion($activacion, $suma);
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
$funcion="Sigmoidal";
$obj = new Backpropagation("andBack",count($x[0]), count($yd[0]), count($x), 1000, 0, 0.8, $x, $yd,$funcion);
#echo print_r(json_encode($obj->entrenar()));*/

//$u = json_decode("[2.866]");
//$w= json_decode("[[1.613],[1.375]]");
//#echo print_r($u);
//echo '<br>';
//#echo print_r($w);
//echo '<br>';
//#echo print_r(PerceptronSimple::simulacion(2, 1, "", $u, $w, $x));

