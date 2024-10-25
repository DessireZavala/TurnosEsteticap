<?php

class Encargado implements JsonSerializable{
    //atributos
    private $id_turno;
    private $num_turno;
    private $fech_hor;
    private $id_rep;
   

   

    //constructor
    function  __construct($id_turno, $num_turno, $fech_hor, $id_rep){
        $this -> id_turno = $id_turno;
        $this -> num_turno = $num_turno;
        $this -> fech_hor = $fech_hor;
        $this -> id_rep = $id_rep;
        
    }
    //metodos
    function jsonSerialize(): mixed{

        return get_object_vars($this);
        
    }
}

?>