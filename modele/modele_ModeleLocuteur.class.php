<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère et représente les balises Speaker trouvables dans les fichiers .trs et .trico.
 */

class Locuteur
{
    /**
     * @var string L'attribut id trouvé dans la balise Speaker.
     */
    protected $id = '';

    /**
     * @var string L'attribut name trouvé dans la balise Speaker.
     */
    protected $name = '';

    /**
     * @var string L'attribut check trouvé dans la balise Speaker.
     */
    protected $check = '';

    /**
     * @var string L'attribut dialect trouvé dans la balise Speaker.
     */
    protected $dialect = '';

    /**
     * @var string L'attribut accent trouvé dans la balise Speaker.
     */
    protected $accent = '';

    /**
     * @var string L'attribut scope trouvé dans la balise Speaker.
     */
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