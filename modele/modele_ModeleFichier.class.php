<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Cette classe gère entièrement les fichiers qui seront reçus et créés par l'application.
 */

include_once('modele/modele_ModeleAbstrait.class.php');
include_once('modele/modele_ModeleTour.class.php');
include_once('modele/modele_ModeleLocuteur.class.php');

class Fichier extends ModeleAbstrait
{
    /**
     * @var int La taille maximale de fichier (de transcription) acceptée. À modifier par l'utilisateur si il en a besoin.
     * @example 2000000
     */
    protected $limiteTailleDeFichierTranscription = 2000000; // Soit à peu près 2Mo par fichier à une vache près.

    /**
     * @var int La taille maximale de fichier (sonore) acceptée. À modifier par l'utilisateur si il en a besoin.
     * @example 100000000
     */
    protected $limiteTailleDeFichierSonore = 100000000;

    /**
     * @var string L'emplacement du fichier à étudier et découper.
     * @example '/home/Documents/monDocument.trs'
     */
    protected $emplacementFichier = '';

    /**
     * @var string Le nom du fichier (extension comprise).
     * @example 'monDocument.trico'
     */
    protected $nomFichier='';

    /**
     * @var string L'extension du fichier. Soit trs soit trico.
     * @example 'trico'
     */
    protected $extensionFichier = '';

    /**
     * @var array Il s'agit de données présentent dans l'en-tête XML du fichier. Je les entrepose ici faute de mieux.
     * @example $metaDonnees['scribe']         = 'nomTranscripteur';
     * @example $metaDonnees['version']        = '34';
     * @example $metaDonnees['audio_filename'] = 'fichierAudio.wmv';
     * @example $metaDonnees['version_date']   = '150303';
     * @example $metaDonnees['elapsed_time']   = '0';
     */
    protected $metaDonnees = array();

    /**
     * @var array La liste (ordonnée) des tours (sous forme d'objets Tour) du fichier.
     */
    protected $listeTours = array();

    /**
     * @var array La liste des locuteurs (sous forme d'objets Locuteur) de ce tour.
     */
    protected $listeLocuteurs = array();

    /**
     * @var string Le chrono d'où devra commencer le nouveau fichier. Correspond à l'attribut startTime de la balise Section.
     * Peut correspondre à l'attribut startTime du premier tour.
     * @example = '0'
     */
    protected $chronoDebut = '';

    /**
     * @var string Le chrono où s'arrêtera le nouveau fichier. Correspond à l'attribut endTime de la balise Section.
     * Peut correspondre à l'attribut endTime du dernier tour.
     * @example '618.560'
     */
    protected $chronoFin = '';

    /**
     * @var string La transcription peut être lie à un fichier sonore qu'il faudra alors découpé comme la transcription.
     * @example 'monFichierAudio.mp3'
     */
    protected $fichierSonAssocie = '';

    /**
     * @var string L'emplacement du fichier sonore(extension comprise).
     * @example '/home/Documents/monFichierAudio.ogg'
     */
    protected $emplacementFichierSonAssocie = '';

    /**
     * @var string L'utilisateur souhaite-t-il recaler les sons ? String pour le moment, TODO Bool plus tard ?
     * @example 'recalculer'
     * @example 'laisser' ou autre chose est équivalent pour le moment
     */
    protected $choixSynchro = '';

    /**
     * @var string L'emplacement complet du fichier final.
     * @example '/var/www/html/coupeTranscriber/resultats/monFichierFinal.trs' dans le cas d'un simple
     * découpage de transcription
     * @example '/var/www/html/coupeTranscriber/resultats/monArchiveFinale.zip' si un découpage sonore a également
     * été demandé.
     */
    protected $fichierFinal = '';


