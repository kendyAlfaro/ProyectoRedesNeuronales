<?php

include 'Conexion.php';

class rna extends Conexion {

    protected static $cnx;

    public static function getConection() {
        self::$cnx = Conexion::getInstance();
    }

    public static function closeConection() {
        self::$cnx = null;
    }

    private $m;
    private $n;
    private $pat;
    private $iter;
    private $goal;
    private $rata;
    private $x;
    private $yd;
    private $nombre;

    function __construct($nombre, $m, $n, $pat, $iter, $goal, $rata, $x, $yd) {
        $this->m = $m;
        $this->n = $n;
        $this->pat = $pat;
        $this->iter = $iter;
        $this->goal = $goal;
        $this->rata = $rata;
        $this->x = $x;
        $this->yd = $yd;
        $this->nombre = $nombre;
    }

    static function getCnx() {
        return self::$cnx;
    }

    function getM() {
        return $this->m;
    }

    function getN() {
        return $this->n;
    }

    function getPat() {
        return $this->pat;
    }

    function getIter() {
        return $this->iter;
    }

    function getGoal() {
        return $this->goal;
    }

    function getRata() {
        return $this->rata;
    }

    function getX() {
        return $this->x;
    }

    function getYd() {
        return $this->yd;
    }

    function getNombre() {
        return $this->nombre;
    }

    static function setCnx($cnx) {
        self::$cnx = $cnx;
    }

    function setM($m) {
        $this->m = $m;
    }

    function setN($n) {
        $this->n = $n;
    }

    function setPat($pat) {
        $this->pat = $pat;
    }

    function setIter($iter) {
        $this->iter = $iter;
    }

    function setGoal($goal) {
        $this->goal = $goal;
    }

    function setRata($rata) {
        $this->rata = $rata;
    }

    function setX($x) {
        $this->x = $x;
    }

