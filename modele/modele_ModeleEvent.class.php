<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère et représente les balises Event trouvables dans les fichiers .trs ou .trico.
 */

class Event
{
    /**
     * @var mixed|string L'attribut desc de la balise Event.
     * example '(inaud.)'
     */
    protected $desc = '';

    /**
     * @var string L'attribut type de la balise Event.
     * @example 'lexical'
     */
    protected $type = '';

    /**
     * @var string L'attribut extent de la balise Event.
     * @example 'instantaneous'
     */
    protected $extent = '';

    /**
     * Event constructor.
     * @param string $desc
     * @param string $type
     * @param string $extent
     */
    public function __construct($desc, $type, $extent)
    {
        $this->desc = str_replace('&', '&amp;', $desc);
        $this->type = $type;
        $this->extent = $extent;
    }

    /**
     * @return string
     * Renvoie l'attribut desc de l'objet Event actuel.
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @return string
     * Renvoie l'attribut type de l'objet Event actuel.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Renvoie l'attribut extent de l'objet Event actuel.
     */
    public function getExtent()
    {
        return $this->extent;
    }




}