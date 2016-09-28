<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Vue où l'utilisateur doit choisir où découper la transcription et le fichier audio et si il faut recaler ces chronos à 0.
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once('vue/vue_VueAbstraite.class.php');

class VueChoix extends VueAbstraite
{
    /**
     * @var string Le code HTML généré.
     */
    protected $corpsHTML = '<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>CNRS coupeTranscriber</title>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Bootstrap -->
        <link href="css/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <link rel="stylesheet" type="text/css" href="css/css_coupeTranscriber.css">
    </head>
<body>
<h1><a href="index.php">Retour à la page précédente</a></h1>
';

    /**
     * VueChoix constructor.
     * @param $donneesFichier
     */
    public function __construct($donneesFichier)
    {
        // La vue fait apparaître un formulaire contenant deux menus déroulants.
        // - Un pour choisir le début de la transcription
        // - Un autre pour en choisir la fin.
        // On ajoutera également une case cochée par défaut qui réinitialisera/recalculera tous les chronos de la portion choisie.

        $this->corpsHTML .= $this->genererRapportErreurs($donneesFichier->getErreurs()) . '
    <h1>Comment souhaitez-vous borner la transcription ?</h1>
        <form action="index.php" enctype="multipart/form-data" method="post" >
        <h3>Début</h3>
            <input type="hidden" name="nomFichierTranscription" value="' . $donneesFichier->getNomFichier() . '">
            <input type="hidden" name="nomFichierSonore"        value="' . $donneesFichier->getFichierSonAssocie() . '">
        ' . $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoDebut') . '
        <h3>Fin</h3>' .
            $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoFin') .

            $this->genererBoutonsRadio() .
            '
        
