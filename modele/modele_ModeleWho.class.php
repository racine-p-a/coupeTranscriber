<?php


class Who
{
    protected $nb='1';

    /**
     * Who constructor.
     * @param string $nb
     */
    public function __construct($nb = '1')
    {
        $this->nb = $nb;
    }

    /**
     * @return string
     */
    public function getNb()
    {
        return $this->nb;
    }




}