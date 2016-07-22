# coupeTranscriber

Project is currently done ans works perfectly fine. I'm just working on few improvements on the interface.

Projet achevé et foncionnel ! Juste quelques améliorations par-ci par-là.

## English

This project focuses on the slashing of transcriber files with an automatic recalculation of all timings.
It means that if you want only a portion of a transcription, you'll have to :
<ol>
    <li>Once the application is installed on your computer, open your web browser and go to this address : http://localhost/coupeTranscriber/index.php</li>
    <li>Import the file in the interface</li>
    <li>Choose the two timings you want (beginning and ending of the portion you want)</li>
    <li>Decide if you want to keep those timings unchanged or to restart the first one at 0 (in this case, the followings ara automatically recalculated)</li>
    <li>Done ! Just accept the downloading file in your web browser.</li>
</ol>
 
## Français
 
Le but de ce projet est de découper des morceaux de transcriptions (au format .trs de Transcriber). L'application recalcule les chronos à partir de 0 si vous le désirez et selon le processus suivant :
<ol>
    <li>Une fois le logiciel installé sur votre machine, allez à cette adresse dans votre navigateur : http://localhost/coupeTranscriber/index.php</li>
    <li>Importez le fichier dont vous désirez une portion.</li>
    <li>Choisissez les deux bornes de départ et de fin que vous désirez.</li>
    <li>Choisissez également si vous souhaitez garder les chronos tels quels ou si vous souhaitez tout redécaler à partir de 0.</li>
    <li>Fini ! L'application vous propose le téléchargement que vous n'avez plus qu'à accepter.</li>
</ol>

# INSTALLATION

## MANUAL INSTALLATION

- You must have installed a server and PHP (PHP5 or PHP7)

For Ubuntu users : `sudo apt-get install apache2 php libapache2-mod-php`

For Windows users, you can [download](https://sourceforge.net/projects/wampserver/) and [install WAMP](http://www.wampserver.com/).

For Mac users, you can [download and install MAMP](https://www.mamp.info/en/downloads/).

- Just download the zipped project (or git-clone it) ans copy the files on your server root location.

Location for Uubuntu users : `/var/www/html/`

Location for DebIan users : `/var/www/`

Location for Mac and windows users is the root directory of your server MAMP/WAMP.
- Open your web browser and go to [http://localhost/coupeTranscriber/index.php](http://localhost/coupeTranscriber/index.php) .
- Congratulations.


## AUTOMATIC INSTALLATION
TODO

## I DON'T WANT TO INSTALL IT ! I JUST WANT TO USE IT ! JE VEUX JUSTE M'EN SERVIR SANS L'INSTALLER !
TODO


----------------------

#PLANNED ENHANCEMENTS / AMÉLIORATIONS PRÉVUES
* Créer un installateur pour DebIan/Ubuntu (éventuellement mac). 
* Dans l'interface de bornage, ajouter le début du texte de chaque tour après les chronos pour rendre cela plus pratique.
* Ajouter des liens pour revenir au départ dans l'interface.
* Effacer les fichiers après utilisation.
* Cliquer sur le texte des boutons radio doit activer ces boutons.
* Effacer les fichiers une fois le travail terminé.
* Vérifier la PHPDoc.
* Le fichier téléchargé doit être nommé différemment du fichier original (ajout des balises de début et de fin à la fin du nom par ex.)
* Safari semble ne pas télécharger correctement les fichiers de sortie (mimetype ?).