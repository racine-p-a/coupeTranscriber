<?php

include_once('vue/vue_VueAbstraite.class.php');

class VueChoix extends VueAbstraite
{
    protected $corpsHTML = '<!DOCTYPE html>
<html>
<body>

    <h1>Comment souhaitez-vous borner la transcription ?</h1>
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
        $this->corpsHTML .= '
        <form action="index.php" enctype="multipart/form-data" method="post" >
        <h3>Début</h3>
        ' . $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoDebut') . '
        <h3>Fin</h3>' .
            $this->genererListeOptions($donneesFichier->getListeTours(), 'chronoFin') .

            $this->genererBoutonsRadio() .
            '
        
            <input type="submit" name="fichierDemande" value="Télécharger">
        </form>
</body>
</html>';
    }

    protected function genererListeOptions($listeTours = array(), $nomBalise = '')
    {
        $html = '
            <select name="' . $nomBalise . '" >';

        foreach ($listeTours as $tour)
        {
            $html .='
                <option value="' . $tour->getChronoDebut() . '">' . $tour->getChronoDebut() . '</option>';
            //var_dump($tour);
            //echo '<br><br>';
        }

        return $html . '
            </select>
';
    }


    protected function genererBoutonsRadio()
    {
        $html = '<br><input type="radio" name="actionChrono" value="laisser">Laisser les chronos tels quels<br>
  <input type="radio" name="actionChrono" value="recalculer" checked>Réinitialiser les chronos : le premier sera à 0 et les suivants seront recalculés<br>';

        return $html;
    }


}