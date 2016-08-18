<?php

class VueAbstraite
{
    protected $corpsHTML = '';

    protected $erreurs = '';

    public function getVue()
    {
        return $this->corpsHTML;
    }


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
                else if($action->getType() == 'noise' && $action->getExtent() == 'end')
                {
                    $resultatTexte .= '(' . $action->getDesc() . ')';
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

    protected function recupererImage($categorieImage)
    {
        // On cherche dans le dossier correspondant à la catégorie d'image.
        // On pioche une image au hasard dans ce dossier.
        $dossierCorrespondant = 'img/' . $categorieImage . '/';
        $listeImages = array_slice(scandir($dossierCorrespondant),2);
        if(count($listeImages)>1)
        {
            $imageChoisie = array_rand($listeImages, 1);
            return $listeImages[$imageChoisie];
        }
        return '';
    }
}