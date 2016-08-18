<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once('modele/modele_ModeleTour.class.php');
include_once('modele/modele_ModeleLocuteur.class.php');
include_once('modele/modele_ModeleFichier.class.php');

session_start();

if(isset($_FILES['fichierTranscription']))
{
    // La page où on choisit les chronos de début et de fin.
    include_once('controleur/controleur_ControleurPrincipal.class.php');
    new ControleurPrincipal();


}

else if(isset($_POST['fichierDemande']))
{
    // On a cliqué sur télécharger le(s) fichier(s).
    include_once('controleur/controleur_ControleurPrincipal.class.php');
    new ControleurPrincipal($_POST);
}

else
{
    // Rien n'a été fait ni demandé pour le moment --> page d'accueil
    include_once('vue/vue_VueAccueil.class.php');
    $vue = new VueAccueil();
    echo $vue->getVue();
}