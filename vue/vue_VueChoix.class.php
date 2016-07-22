<?php

include_once('vue/vue_VueAbstraite.class.php');

class VueChoix extends VueAbstraite
{
    protected $corpsHTML = '<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/css_coupeTranscriber.css">
    </head>
<body>
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
        ' . $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoDebut') . '
        <h3>Fin</h3>' .
            $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoFin') .

            $this->genererBoutonsRadio() .
            '
        
            <input type="submit" name="fichierDemande" value="Télécharger">
            <p id="affichageErreurs">
            </p>
        </form>' . $this->genererScripts() .
            '
</body>
</html>';
    }

    protected function genererListeOptions($listeTours = array(), $nomBalise = '')
    {
        $html = '
            <select onchange="verificationBornes()" id="' . $nomBalise . '" name="' . $nomBalise . '" >';

        foreach ($listeTours as $tour)
        {
            $html .='
                <option value="' . $tour->getChronoDebut() . '">' . $this->convertirChrono($tour->getChronoDebut()) . '</option>';
            //var_dump($tour);
            //echo '<br><br>';
        }

        return $html . '
            </select>
';
    }


    protected function genererBoutonsRadio()
    {
        $html = '<br>
            <input type="radio" name="actionChrono" value="laisser">Laisser les chronos tels quels<br>
            <input type="radio" name="actionChrono" value="recalculer" checked>Réinitialiser les chronos : le premier sera à 0 et les suivants seront recalculés<br>';

        return $html;
    }

    protected function genererRapportErreurs($erreurs)
    {
        if($erreurs == '')
        {
            $html = '<h4>Félicitations, aucune erreur n\'a été levée durant l\'import</h4>';
        }
        else
        {
            $html = '<h4>ATTENTION ! Des erreurs ont été levées durant l\'importation du fichier</h4>
            <p>' .
            $erreurs .
            '</p>';
        }
        return $html;
    }

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
        $decimales = floatval($chrono) - $nbTotalDeSecondes;

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