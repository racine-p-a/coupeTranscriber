<?php

include_once('modele/modele_ModeleTour.class.php');
include_once('modele/modele_ModeleLocuteur.class.php');
include_once('modele/modele_ModeleFichier.class.php');

session_start();

if(!isset($_POST['envoiFichier']) && !isset($_POST['fichierDemande']))
{
    // Rien n'a été fait ni demandé pour le moment --> page d'accueil
    include_once('vue/vue_VueAccueil.class.php');
    $vue = new VueAccueil();
    echo $vue->getVue();
}
else if(isset($_POST['envoiFichier']) && !isset($_POST['fichierDemande']))
{
    // L'utilisateur a envoyé le fichier, on le traite et on affiche les curseurs
    // TRAITEMENT DES DONNÉES
    include_once('modele/modele_ModeleFichier.class.php');
    $donneesFichier = new Fichier();
    $_SESSION['donnees'] = $donneesFichier;
    // AFFICHAGE DE LA VUE.
    include_once('vue/vue_VueChoix.class.php');
    $vue = new VueChoix($donneesFichier);
    echo $vue->getVue();
}
else if(isset($_POST['fichierDemande']))
{
    // L'utilisateur a bougé (ou pas) les curseurs et a demandé le téléchargement du fichier
    // Commençons par recréer le fichier en fonction des bornes choisies
    include_once('modele/modele_ModeleFichier.class.php');
    $fichierFinal = new Fichier();
    $fichierFinal->reconstruction($_SESSION['donnees'], $_POST['chronoDebut'], $_POST['chronoFin'], $_POST['actionChrono']);

}
else
{
    echo 'fichier demandé';
}

