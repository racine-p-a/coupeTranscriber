<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Vue assez simple servant de page d'accueil. Ici l'utilisateur doit envoyer les fichiers qu'il souhaite découper.
 */


include_once('vue/vue_VueAbstraite.class.php');

class VueAccueil extends VueAbstraite
{
    /**
     * VueAccueil constructor.
     */
    public function __construct()
    {
        $this->corpsHTML .= '<!DOCTYPE html>
<html lang="fr">

<head>

    <title>CNRS coupeTranscriber</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/css_coupeTranscriber.css">

</head>



<body>

    <h2>Importez le fichier transcriber que vous souhaitez découper.</h2>

    <form action="index.php" enctype="multipart/form-data" method="post" >
    
        <input type="file" name="fichierTranscription" id="fichierTranscription" required /><br>
    
        <h3>Vous pouvez également importer un fichier sonore avec afin qu\'il soit découpé aussi.</h3>
        <input type="file" name="fichierSonore" id="fichierSonore" /><br>
    
        <input type="submit" name="envoiFichier" value="Envoyer" />
    
    </form>

</body>
</html>';
    }
}