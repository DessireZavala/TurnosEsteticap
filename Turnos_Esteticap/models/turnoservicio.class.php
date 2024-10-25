<?php

class turnoservicio implements JsonSerializable{

    private $costo;
    private $idTurno;
    private $idServicio;

    function __construct($costo, $idTurno, $idServicio){
        $this -> costo = $costo;
        $this -> idTurno = $idTurno;
        $this -> idServicio = $idServicio;
    }

    function jsonSerialize(): mixed
        {
            return get_object_vars($this);
        }
    }
?>