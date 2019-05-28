<?php


/**
 *
 */
class Umbral 
{
    
    private $n;
    private $u;

    function __construct($n) {
        $this->n = $n;
    }

    function getN() {
        return $this->n;
    }

    function setN($n) {
        $this->n = $n;
    }

    function inicializacion() {
        for ($i = 0; $i < $this->n; $i++) {
          $this->u[$i] = (rand(-1000, 1000))/1000;
        }
        return $this->u;
    }
     function mostrarUmbral() {
        for ($i = 0; $i < $this->n; $i++) {
           echo $this->u[$i]."<br>";
        }
    }

    function getU() {
        return $this->u;
    }

}