    /**
     * Fichier constructor. Hop hop, le constructeur gère tout et appelle toutes les classes/méthodes nécessaires.
     */
    public function __construct($donnees = '')
    {
        if($donnees != '')
        {
            //echo $donnees['nomFichierTranscription'];
            $this->nomFichier                   = $donnees['nomFichierTranscription'];
            $this->emplacementFichier           = 'uploads/' . $this->nomFichier;
            $this->extensionFichier             = pathinfo($this->nomFichier)['extension'];
            $this->fichierSonAssocie            = $donnees['nomFichierSonore'];
            $this->emplacementFichierSonAssocie = getcwd() . '/uploads/' . $this->fichierSonAssocie;
            $this->chronoDebut                  = $donnees['chronoDebut'];
            $this->chronoFin                    = $donnees['chronoFin'];
            $this->choixSynchro                 = strtolower($donnees['actionChrono']);
            $this->parcourirFichier();
            $this->creerResultat($this->reconstruction());
        }
        else
        {
            $this->upload();
            $this->parcourirFichier();
        }
        //var_dump($this->listeTours);
    }

    /**
     * Une fois tout le traitement du texte effectué, on écrit celui-ci dans un fichier. Si le découpage sonore a
     * également été demandé, on le fait ici aussi et on zippe la transcription et l'audio ensemble. Enfin,
     * on efface d'éventuels fichiers interméduiares/uploadés restés.
     * @param $texteTranscription Le texte final sous forme brute.
     */
    protected function creerResultat($texteTranscription)
    {
        /*
         * Cas :
         * - Une transcription seule --> On créé un fichier vide, on écrit le texte dedans et on lance le téléchargement.
         * - Une transcription + un fichier sonore --> Pareil que ci-dessus + découpage du fichier sonore, création
         *   d'une archive contenant les deux fichiers nouvellement créés (transcription et son découpé)
         */

        /*****************************************
         *              TRANSCRIPTION
         *****************************************/
        // Dans tous les cas, on commence par créer le nouveau fichier de transcription.
        $emplacementResultatTranscription = getcwd() . '/resultats/' . $this->nomFichier;
        file_put_contents($emplacementResultatTranscription, $texteTranscription);
        $this->fichierFinal = $emplacementResultatTranscription;

        /*****************************************
         *              AUDIO
         *****************************************/
        if($this->fichierSonAssocie != '')
        {
            // On découpe le fichier son selon les deux balises de temps reçue.

            // COMMANDE
            // avconv -y -ss debutSequenceEnSecondes -i fichierSource -t dureeDeLaSectionADecouper -vcodec copy -acodec copy fichierSortie
            // -vcodec copy -acodec copy  <-- Signifie qu'on garde les codecs tels quels. Pas de conversion/transcodage.
            // -y pour écraser un éventuel fichier déjà présent.
            $duree = floatval($this->chronoFin) - floatval($this->chronoDebut);
            $fichierSonDeSortie = getcwd() . '/resultats/' . $this->fichierSonAssocie;
            $fichierlog = getcwd() . '/resultats/logs.txt';
            $commande = 'avconv -y -ss ' . $this->chronoDebut . ' -i ' . $this->emplacementFichierSonAssocie . ' -t ' .
                        $duree . ' -vcodec copy -acodec copy ' . $fichierSonDeSortie . ' 2>' . $fichierlog;
            //echo $commande;
            exec($commande);
        /*****************************************
        *              ARCHIVE
        *****************************************/
            // L'archive contient la transcription et le fichier sonore. Elle n'est créée que lorsque un fichier sonore est demandé.
            $emplacementArchive = getcwd() . '/resultats/' . $this->nomFichier . '.zip';
            // Version 7z universelle mais requérant l'installation de 7zip
            // $commande = '7z a ' . $emplacementArchive . ' ' . $fichierSonDeSortie . ' ' . $emplacementResultatTranscription;
            // Version zip fonctionnant sur de nombreux systèmes.
            $commande = 'zip -j ' . $emplacementArchive . ' ' . $fichierSonDeSortie . ' ' . $emplacementResultatTranscription;
            // |--> l'option -j signifie que l'arborescence des fichiers archivés N'est PAS respectée.
            //echo $commande;
            exec($commande);

            // Les deux fichiers sont à présent dans l'archive. On peut les supprimer du dossier /résultats.
            $commande = 'rm ' . $fichierSonDeSortie . ' ' . $emplacementResultatTranscription;
            exec($commande);

            $this->fichierFinal = $emplacementArchive;
        }

        // Vidons également le dossier /uploads.
        $commande = 'rm ' . $this->emplacementFichier . ' ' . $this->emplacementFichierSonAssocie;
        exec($commande);
    }

