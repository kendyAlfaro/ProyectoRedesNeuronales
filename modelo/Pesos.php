<?php

/**
 *
 */
class Pesos  {

    private $cnx;
    private $n;
    private $m;
    private $w;

    function __construct($m, $n) {
        $this->n = $n;
        $this->m = $m;
    }

    function getI() {
        return $this->n;
    }

    function getJ() {
        return $this->m;
    }

    function setI($i) {
        $this->n = $i;
    }

    function setJ($j) {
        $this->m = $j;
    }

    function inicializacion() {
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                $this->w[$j][$i] = (rand(-1000, 1000)) / 1000;
            }
        }
        return $this->w;
    }

    function mostrarPesos() {
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                echo $this->w[$j][$i] . "&nbsp &nbsp";
            }
            echo "<br>";
        }
    }

    public function getW(){
        return $this->w;
    }


}
