<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Controleur de l'application appelé lors d'un accès par navigateur.
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once(getcwd() . '/modele/modele_ModeleFichier.class.php');


class ControleurPrincipal
{

    /**
     * ControleurPrincipal constructor.
     * @param array $donnees
     */
    public function __construct($donnees = '')
    {
        if($donnees == '')
        {
            // Le controleur a plusieurs choses à faire.
            // - S'occuper des fichiers uploadés.
            // - Récupérer les données (dont objets) de ces fichiers.
            $donnees = new Fichier();
            //var_dump($donnees);
            // On appelle la vue.
            include_once(getcwd() . '/vue/vue_VueChoix.class.php');
            $vue = new VueChoix($donnees);
            echo $vue->getVue();
        }

        else
        {
            // Ici, l'utilisateur a cliqué sur le bouton « Télécharger ».
            // Cela implique de regénérer le fichier avec les balises correctes puis de lancer le téléchargement.

            // D'abord, récupérons les noms des fichiers de transcription et audio (si celui-ci existe).
            $donnees = new Fichier($_POST);
            $emplacementFichierATelecharger = $donnees->getFichierFinal();
            if(file_exists($emplacementFichierATelecharger))
            {
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($emplacementFichierATelecharger)).' GMT');
                header('Cache-Control: private',false);
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($emplacementFichierATelecharger).'"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: '.filesize($emplacementFichierATelecharger));
                header('Connection: close');
                readfile($emplacementFichierATelecharger);
                // On n'oubliera pas d'effacer les fichiers :
                $donnees->effacerFichierFinal();
                exit();
            }
        }

    }


}