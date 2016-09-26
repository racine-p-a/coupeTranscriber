# coupeTranscriber

The project is currently finished and functional.

Projet achevé et foncionnel !

Notable software used/Logiciels notables utilisés :
- [apache](https://httpd.apache.org/)
- [php](https://secure.php.net/manual/fr/index.php)
- [avconv](https://libav.org/avconv.html)
- [curl](https://curl.haxx.se/)
- [Bootstrap Twitter](http://getbootstrap.com/)

# Licence
This program is under the [CeCiLL-B](http://www.cecill.info/licences.fr.html) 

# OVERVIEW/APERÇU

## English

The aim of this project is to divide the transcriptions (in Transcriber format .trs) The application recalculates the times from 0 if you desire, and according to the following processes.
<ol>
    <li>Once the application is installed on your computer, open your web browser and go to this address : http://localhost/coupeTranscriber/index.php</li>
    <li>Import the file in the interface.</li>
    <li>Choose the desired beginning and end times.</li>
    <li>Decide if you would like to keep the times as they are or if you would like to restart from 0 (in which case all following times wil be recalculated automatically).</li>
    <li>Done! Just accept the download in your web browser.</li>
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

For Ubuntu users : `sudo apt-get install apache2 php libapache2-mod-php php-xml php-mbstring git curl libav-tools ubuntu-restricted-extras`
Please, note that the package `ubuntu-restricted-extras` can differ if you have a different flavor of Ubuntu (`kubuntu-restricted-extras` for Kubuntu, `lubuntu-restricted-extras` for LUbuntu, `xubuntu-restricted-extras` for XUbuntu)

For Windows users, you can [download](https://sourceforge.net/projects/wampserver/) and [install WAMP](http://www.wampserver.com/).

For Mac users, you can [download and install MAMP](https://www.mamp.info/en/downloads/).

- Restart apache (On Ubuntu: `sudo service apache2 restart`)

- Just download the zipped project (or git-clone it) and copy the files on your server root location. (Ubuntu : `git clone https://github.com/racine-p-a/coupeTranscriber /var/www/html/coupeTranscriber`)

Location for Ubuntu users: `/var/www/html/coupeTrancriber`

Location for DebIan users: `/var/www/coupeTranscriber`

Location for Mac and windows users is the root directory of your server MAMP/WAMP.

- Increase the parameters upload_max_filesize and post_max_size depending on the size of the file you want to slice with the application. **Be cautious**, those modifications are going to change the behaviour of all your applications working with apache.

- Make sure all the directories are readable/writable by the server. Make `sudo chmod -R 777 /var/www/html/coupeTranscriber` if you are in a hurry.

- Open your web browser and go to [http://localhost/coupeTranscriber/index.php](http://localhost/coupeTranscriber/index.php) .

- Congratulations.


## AUTOMATIC INSTALLATION

### UBUNTU
#### English

You can just download the file named instaler_ubuntu.sh and execute it as super-user ( `sudo ./instaler_ubuntu.sh` launched in the installer's directory).
It will launch an automatic Git installation at the convenient place.

In some (most) cases, it appears that Apache blocks maximum filesize you can upload up to 2Mo which is quite tiny especially if you want to cut audio files.
If this happens, you can change those parameters in your `/etc/php/PHPVersionNumber/apache2/php.ini` :
- upload_max_filesize
- post_max_size
**Be careful**, these modifications are going to apply on all your local web applications. 
Once it is done, you can access the application here: [http://localhost/coupeTranscriber/index.php](http://localhost/coupeTranscriber/index.php) .

#### Français

Vous pouvez simplement télécharger le fichier nommé instaler_ubuntu.sh et l'éxécuter en tant que super-utilisateur ( `sudo ./instaler_ubuntu.sh` à lancer dans le dossier où vous avez placé l'installateur).
L'installateur fera lui-même le git-clone, et placera les fichiers au bon endroit.

Dans certains cas (la plupart), Apache bloque la limite de taille des fichiers que vous pourrez utiliser dans l'application à 2Mo (ce qui est très faible surtout si vous souhaitez découper des fichiers audio).
Vous devrez alors augmenter vous-même cette limite dans le fichier `/etc/php/PHPVersionNumber/apache2/php.ini` .
Les paramètres à modifier sont :
- upload_max_filesize
- post_max_size
**Attention !** Modifier ces paramètres impactera toutes les autres applications utilisant Apache et PHP.
Dès que l'installation sera terminée, vous pourrez utiliser l'application ici : [http://localhost/coupeTranscriber/index.php](http://localhost/coupeTranscriber/index.php) . 

### DEBIAN
TODO

## I DON'T WANT TO INSTALL IT! I JUST WANT TO USE IT! JE VEUX JUSTE M'EN SERVIR SANS L'INSTALLER !
TODO

## PROBLEMS / PROBLÈMES
- If you cannot upload your file, browse your apache php.ini increase the parameters upload_max_filesize and post_max_size at more convenient values. Then restart apache (ubuntu : sudo service apache2 restart).

- Si vous ne pouvez pas passer l'étape d'envoi de fichiers, ouvrez votre php.ini (celui d'apache) et acroissez les paramètres upload_max_filesize et post_max_size à des valeurs pus hautes. Puis redémarrez Apache (ubuntu : sudo service apache2 restart).


----------------------

#PLANNED ENHANCEMENTS / AMÉLIORATIONS PRÉVUES
* Créer un installateur pour DebIan (éventuellement mac).
* (Ré-)Écrire la documentaton.