            <button type="submit" class="btn btn-success" name="fichierDemande" value="Télécharger">Télécharger</button>
            <p id="affichageErreurs">
            </p>
        </form>' . $this->genererScripts() .
            $this->genererErreursAffichage() .
            '
</body>
</html>';
        /*
         */
    }

    /**
     * On génère le contenu des menus de sélection (ils devront contenir les chronos et le débit du texte de chaque tour).
     * @param array $listeTours La liste de tous les tours de la transcription reçus par le serveur.
     * @param string $nomBalise Le nom à donner à ce menu à créer.
     * @return string Le code HTML de ce menu ainsi créé.
     */
    protected function genererListeOptions($listeTours = array(), $nomBalise = '')
    {
        $html = '
            <select onchange="verificationBornes()" id="' . $nomBalise . '" name="' . $nomBalise . '" >';

        foreach ($listeTours as $tour)
        {
            $html .='
                <option value="' . $tour->getChronoDebut() . '">' . $this->convertirChrono($tour->getChronoDebut()) . ' : ' . $this->genererDebutTourEnTexte($tour->getDeroulementDuTour()) . '</option>';
            //var_dump($tour);
            //echo '<br><br>';
        }

        return $html . '
            </select>
';
    }


    /**
     * Simplement le contenu des boutons radio.
     * @return string Le code HTML gérant ces boutons.
     */
    protected function genererBoutonsRadio()
    {
        $html = '<br>
            <input type="radio" name="actionChrono" value="laisser" id="laisser"><label for="laisser">Laisser les chronos tels quels</label><br>
            <input type="radio" name="actionChrono" value="recalculer" id="recalculer" checked><label for="recalculer">Réinitialiser les chronos : le premier sera à 0 et les suivants seront recalculés</label><br>';

        return $html;
    }

    /**
     * Affiche un encart au sujet des erreurs levées ou pas durant l'import.
     * @param $erreurs Le texte des erreurs compréhensibles par l'utilisateur.
     * @return string Le code HTML de l'encart des erreurs.
     */
    protected function genererRapportErreurs($erreurs)
    {
        if($erreurs == '')
        {
            $html = '<h4>Félicitations, aucune erreur n\'a été relevée durant l\'import</h4>';
            if(rand(0,50) == 2)
            {
                $nomImage = $this->recupererImage('succes');
                if($nomImage !='' && strtolower(pathinfo('img/succes/' . $nomImage)['extension']) == 'mp4')
                {
                    $html .= '
                <video width="320" height="240" autoplay loop>
                    <source src="img/succes/' . $nomImage . '" type="video/mp4">
                </video> ';
                }
                else if($nomImage !='')
                {
                    $html .= '<img alt="erreur" src="img/succes/' . $nomImage . '">';
                }
            }




        }
        else
        {
            $nomImage = $this->recupererImage('echec');
            if(rand(0,50) == 2)
            {
                if($nomImage != '')
                {
                    $codeImage = '<img alt="erreur" src="img/echec/' . $nomImage . '">\'';
                }
            }

            $html = '<h4>ATTENTION ! Des erreurs ont été levées durant l\'importation du fichier. <a href="https://github.com/racine-p-a/coupeTranscriber">Prévenez-en l\'auteur de ce logiciel.</a></h4>
            
            <p>' .
            $erreurs .
            '</p>
            ';
        }
        return $html;
    }

    /**
     * Affiche un encart au sujet des erreurs levées ou pas durant l'affichage. Ces erreurs ne sont pas graves et
     * ne gênent en rien l'éxécution mais risquent de gêner l'affichage.
     * @param $erreurs Le texte des erreurs compréhensibles par l'utilisateur.
     * @return string Le code HTML de l'encart des erreurs.
     */
    protected function genererErreursAffichage()
    {
        $rapportErreurs = '';
        $codeImage = '';

        if(!$this->erreurs == '')
        {
            $nomImage = $this->recupererImage('echec');
            if($nomImage != '')
            {
                if($nomImage !='' && strtolower(pathinfo('img/succes/' . $nomImage)['extension']) == 'mp4' || $nomImage !='' && strtolower(pathinfo('img/succes/' . $nomImage)['extension']) == 'webm')
                {
                    $codeImage .= '
                <video width="320" height="240" autoplay loop>
                    <source src="img/echec/' . $nomImage . '" type="video/' . strtolower(pathinfo('img/succes/' . $nomImage)['extension']) . '">
                </video> ';
                }
                else
                {
                    $codeImage = '<img alt="erreur" src="img/echec/' . $nomImage . '">';
                }
            }
            $rapportErreurs .= '
        <h3>Des erreurs ont été levées durant l\'affichage. Elles ne gêneront pas le découpage du fichier mais peuvent nuire au confort d\'utilisation.
        <a href="https://github.com/racine-p-a/coupeTranscriber">Reportez-les si possible afin qu\'elles soient corrigées.</a></h3>
            ' . $codeImage . '
            <p>
                ' . $this->erreurs . '
            </p>';
        }

        return $rapportErreurs;
    }

    /**
     * Créé le code HTML des scripts.
     * @return string Le code HTML/javascript de tous les scripts de cette page.
     */
    protected function genererScripts()
    {
        // Il n'y a qu'un seul script pour le moment : celui de vérification des bornes. La borne de début doit être
        // évidemment inférieure à celle de fin.

        // On commence par récupérer les valeurs de chaque select.
        $scripts = '
        <script>
            function verificationBornes()
            {
                var valeurDepart = parseFloat(document.getElementById("chronoDebut").value);
                var valeurFin    = parseFloat(document.getElementById("chronoFin").value);
                var paragraphe   = document.getElementById("affichageErreurs");
                if(valeurDepart>valeurFin)
                {
                    paragraphe.innerHTML = "<span class=rouge >Le chrono de départ est postérieur au chrono de fin (t2&lt;t1).</span>";
                }
                else
                {
                    paragraphe.innerHTML = "<span class=vert>Les bornes temporelles sont logiques (t1&lt;t2).</span>";
                }
                
            }
        </script>';

        return $scripts;
    }

    /**
     * Cette classe reçoit en entrée un chrono de type '65.122' et doit le convertir en 00:01:05.122
     * @param string $chrono
     * @return string
     */
    protected function convertirChrono($chrono = '')
    {
        $nbTotalDeSecondes = intval($chrono);
        $decimales = round(floatval($chrono) - $nbTotalDeSecondes, 3);

        $heures  = floor($nbTotalDeSecondes/3600);
        $secondesRestantes = $nbTotalDeSecondes - $heures*3600;
        $minutes = floor($secondesRestantes/60);
        $secondes = $secondesRestantes - $minutes*60;

        // Ajustements de la taille au format str.
        if($heures<10)
        {
            $heures = '0' . $heures;
        }
        if($minutes<10)
        {
            $minutes = '0' . $minutes;
        }
        if($secondes<10)
        {
            $secondes = '0' . $secondes;
        }
        // Nettoyage des décimales : 0.25 devient 25
        $decimales = substr('' . $decimales, 2);
        if($decimales == '')
        {
            $decimales = '00';
        }


        return '[' . $heures . ':' . $minutes . ':' . $secondes . '.' . $decimales .  ']';
    }
}