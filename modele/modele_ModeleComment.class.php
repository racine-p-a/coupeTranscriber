<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère et représente les balises Comment trouvables dans les fichiers .trs ou .trico.
 */

class Comment
{
    /**
     * @var mixed|string L'attribut desc de la balise Comment.
     * @example 'rires'
     */
    protected $desc = '';

    /**
     * Comment constructor.
     * @param string $desc
     */
    public function __construct($desc)
    {
        $this->desc = str_replace('&', '&amp;', $desc);
    }

    /**
     * @return string
     * Renvoie l'attribut desc de l'objet Comment actuel.
     */
    public function getDesc()
    {
        return $this->desc;
    }




}