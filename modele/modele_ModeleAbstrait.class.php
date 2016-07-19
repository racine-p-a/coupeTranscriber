<?php

class ModeleAbstrait
{
    protected $erreurs = '';


    public function getErreurs()
    {
        return $this->erreurs;
    }

}