    function setYd($yd) {
        $this->yd = $yd;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function entrenar() {
        // TODO: implement here
    }

    /**
     * @inheritDoc
     */
    public function simulacion($m, $n, $conf, $u, $w, $x) {
        // TODO: implement here
    }

    /**
     * @inheritDoc
     */
    public function guardar($nombre, $tipo, $confi, $w, $u, $x, $yd) {

        //echo "INSERT INTO red(nombre, tipoRed, configuracion, pesos, umbrales, x, yd) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7])";
        $query = "INSERT INTO  red(nombre, tipoRed, configuracion, pesos, umbrales, x, yd) VALUES ( :nombre ,  :tipoRed ,  :configuracion ,  :w ,  :u, :x, :yd )";
        self::getConection();
        //echo "<br> me conecto y entro<br>";
        $resul = self::$cnx->prepare($query);
        $resul->bindParam(":nombre", $nombre);
        $resul->bindParam(":tipoRed", $tipo);
        $resul->bindParam(":configuracion", $confi);
        $resul->bindParam(":w", $w);
        $resul->bindParam(":u", $u);
        $resul->bindParam(":x", $x);
        $resul->bindParam(":yd", $yd);
        
        try {
           // echo "<br> preparo bien los pararmetros <br>";
            if ($resul->execute()) {
               // echo "<br> se ejecuta<br>";
                return true;
            }
           // echo "<br> preparo bien los pararmetros 2 <br>";
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }


       // echo "<br> no realiza la consulta<br>";
        return false;
    }

    public function validarNombre($nombre) {
        $query = "SELECT * FROM  red  WHERE  nombre = :nombre ";
        self::getConection();

        $resul = self::$cnx->prepare($query);
        $resul->bindParam(":nombre", $nombre);
        $resul->execute();

        if ($resul->rowCount() < 1) {
            return true;
        }
        return false;
    }

    public function consultaIndividual($nombre) {
        $query = "SELECT * FROM  red  WHERE  nombre = :nombre ";
        self::getConection();
        $resul = self::$cnx->prepare($query);
        $resul->bindParam(":nombre", $nombre);
        $resul->execute();

        if ($resul->execute()) {
            //creamos un array
            $result = array();
            $result = $resul->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        return null;
    }

    public function consultaGeneral() {
        $query = "SELECT nombre FROM  red  ";
        self::getConection();
        $resul = self::$cnx->prepare($query);
        $resul->execute();

        if ($resul->execute()) {
            //creamos un array
            $result = array();
            $result = $resul->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        return null;
    }

    function activacion($funcion, $soma) {
        $act = 0.0;
        switch ($funcion) {
            case "Escalon":
                if (($soma >= 0)) {
                    $act = 1;
                } else {
                    $act = 0;
                }
                break;
            case "Lineal":
                /**
                 * Funcion de activacion(lineal): Identidad
                 */
                $act = $soma;
                break;
            case "Sigmoidal":
                /*
                  Funcion de activacion(sigmoidea): Logaritmo Sigmoidal
                 */
                $act = 1.0 / (1.0 + (M_E ** (- $soma / 0.01)));
                break;
            case "TanSigmoidal":
                /**
                 * Funcion de activacion(sigmoidea): Tangente Sigmoidal
                 */
                $act = (2.0 / (1.0 + ( (M_E ** - $soma)))) - 1.0;
                break;
            case "TanHiperbolica":
                /*
                  Funcion de activacion(sigmoidea): Tangente Hiperbolica
                 */
                $act = (( (M_E ** $soma)) - (( (M_E ** - $soma)))) / (( (M_E ** $soma)) + (( (M_E ** - $soma))));
                break;
            case "Gaussiana":
                /**
                 * Funcion de activacion (gaussiana): Campana de Gauss
                 */
                $act = $soma * (M_E ** - $soma);
                break;
            case "Seno":
                /**
                 * Funcion de activacion (seno): funcion seno
                 */
                $act = sin($soma);
                break;
            case "Coseno":
                /*
                  Funcion de activacion (gaussiana): Campana de Gauss
                 */
                $act = cos($soma);
                break;
            default:
                break;
        }
        return $act;
    }

    function derivada($funcion, $potencial) {
        $deri = 0.0;
        switch ($funcion) {
            case "Lineal":
                /**
                 * Derivada 'Identidad Lineal'
                 */
                $deri = 1.0;
                break;
            case "Sigmoidal":
                /*
                  Derivada 'Logaritmo Sigmoidal'
                 */
                $deri = $this->activacion("Sigmoidal", $potencial) * (1 - $this->activacion("Sigmoidal", $potencial));
                break;
            case "TanSigmoidal":
                /* rr
                  Derivada 'Tangente Sigmoidal'
                 */
                $deri = (2 * ( (M_E ** -$potencial))) / ((1.0 + ( (M_E ** -$potencial))) ** 2);
                break;
            case "TanHiperbolica":
                /*
                  Derivada 'Tangente Hiperbolica'
                 */
                $deri = 1 - ($this->activacion("TanHiperbolica", $potencial) ** 2);
                break;
            case "Gaussiana":
                /**
                 * Derivada 'Gaussiana'
                 */
                $deri = (-M_E ** -$potencial) * ($potencial - 1); //gaussiana;
                break;
            case "Seno":
                /**
                 * Funcion de activacion (gaussiana): Campana de Gauss
                 */
                $deri = cos($potencial);
                break;
            case "Coseno":
                /*
                  Funcion de activacion (gaussiana): Campana de Gauss
                 */
                $deri = -sin($potencial);
                break;
            default:
                break;
        }
        return $deri;
    }

}

/*$r = rna::guardar("andMulti", "simple", "0.4", "2", "4", "66", "2");
echo "<br> r:" . $r;
if ($r) {
    echo"se guardo";
} else {
    echo "no se guardo";
}
print_r(rna::consultaIndividual("and"));




if (rna::validarNombre("and3")) {
    echo "esta disponible";
} else {
    echo 'no esta disponible';
}*/