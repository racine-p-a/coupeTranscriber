<?php

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
    // AFFICHAGE DE LA VUE.
    include_once('vue/vue_VueChoix.class.php');
    $vue = new VueChoix($donneesFichier);
    echo $vue->getVue();
}
else if(isset($_POST['fichierDemande']))
{
    // Lu'tilisateur a bougé (ou pas) les curseurs et a demandé le téléchargement du fichier
}
else
{
    echo 'fichier demandé';
}

