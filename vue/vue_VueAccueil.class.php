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

    <h2>Importez le fichier transcriber que vous souhaitez découper.</h2>

    <form action="index.php" enctype="multipart/form-data" method="post" >
    
        <input type="file" name="fichierTranscription" id="fichierTranscription" required><br>
    
        <h3>Vous pouvez également importer un fichier sonore avec afin qu\'il soit découpé aussi.</h3>
        <input type="file" name="fichierSonore" id="fichierSonore"><br>
    
        <input type="submit" name="envoiFichier" value="Envoyer">
    
    </form>

</body>
</html>';
    }
}