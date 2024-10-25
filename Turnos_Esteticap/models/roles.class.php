<?php

class roles implements JsonSerializable{
    private $id_rol;
    private $nombre_roles;


    function __construct($id_rol,
                            $nombre_roles){
        $this->id_rol = $id_rol;
        $this->nombre_roles = $nombre_roles;
    }

    function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

};


?>