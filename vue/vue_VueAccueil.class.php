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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap -->
    <link type="text/css" href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link rel="stylesheet" type="text/css" href="css/css_coupeTranscriber.css">

</head>



<body>

    <h2>Importez le fichier transcriber que vous souhaitez découper.</h2>

    <form action="index.php" enctype="multipart/form-data" method="post" >
    
        <input type="file" name="fichierTranscription" id="fichierTranscription" required /><br>
    
        <h3>Vous pouvez également importer un fichier sonore avec afin qu\'il soit découpé aussi.</h3>
        <input type="file" name="fichierSonore" id="fichierSonore" /><br>
    
        <button type="submit" name="envoiFichier" class="btn btn-primary" value="Envoyer">Envoyer</button>
    
    </form>
    
    
    <script>
        fichierTranscription.addEventListener(\'change\', verificationFormatTranscription, false);
        fichierSonore.addEventListener(\'change\', verificationFormatSon, false);
        
        function verificationFormatSon(e)
        {
            var formatsSonAutorises = new Array("flac", "mp3", "ogg", "wav", "wma");
            var file_list = e.target.files;
            
            for(var i = 0, file; file = file_list[i]; i++)
            {
                var sFileName = file.name;
                var sFileExtension = sFileName.split(".")[sFileName.split(".").length - 1].toLowerCase();
                var iFileSize = file.size;
                var iConvert = (file.size / 10485760).toFixed(2);
                
                if(formatsSonAutorises.indexOf(sFileExtension) == -1)
                {
                    alert("Format sonore inconnu ou peu courant.\nLe résultat pourrait dépendre des codecs installés sur votre machine");
                }
            }
        }
        
        function verificationFormatTranscription(e)
        {
            var formatsTranscriptionAutorises = new Array("trico", "trs");
            var file_list = e.target.files;
            
            for(var i = 0, file; file = file_list[i]; i++)
            {
                var sFileName = file.name;
                var sFileExtension = sFileName.split(".")[sFileName.split(".").length - 1].toLowerCase();
                var iFileSize = file.size;
                var iConvert = (file.size / 10485760).toFixed(2);
                
                if(formatsTranscriptionAutorises.indexOf(sFileExtension) == -1)
                {
                    alert("Format de transcription non autorisé. L\'application ne pourra pas fonctionner.");
                }
            }
        }

    </script>

</body>
</html>';
    }
}