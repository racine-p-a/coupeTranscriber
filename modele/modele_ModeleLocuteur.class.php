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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @return string
     */
    public function getDialect()
    {
        return $this->dialect;
    }

    /**
     * @return string
     */
    public function getAccent()
    {
        return $this->accent;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }




}