<?php

class clientes implements JsonSerializable{
    private $idcli;
    private $nom;
    private $apat;
    private $amat;
    private $idrece;


    function __construct($idcli,
    $nom,
    $apat,
    $amat,
    $idrece){
        $this->idcli = $idcli;
        $this->nom = $nom;
        $this->apat = $apat;
        $this->amat = $amat;
        $this->idrece = $idrece;
    }

    function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

};


?>