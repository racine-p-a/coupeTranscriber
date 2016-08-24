<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère et représente les balises Background trouvables dans les fichiers .trs ou .trico.
 */

class Background
{
    /**
     * @var string L'attribut time de la balise Background.
     * @example '16.77'
     */
    protected $time = '';

    /**
     * @var string L'attribut type de la balise Background.
     * @example 'anonymiser'
     */
    protected $type = '';

    /**
     * @var string L'attribut level de la balise Background.
     * @example 'high'
     */
    protected $level = '';

    /**
     * Background constructor.
     * @param string $time
     * @param string $type
     * @param string $level
     */
    public function __construct($time, $type, $level)
    {
        $this->time = $time;
        $this->type = $type;
        $this->level = $level;
    }

    /**
     * @return string
     * Renvoie l'attribut time de l'objet Background actuel.
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     * Renvoie l'attribut type de l'objet Background actuel.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     * Renvoie l'attribut level de l'objet Background actuel.
     */
    public function getLevel()
    {
        return $this->level;
    }




}