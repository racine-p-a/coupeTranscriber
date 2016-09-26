<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Toutes les vues doivent hériter de celle-ci. En effet, elle a vocation à gérer toutes les parties communes.
 */

class VueAbstraite
{
    /**
     * @var string Correspond au code HTML tel qu'il sera affiché dans le navigateur.
     */
    protected $corpsHTML = '';

    /**
     * @var string La concaténation de toutes les erreurs levées par la(les) vue(s) dans un format compréhensible
     * par l'utilisateur.
     */
    protected $erreurs = '';

    /**
     * @return string Renvoie le code HTML généré.
     */
    public function getVue()
    {
        return $this->corpsHTML;
    }

    /**
     * Pour rendre l'interface plus intuitive, on fait apparaître dans les menus de sélection le début du texte du tour.
     * Cette méthode permet de générer ce texte.
     * @param $deroulementDuTour array Le déroulement complet du tour.
     * @return string Le texte correspondant à ce début de tour.
     */
    protected function genererDebutTourEnTexte($deroulementDuTour)
    {
        $resultatTexte = '';
        // On ne sort que xx caractères pour ne pas trop alourdir
        $nbMaxDeCaracteres = 20;

        // On parcourt le déroulement du tour, on convertit chaque portion en texte que l'on ajoute au résultat
        // jusqu'à dépasser les 20 caractères.
        //var_dump($deroulementDuTour);
        //echo '<br><br>';

        foreach($deroulementDuTour as $action)
        {
            if(is_string($action))
            {
                $resultatTexte .= $action;
            }
            else if(get_class($action) == 'Event')
            {
                if($action->getDesc() == '-')
                {
                    $resultatTexte .= '-';
                }
                else if($action->getDesc() == '/')
                {
                    $resultatTexte .= '/';
                }
                else if($action->getDesc() == '\\')
                {
                    $resultatTexte .= '\\';
                }
                else if($action->getDesc() == ':')
                {
                    $resultatTexte .= ':';
                }
                else if($action->getDesc() == '(inaud.)')
                {
                    $resultatTexte .= '(inaud.)';
                }
                else if($action->getType() == 'noise' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else if($action->getType() == 'lexical' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else if($action->getType() == 'noise' && $action->getExtent() == 'previous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else if($action->getType() == 'pronounce' && $action->getExtent() == 'begin')
                {
                    $resultatTexte .= '&lt;((' . $action->getDesc();
                }
                else if($action->getType() == 'pronounce' && $action->getExtent() == 'end')
                {
                    $resultatTexte .= '&gt;';
                }
                else if($action->getType() == 'overlap' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else if($action->getType() == 'pause' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= '(' . $action->getDesc() . ')';
                }
                else if($action->getType() == 'action' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= '(' . $action->getDesc() . ')';
                }
                else if($action->getType() == 'vocal' && $action->getExtent() == 'begin')
                {
                    $resultatTexte .= '&lt;((' . $action->getDesc() . '))';
                }
                else if($action->getType() == 'vocal' && $action->getExtent() == 'end')
                {
                    $resultatTexte .= '&gt;';
                }
                else if($action->getType() == 'unclear' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else if($action->getType() == 'noise' && $action->getExtent() == 'begin')
                {
                    $resultatTexte .= '(' . $action->getDesc() . ')';
                }
                else if($action->getType() == 'noise' && $action->getExtent() == 'end')
                {
                    $resultatTexte .= '(' . $action->getDesc() . ')';
                }
                else if($action->getType() == 'pronounce' && $action->getExtent() == 'instantaneous')
                {
                    $resultatTexte .= $action->getDesc();
                }
                else
                {
                    $this->erreurs .= 'Erreur levée : Event inconnu (' . var_export($action, true) . ')<br>';
                }

            }
            else if(get_class($action) == 'Comment')
            {
                $resultatTexte .= $action->getDesc();
            }

            else if(get_class($action) == 'Who')
            {
                // BALÉKOUYE.
            }

            else
            {
                $this->erreurs .= 'Erreur levée : Event inconnu (' . var_export($action, true) . ')<br>';
            }
        }
        return mb_substr($resultatTexte, 0, $nbMaxDeCaracteres);
    }

    /**
     * Clin d'œil/ Easter Egg
     * @param $categorieImage succes/echec ou tout autre texte qui correspond à un nom de dossier dans le dossier img/.
     * @return mixed|string Le nom de l'image choisie.
     */
    protected function recupererImage($categorieImage)
    {
        // On cherche dans le dossier correspondant à la catégorie d'image.
        // On pioche une image au hasard dans ce dossier.
        $dossierCorrespondant = 'img/' . $categorieImage . '/';
        $listeImages = array_slice(scandir($dossierCorrespondant),2);
        if(count($listeImages)>1)
        {
            $imageChoisie = array_rand($listeImages, 1);
            $imageChoisie = $listeImages[$imageChoisie];
            if($imageChoisie == 'index.php')
            {
                return $this->recupererImage($categorieImage);
            }
            else
            {
                return $imageChoisie;
            }
        }
        return '';
    }
}