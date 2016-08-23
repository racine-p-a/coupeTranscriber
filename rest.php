<?php
/**
 * Created by PhpStorm.
 * User: pracine
 * Date: 18/08/16
 * Time: 14:41
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once(getcwd() . '/modele/modele_ModeleFichier.class.php');




/*
 * L'application RESTful peut être contactée par formulaire, ligne de commande ou autre...
 * Deux cas sont possibles :
 * - on reçoit un fichier de transcription seul --> renvoi d'une liste de balises temporelles.
 * - on reçoit un fichier de transcription (plus éventuellement un fichier sonore) et deux balises temporelles
 *   |--> découpage de la transcription (et du fichier sonore si il existe) et téléchargement forcé.
 */

if (isset($_FILES['fichierTranscription']) AND $_FILES['fichierTranscription']['error'] == 0
    AND isset($_POST['chronoDebut'])
    AND isset($_POST['chronoFin'])
    )
{
    $donneesFichierEntree = new Fichier();

    $donneesATransmettre = $_POST;
    // Il manque encore quelques données à récupérer proprement avant d'appeler le fichier.
    // - Le nom du fichier de transcription.
    // - Le nom du fichier sonore si il existe.
    $donneesATransmettre['nomFichierTranscription'] = $donneesFichierEntree->getNomFichier();
    $donneesATransmettre['nomFichierSonore']        = '';
    if (isset($_FILES['fichierSonore']) AND $_FILES['fichierSonore']['error'] == 0)
    {
        $donneesATransmettre['nomFichierSonore']    = $donneesFichierEntree->getFichierSonAssocie();
    }
    $donneesFichierFinal = new Fichier($donneesATransmettre);

    // On lance le téléchargement du fichier.
    $emplacementFichierATelecharger = $donneesFichierFinal->getFichierFinal();
    //echo $emplacementFichierATelecharger;

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
        //header('Content-Length: '.filesize($emplacementFichierATelecharger));
        header('Connection: close');
        readfile($emplacementFichierATelecharger);
        $donneesFichierFinal->effacerFichierFinal();
        exit();
    }

    // Exemples de commande curl pour contacter cette page :

    /*      TRANSCRIPTION SEULE
    curl \
    -F "fichierTranscription=@/home/pracine/Téléchargements/Aperitif-chat.trs" \
    -F "chronoDebut=0" \
    -F "chronoFin=10" \
    -F "actionChrono=recalculer" \
    -o /home/pracine/monArchive.trs \
    http://localhost/dev_coupeTranscriber/rest.php
    */

    /*      TRANSCRIPTION + FICHIER AUDIO
    curl \
    -F "fichierTranscription=@/home/pracine/Téléchargements/Aperitif-chat.trs" \
    -F "fichierSonore=@/home/pracine/Téléchargements/WanChaiExtended.mp3" \
    -F "chronoDebut=0" \
    -F "chronoFin=10" \
    -F "actionChrono=recalculer" \
    -o /home/pracine/monArchive.zip \
    http://localhost/dev_coupeTranscriber/rest.php
    */
}






else if (isset($_FILES['fichierTranscription']) AND $_FILES['fichierTranscription']['error'] == 0)
{
    // Ici, on a reçu juste des fichiers, c'est donc que l'utilisateur souhaite la liste des balises temporelles.
    $donnees = new Fichier();
    echo implode(';', $donnees->getListeBalisesTemporelles());

    // Pensons à effacer la transcription reçue.
    $commande = 'rm uploads/' . $donnees->getNomFichier();
    exec($commande);

    // Exemple de commande curl pour contacter cette page :
    /*
     curl \
    -F "fichierTranscription=@/home/pracine/Téléchargements/Aperitif-chat.trs" \
     http://localhost/dev_coupeTranscriber/rest.ph
     */
}
else
{
    echo 'Paramètres reçus incohérents.';

}