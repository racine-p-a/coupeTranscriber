#!/bin/bash
#######################################################################
#                             LICENCE
# @author Pierre-Alexandre RACINE
# @licence CeCILL-B
# @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
# @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
# Automatic coupeTranscriber installer for Ubuntu users.
# Installeur automatique de coupeTranscriber pour Ubuntu.
#######################################################################



#######################################################################
#                             FONCTIONS
#######################################################################

verificationEntree ()
{
    if [[ "$1" =~ ^[0-9]+$ ]] && [ $1 -ne 0 ]; then
        #echo 'La variable' $2 'est initialisée à' $1 '.'
        if [ $2 == 'upload_max_filesize' ]; then
            let "upload_max_filesize = $1"
            return 0
        else
            if [ $upload_max_filesize -lt $1 ]; then
                #echo 'post_max_size a été choisi.'
                let "post_max_size = $1"
                return 0
            else
                echo "La taille totale doit être supérieure à la taille d\'un fichier seul. Recommencez."
                return 1
            fi
        fi
    else
        echo $1 "n'est pas un entier positif."
        return 1
    fi
}




#######################################################################



# DEMANDE DE TAILLES POUR LE PHP.INI
echo -e "\n"
echo 'Ce logiciel fonctionne en installant un petit serveur sur votre machine.'
echo 'Vous devez juste donner quelques informations afin que cet installateur puisse configurer ce serveur.'
    # Les deux variables à modifier sont :
    #   - upload_max_filesize
    #   - post_max_size

######### upload_max_filesize #########
let "upload_max_filesize = 200"
echo 'Pour commencer, quelle est la taille maximale (en Mo) autorisez-vous pour un fichier unique ?'

while [ -z $reponse ]
do
    read -p 'Valeur conseillée 200 : ' tailleMaximaleFichierEntree
    if verificationEntree $tailleMaximaleFichierEntree 'upload_max_filesize'; then
        break;
    fi
done


######### post_max_size #########
let "post_max_size = 220"
echo 'Ensuite, quelle taille maximale (en Mo) autorisez-vous pour une requête complète (transcription + audio) ?'
while [ -z $reponse ]
do
    read -p 'Valeur conseillée 220 : ' post_max_size
    if verificationEntree $post_max_size 'post_max_size'; then
        break;
    fi
done


echo 'La variable upload_max_filesize sera initialisée à' $upload_max_filesize'.'
echo 'La variable post_max_size sera initialisée à' $post_max_size'.'


# INSTALLATION DES PAQUETS ET DÉPENDANCES

echo "À présent, l'installateur télécharge et installe les dépendances suivantes si elles ne sont pas déjà installées : "
echo '- apache2 (le serveur).'
echo '- php libapache2-mod-php (le langage utilisé et son greffon sur le serveur).'
echo '- php-xml php-mbstring (pour que PHP puisse gérer le XML et les chaînes de caractères unicode)'
echo '- git (pour récupérer les fichiers).'
echo '- curl (si vous voulez faire des appels au serveur en ligne de commandes).'
echo '- libav-tools (pour découper les fichiers sonores que vous enverrez).'
echo "- Il est également conseillé d'installer les codecs non libres. L'installateur tentera de détecter votre variante d'ubuntu et d'installer les paquets correspondants."
echo "En cas d'échec, installez ces paquets manuellement et relancez l'installateur."
echo ''


# Petite mise à jour des paquets par précaution.
apt-get update && apt-get upgrade && apt-get dist-upgrade -y
# Récupération du nouveau dépôt PHP7.
echo "deb http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/dotdeb.list
wget https://www.dotdeb.org/dotdeb.gpg && apt-key add dotdeb.gpg
rm dotdeb.gpg*
apt-get update
# Ajout du dépôt non-libre
echo "deb http://www.deb-multimedia.org stable main non-free">>/etc/apt/sources.list
apt-get update
# Suppression de PHP5 si il est installé.
systemctl stop php5-fpm
apt-get -y autoremove --purge php5*
# Installation des paquets
apt-get --force-yes install apache2 php7.0 php7.0-fpm libapache2-mod-php7.0 php7.0-gd php7.0-curl php7.0-mcrypt php7.0-memcached php7.0-intl php7.0-mbstring php7.0-xml php7.0-zip git curl libav-tools libdvdcss2 vlc


# On redémarre Apache.
service apache2 restart

git clone https://github.com/racine-p-a/coupeTranscriber /var/www/html/coupeTranscriber

# MODIFICATION DU PHP.INI
    # La meilleure des solutions serait de créer un fichier .htaccess qui contiendrait toutes les données nécessaires.
    # Malheureusement, ces variables sont souvent (toujours ?) verrouillées par Apache, on peut créer un fichier php.ini et .user.ini
    # pour tenter d'outrepasser ce blocage mais cela ne fonctionnera pas toujours.
    # En cas d'échec, l'utilisateur devra modifier à la main (ou de manière assistée) les valeurs dans le php.ini d'apache.
texteHtAccess='php_value upload_max_filesize '$upload_max_filesize'M\nphp_value post_max_size '$post_max_size'M'
texteIni='upload_max_filesize = '$upload_max_filesize'M\npost_max_size = '$post_max_size'M'
echo -e $texteHtAccess>/var/www/html/.htaccess
echo -e $texteIni>/var/www/html/php.ini
echo -e $texteIni>/var/www/html/.user.ini
echo ''
echo 'Si à l'\''utilisation, vous avez des problèmes lors de la phase d'\''envoi des fichiers, il vous faudra modifier'
echo 'vous même les paramètres upload_max_filesize et post_max_size de votre fichier php.ini.'
echo 'Ce fichier se trouve normalement dans /etc/php/(NuméroDeVersionDePHP)/apache2/php.ini .'
echo 'Sachez néanmoins que cette manipulation est inutile si vous ne souhaitez pas découper de fichiers audio'



chmod -R 777 /var/www/html/coupeTranscriber
