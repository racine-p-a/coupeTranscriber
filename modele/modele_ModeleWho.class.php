<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère et représente les balises Who trouvables dans les fichiers .trs et .trico.
 */


class Who
{
    /**
     * @var string L'attribut nb trouvé dans la balise Speaker.
     */
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