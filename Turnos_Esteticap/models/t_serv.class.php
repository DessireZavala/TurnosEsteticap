<?php

class Encargado implements JsonSerializable{
    //atributos
    private $id_servicio;
    private $costo_act;
    private $nombre_serv;
    private $id_status;
    private $id_rep;
   

   

    //constructor
    function  __construct($id_servicio, $costo_act, $nombre_serv, $id_status, $id_rep){
        $this -> id_servicio = $id_servicio;
        $this -> costo_act = $costo_act;
        $this -> nombre_serv = $nombre_serv;
        $this -> id_status = $id_status;
        $this -> id_rep = $id_rep;
       
        
    }
    //metodos
    function jsonSerialize(): mixed{

        return get_object_vars($this);
        
    }
}

?>