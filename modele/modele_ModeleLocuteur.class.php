<?php


class Locuteur
{
    protected $id = '';

    protected $name = '';

    protected $check = '';

    protected $dialect = '';

    protected $accent = '';

    protected $scope = '';

    /**
     * Locuteur constructor.
     * @param string $id
     * @param string $name
     * @param string $check
     * @param string $dialect
     * @param string $accent
     * @param string $scope
     */
    public function __construct($id, $name, $check, $dialect, $accent, $scope)
    {
        $this->id = $id;
        $this->name = $name;
        $this->check = $check;
        $this->dialect = $dialect;
        $this->accent = $accent;
        $this->scope = $scope;
    }


}