    /**
     * Lorsque l'utilisateur a fini de télécharger son résultat, il est judicieux d'effacer le fichier final, celui-ci
     * pouvant être encombrant en cas de découpage audio.
     */
    public function effacerFichierFinal()
    {
        $commande = 'rm ' . $this->fichierFinal;
        exec($commande);
    }


    /**
     * Cette classe parcourt le fichier reçu (trs ou trico) et en extrait toutes les données posibles.
     */
    protected function parcourirFichier()
    {
        // On parcourt le fichier pour en extraire les tours.
        $refl = new ReflectionClass('XMLReader');
        $xml_consts = $refl->getConstants();
        $xml = file_get_contents($this->emplacementFichier);
        $reader = new XMLReader();
        $reader->XML($xml, NULL, LIBXML_NOWARNING|LIBXML_NOERROR ); // Faire sauter tous les paramètres sauf le premier
                                                                    // pour voir les erreurs inhérentes au fichier.
        // On s'en fout si la dtd est absente.
        $reader->setParserProperty(XMLReader::VALIDATE, false);
        $nouveauTour = new Tour();

        //              MÉTADONNÉES
        $this->metaDonnees['scribe']         = '';
        $this->metaDonnees['version']        = '';
        $this->metaDonnees['audio_filename'] = '';
        $this->metaDonnees['version_date']   = '';
        $this->metaDonnees['elapsed_time']   = '';

        while ($reader->read())
        {
            switch ($reader->nodeType)
            {
                case XMLReader::TEXT:
                    $nouveauTour->insertionTexte($reader->value);
                    break;

                case XMLReader::ELEMENT:
                    switch ($reader->name)
                    {
                        case 'Trans':
                            if('' . $reader->getAttribute('scribe') != '')
                            {
                                $this->metaDonnees['scribe'] = $reader->getAttribute('scribe');
                            }
                            if('' . $reader->getAttribute('version') != '')
                            {
                                $this->metaDonnees['version'] = $reader->getAttribute('version');
                            }
                            if('' . $reader->getAttribute('audio_filename') != '')
                            {
                                $this->metaDonnees['audio_filename'] = $reader->getAttribute('audio_filename');
                            }
                            if('' . $reader->getAttribute('version_date') != '')
                            {
                                $this->metaDonnees['version_date'] = $reader->getAttribute('version_date');
                            }
                            if('' . $reader->getAttribute('elapsed_time') != '')
                            {
                                $this->metaDonnees['elapsed_time'] = $reader->getAttribute('elapsed_time');
                            }
                            break;

                        case 'Speaker':
                            // On ajoute ce locuteur à la liste.
                            array_push($this->listeLocuteurs, new Locuteur($reader->getAttribute('id'),
                                $reader->getAttribute('name'),
                                $reader->getAttribute('check'),
                                $reader->getAttribute('dialect'),
                                $reader->getAttribute('accent'),
                                $reader->getAttribute('scope')
                            ));
                            break;

                        case 'Turn':
                            // Un nouveau tour donc.
                            $nouveauTour = new Tour();
                            // On lui ajoute un maximum de données récupérables.
                            $nouveauTour->ajoutLocuteurs($reader->getAttribute('speaker'));
                            $nouveauTour->setChronoDebut($reader->getAttribute('startTime'));
                            $nouveauTour->setChronoFin($reader->getAttribute('endTime'));
                            //var_dump($nouveauTour);
                            //echo '<br><br><br>';
                            break;

                        case 'Event':
                            $nouveauTour->insertionEvent($reader->getAttribute('desc'),
                                $reader->getAttribute('type'),
                                $reader->getAttribute('extent'));
                            break;

                        case 'Comment':
                            $nouveauTour->insertionComment($reader->getAttribute('desc'));
                            break;

                        case 'Who':
                            $nouveauTour->insertionWho($reader->getAttribute('nb'));
                            break;

                        case 'Background':
                            $nouveauTour->insertionBackground($reader->getAttribute('time'), $reader->getAttribute('type'), $reader->getAttribute('level'));
                            break;

                        // BALÉKOUYE
                        case 'Speakers':
                        case 'Topics':
                        case 'Topic':
                        case 'Episode':
                        case 'Section':
                        case 'Sync':
                            break;


                        default:
                            $this->erreurs .= 'Balise inconnue trouvée : ' . $reader->name . '<br>';
                            break;
                    }
                    break;

                case XMLReader::END_ELEMENT:
                    if($reader->name == 'Turn')
                    {
                        // Le tour est fini, on peut l'ajouter.
                        array_push($this->listeTours, $nouveauTour);
                        //var_dump($nouveauTour);
                        //echo '<br><br><br>';
                        $nouveauTour = new Tour();
                    }
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Cette méthode recréé la transcription à partir des chronos de départ et de fin et recalcule les nouvelles
     * balises temporelles si cela a été demandé.
     * @return string Le texte final de la transcription une fois découpée.
     */
    public function reconstruction()
    {
        $dtd = 'trans-14.dtd';
        if($this->extensionFichier == 'trico')
        {
            $dtd = 'transicor.dtd';
        }

        // Ici, on recréé le contenu d'un fichier .trs ou .trico en fonction des besoins.
        $texteXML = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Trans SYSTEM "' . $dtd . '">';

        // MÉTADONNÉES
        $texteXML .= '
<Trans scribe="' . $this->getMetaDonnees()['scribe'] . '" version="' . $this->getMetaDonnees()['version'] . '"
       audio_filename="' . $this->getMetaDonnees()['audio_filename'] . '"
       version_date="' . $this->getMetaDonnees()['version_date'] . '"
       elapsed_time="' . $this->getMetaDonnees()['elapsed_time'] . '">';

        // LOCUTEURS
        $texteXML .= '
    <Speakers>';
        foreach($this->getListeLocuteurs() as $locuteur)
        {
            $texteXML .= '
        <Speaker id="' . $locuteur->getId() .
              '" name="' . $locuteur->getName() .
              '" check="' . $locuteur->getCheck() .
              '" dialect="' . $locuteur->getDialect() .
              '" accent="' . $locuteur->getAccent() .
              '" scope="' . $locuteur->getSCope() . '" />';
        }
        $texteXML .= '
    </Speakers>';

        // TOURS
        $texteXML .= '
    <Episode>
        ' . $this->genererBaliseSection($this->getListeTours(), $this->chronoDebut, $this->chronoFin, $this->choixSynchro);
        $texteXML .= $this->regenererListeTours($this->getListeTours(), $this->chronoDebut, $this->chronoFin, $this->choixSynchro);

        // FIN
        $texteXML .= '
        </Section>
    </Episode>
</Trans>';
        //echo $texteXML;
        return $texteXML;
    }

    /**
     * Cette fonction sélectionne les tours à garder et les recréé.
     * @param $listeTours La liste complète des tours du fichier initial.
     * @param $chronoDebut À quel moment l'utilisateur souhaite-t-il commencer la nouvelle transcription ?
     * @param $chronoFin À quel moment l'utilisateur souhaite-t-il achever la nouvelle transcription ?
     * @param $choixSynchro Faut-il laisser les chronos des tours tels quels ou les recalculer ?
     * @return string Le texte brut et concaténé des tours.
     */
    protected function regenererListeTours($listeTours, $chronoDebut, $chronoFin, $choixSynchro)
    {
        $balisesTRS = '';
        $chronoDebut = floatval($chronoDebut);
        $chronoFin   = floatval($chronoFin);

        foreach($listeTours as $tour)
        {
            if(floatval($tour->getChronoDebut()) >= $chronoDebut && floatval($tour->getChronoFin()) <= $chronoFin )
            {
                // Les tours qui correspondent aux bornes demandés par l'utilisateur. Tous les autres sont à ignorer.
                $chronoDebutTour = $tour->getChronoDebut();
                $chronoFinTour   = $tour->getChronoFin();
                if(strtolower($choixSynchro) =='recalculer')
                {
                    $chronoFinTour   = floatval($chronoFinTour) - floatval($chronoDebut);
                    $chronoDebutTour = floatval($chronoDebutTour) - floatval($chronoDebut);
                }
                $balisesTRS .= '
            <Turn speaker="' . $tour->getLocuteurs() . '" startTime="' . $chronoDebutTour . '" endTime="' . $chronoFinTour . '">
                <Sync time="' . $chronoDebutTour . '" />';
                $balisesTRS .= $this->regenererDeroulementDuTour($tour, $chronoDebut) . '</Turn>';
            }
        }

        return $balisesTRS;
    }

    /**
     * @param $tour Un objet Tour à regénérer au format .trs ou .trico
     * @param $chronoDebut Le chrono de départ choisi par l'utilisateur. Sert à recaler les chronos si besoin.
     * @return string Le texte brut du tour au format .trs ou .trico.
     */
    protected function regenererDeroulementDuTour($tour, $chronoDebut)
    {
        $contenuTour = '';
        foreach($tour->getDeroulementDuTour() as $action)
        {
            if(is_string($action))
            {
                $contenuTour .= str_replace('&', '&amp;', $action);
            }
            else if(get_class($action) == 'Event')
            {
                $contenuTour .= '<Event type="' . $action->getType() . '" desc="' . $action->getDesc() . '" extent="' . $action->getExtent() . '"/>';
            }
            else if(get_class($action) == 'Comment')
            {
                $contenuTour .= '<Comment desc="' . $action->getDesc() . '"/>';
            }
            else if(get_class($action) == 'Who')
            {
                $contenuTour .= '<Who nb="' . $action->getNb() . '"/>';
            }
            else if(get_class($action) == 'Background')
            {
                $contenuTour .= '<Background time="' . floatval($action->getTime())-$chronoDebut . '" type="' . $action->getType() . '" level="' . $action->getLevel() . '"/>';
            }
            else
            {
                //var_dump($action);
            }
        }
        return $contenuTour;
    }


    /**
     * @param $listeTours La liste complète des tours.
     * @param $chronoDebut Le chrono de départ choisi par l'utilisateur.
     * @param $chronoFin Le chrono de fin choisi par l'utilisateur.
     * @param $choixSynchro Faut-il recaler les chronos ?
     * @return string Le texte brut de la balise Section au format .trs ou .trico.
     */
    protected function genererBaliseSection($listeTours, $chronoDebut, $chronoFin, $choixSynchro)
    {
        if($choixSynchro == 'recalculer')
        {
            $chronoFin = floatval($chronoFin)-floatval($chronoDebut);
            $chronoDebut = 0;
        }
        return '<Section type="report" startTime="' . $chronoDebut . '" endTime="' . $chronoFin . '">';
    }

    /**
     * Cette classe reçoit en entrée un chrono de type '65.122' et doit le convertir en 00:01:05.122
     * @param string $chrono
     * @return string
     */
    public function convertirChrono($chrono = '')
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


        return '[' . $heures . '-' . $minutes . '-' . $secondes . '-' . $decimales .  ']';
    }

    /**
     * Gestion complète de la réception des fichiers de transcription et audio.
     */
    protected function upload()
    {
        // Il y a au moins un fichier de transcription à récupérer et potentiellement un fichier son également.
        // On commence par récupérer le fichier
        if (isset($_FILES['fichierTranscription']) AND $_FILES['fichierTranscription']['error'] == 0)
        {
            // Testons si le fichier n'est pas trop gros
            if ($_FILES['fichierTranscription']['size'] <= $this->limiteTailleDeFichierTranscription)
            {
                // Testons si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['fichierTranscription']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('trs', 'trico');
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    // Tout est OK. On peut crécupérer le fichier.
                    $this->emplacementFichier = 'uploads/' . basename($_FILES['fichierTranscription']['name']);
                    $this->nomFichier = basename($_FILES['fichierTranscription']['name']);
                    $this->extensionFichier = pathinfo($this->nomFichier)['extension'];
                    move_uploaded_file($_FILES['fichierTranscription']['tmp_name'], $this->emplacementFichier);
                }
                else
                {
                    $this->erreurs .= 'Erreur : Le fichier que vous avez envoyé n\'est pas une extension autorisée : ' . $extension_upload . '<br>';
                }
            }
            else
            {
                $this->erreurs .= 'Erreur : Le fichier de transcription est certainement trop gros. Résolvez ce problème ainsi :
                <ol>
                    <li>Augmentez la taille des fichiers que votre serveur (Apache ou autre) peut accepter.</li>
                    <li>Augmentez la limite de taille des fichiers à accepter dans la classe « Fichier » située dans « coupeTranscriber/modele/modele_ModeleFichier.class.php »</li>
                </ol>
                <br>';
            }
        }
        else
        {
            $this->erreurs .= 'Erreur lors de la transmission du fichier de transcriptions.<br>';
        }
        /*
         *                          FICHIER SONORE
         */
        if (isset($_FILES['fichierSonore']) AND $_FILES['fichierSonore']['error'] == 0)
        {
            // Testons si le fichier n'est pas trop gros
            if ($_FILES['fichierSonore']['size'] <= $this->limiteTailleDeFichierSonore)
            {
                // Testons si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['fichierSonore']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('wav', 'mp3', 'ogg');
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    // Tout est OK. On peut crécupérer le fichier.
                    $this->emplacementFichierSonAssocie = getcwd() . '/uploads/' . basename($_FILES['fichierSonore']['name']);
                    $this->fichierSonAssocie = basename($_FILES['fichierSonore']['name']);
                    $this->extensionFichier = pathinfo($this->nomFichier)['extension'];
                    move_uploaded_file($_FILES['fichierSonore']['tmp_name'], $this->emplacementFichierSonAssocie);
                }
                else
                {
                    $this->erreurs .= 'Erreur : Le fichier sonore que vous avez envoyé n\'est pas une extension autorisée : ' . $extension_upload . '<br>';
                }
            }
            else
            {
                $this->erreurs .= 'Erreur : Le fichier sonore est certainement trop gros. Résolvez ce problème ainsi :
                <ol>
                    <li>Augmentez la taille des fichiers que votre serveur (Apache ou autre) peut accepter.</li>
                    <li>Augmentez la limite de taille des fichiers à accepter dans la classe « Fichier » située dans « coupeTranscriber/modele/modele_ModeleFichier.class.php »</li>
                </ol>
                <br>';
            }
        }
    }

