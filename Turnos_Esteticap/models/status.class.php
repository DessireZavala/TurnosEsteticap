<?php

class status implements JsonSerializable{

    private $idStatus;
    private $descripStatus;

    function __construct($idStatus, $descripStatus){
        $this -> idStatus = $idStatus;
        $this -> descripStatus = $descripStatus;
    }

    function jsonSerialize(): mixed
        {
            return get_object_vars($this);
        }
    }
?>