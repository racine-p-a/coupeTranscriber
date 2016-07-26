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
    $_SESSION['nomFichier'] = $donneesFichier->getNomFichier();
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
    $contenuDuNouveauFichierTRS = $fichierFinal->reconstruction($_SESSION['donnees'], $_POST['chronoDebut'], $_POST['chronoFin'], $_POST['actionChrono'], pathinfo($_SESSION['nomFichier'])['extension']);
    // Il est plus judicieux d'ajouter les différents chronos au nouveau nom de fichier. Cela sera utile.
    if(pathinfo($_SESSION['nomFichier'])['extension'] == 'trs')
    {
        $nomFichier = basename($_SESSION['nomFichier'], '.trs') . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoDebut()) . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoFin()) . '.trs';
    }
    else if(pathinfo($_SESSION['nomFichier'])['extension'] == 'trico')
    {
        $nomFichier = basename($_SESSION['nomFichier'], '.trico') . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoDebut()) . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoFin()) . '.trico';
    }
    else
    {
        $nomFichier = basename($_SESSION['nomFichier'], '.trs') . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoDebut()) . '_' . $fichierFinal->convertirChrono($fichierFinal->getChronoFin()) . '.' . pathinfo($_SESSION['nomFichier'])['extension'];
    }
    $emplacementFichier = $_SERVER["DOCUMENT_ROOT"] . '/coupeTranscriber/resultats/' . $nomFichier;
    //echo $emplacementFichier;
    file_put_contents($emplacementFichier, $contenuDuNouveauFichierTRS);

    if(file_exists($emplacementFichier))
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($emplacementFichier)).' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($emplacementFichier).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($emplacementFichier));
        header('Connection: close');
        readfile($emplacementFichier);
        // On n'oubliera pas d'effacer les fichiers :
        $fichierFinal->nettoyerFichiers($_SESSION['nomFichier'], $nomFichier);
        exit();
    }
    /*
     */


}
else
{
    echo 'fichier demandé';
}