    /**
     * Cette méthode récupère tous les attributs startTime et endTime de chaque tour de la transcription reçue et en
     * renvoie la liste.
     * @return array La liste de tous les chronos contenus dans les tours.
     */
    public function getListeBalisesTemporelles()
    {
        /*
         * Pour récupérer la liste des balises, il suffit de parcourir la liste des tours et d'en extraire toutes
         * les balises de début et de fin.
         */
        $listeChronos = array();
        foreach($this->listeTours as $tour)
        {
            if(!in_array($tour->getChronoDebut(), $listeChronos))
            {
                array_push($listeChronos, $tour->getChronoDebut());
            }
            if(!in_array($tour->getChronoFin(), $listeChronos))
            {
                array_push($listeChronos, $tour->getChronoFin());
            }
        }
        sort($listeChronos);
        return $listeChronos; // On fait un dernier tri au cas où.
    }

    /**
     * @return string
     */
    public function getEmplacementFichier()
    {
        return $this->emplacementFichier;
    }

    /**
     * @return array
     */
    public function getMetaDonnees()
    {
        return $this->metaDonnees;
    }

    /**
     * @return array
     */
    public function getListeTours()
    {
        return $this->listeTours;
    }

    /**
     * @return array
     */
    public function getListeLocuteurs()
    {
        return $this->listeLocuteurs;
    }

    /**
     * @return string
     */
    public function getNomFichier()
    {
        return $this->nomFichier;
    }

    /**
     * @return string
     */
    public function getChronoDebut()
    {
        return $this->chronoDebut;
    }

    /**
     * @return string
     */
    public function getChronoFin()
    {
        return $this->chronoFin;
    }

    /**
     * @return string
     */
    public function getExtensionFichier()
    {
        return $this->extensionFichier;
    }

    /**
     * @return int
     */
    public function getLimiteTailleDeFichierTranscription()
    {
        return $this->limiteTailleDeFichierTranscription;
    }

    /**
     * @return int
     */
    public function getLimiteTailleDeFichierSonore()
    {
        return $this->limiteTailleDeFichierSonore;
    }

    /**
     * @return string
     */
    public function getFichierSonAssocie()
    {
        return $this->fichierSonAssocie;
    }

    /**
     * @return string
     */
    public function getEmplacementFichierSonAssocie()
    {
        return $this->emplacementFichierSonAssocie;
    }

    /**
     * @return string
     */
    public function getChoixSynchro()
    {
        return $this->choixSynchro;
    }

    /**
     * @return string
     */
    public function getFichierFinal()
    {
        return $this->fichierFinal;
    }
}