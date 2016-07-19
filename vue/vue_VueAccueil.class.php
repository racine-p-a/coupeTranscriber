<?php

include_once('vue/vue_VueAbstraite.class.php');

class VueAccueil extends VueAbstraite
{
    /**
     * VueAccueil constructor.
     */
    public function __construct()
    {
        $this->corpsHTML .= '<!DOCTYPE html>
<html>
<body>

    <h1>Importez le fichier transcriber que vous souhaitez découper.</h1>

    <form action="index.php" enctype="multipart/form-data" method="post" >
    
        <input type="file" name="fichierADecouper" id="fichierADecouper"><br>
    
        <input type="submit" name="envoiFichier" value="Envoyer">
    
    </form>

</body>
</html>';
    }
}