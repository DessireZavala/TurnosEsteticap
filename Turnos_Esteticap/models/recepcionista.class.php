<?php

class recepcionista implements JsonSerializable{
    private $id_rep;
    private $usuario;
    private $password;
    private $id_rol;


    function __construct($id_rep,
                            $usuario,
                            $password,
                            $id_rol){
        $this->id_rep = $id_rep;
        $this->usuario = $usuario;
        $this->password = $password;
        $this->id_rol = $id_rol;
    }

    function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

};


?>