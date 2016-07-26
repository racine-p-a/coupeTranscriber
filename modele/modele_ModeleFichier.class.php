<?php

include_once('modele/modele_ModeleAbstrait.class.php');

include_once('modele/modele_ModeleTour.class.php');
include_once('modele/modele_ModeleLocuteur.class.php');

class Fichier extends ModeleAbstrait
{
    /**
     * @var int La taille maximale de fichier acceptée. À modifier par l'utilisateur si il en a besoin.
     */
    protected $limiteTailleDeFichier = 2000000; // Soit à peu près 2Mo par fichier à une vache près.

    /**
     * @var string L'emplacement du fichier à étudier et découper.
     */
    protected $emplacementFichier = '';

    /**
     * @var string Le nom du fichier (extension comprise).
     */
    protected $nomFichier='';

    /**
     * @var array Il s'agit de données présentent dans l'en-tête XML du fichier. Je les entrepose ici faute de mieux.
     */
    protected $metaDonnees = array();

    /**
     * @var array La liste des tours du fichier.
     */
    protected $listeTours = array();

    /**
     * @var array La liste des locuteurs de ce tour.
     */
    protected $listeLocuteurs = array();

    /**
     * @var string Le chrono d'où devra commencer le nouveau fichier.
     */
    protected $chronoDebut = '';

    /**
     * @var string Le chrono où s'arrêtera le nouveau fichier.
     */
    protected $chronoFin = '';


    /**
     * Fichier constructor. Hop hop, le constructeur gère tout et appelle toutes les classes nécessaires.
     */
    public function __construct()
    {
        /*
         *                          UPLOAD
         */
        // On commence par récupérer le fichier
        if (isset($_FILES['fichierADecouper']) AND $_FILES['fichierADecouper']['error'] == 0)
        {
            // Testons si le fichier n'est pas trop gros
            if ($_FILES['monfichier']['size'] <= $this->limiteTailleDeFichier)
            {
                // Testons si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['fichierADecouper']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('trs', 'trico');
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    // Tout est OK. On peut crécupérer le fichier.
                    $this->emplacementFichier = 'uploads/' . basename($_FILES['fichierADecouper']['name']);
                    $this->nomFichier = basename($_FILES['fichierADecouper']['name']);
                    move_uploaded_file($_FILES['fichierADecouper']['tmp_name'], $this->emplacementFichier);
                    /*
                     *              DÉCOUPAGE
                     */
                    // On a le fichier, y a plus qu'à l'éplucher tour par tour.
                    $this->parcourirFichier();
                }
                else
                {
                    $this->erreurs .= 'Erreur : Le fichier que vous avez envoyé n\'est pas une extension autorisée : ' . $extension_upload . '<br>';
                }
            }
            else
            {
                $this->erreurs .= 'Erreur : Le fichier est certainement trop gros. Résolvez ce problème ainsi :
                <ol>
                    <li>Augmentez la taille des fichiers que votre serveur (Apache ou autre) peut accepter.</li>
                    <li>Augmentez la limite de taille des fichiers à accepter dans la classe « Fichier » située dans « coupeTranscriber/modele/modele_ModeleFichier.class.php »</li>
                </ol>
                <br>';
            }
        }
        else
        {
            $this->erreurs .= 'Erreur lors de la transmission du fichier<br>';
        }
        //var_dump($this->listeTours);
    }


    protected function parcourirFichier()
    {
        // On parcourt le fichier pour en extraire les tours.
        $refl = new ReflectionClass('XMLReader');
        $xml_consts = $refl->getConstants();

        $xml = file_get_contents($this->emplacementFichier);
        $reader = new XMLReader();
        $reader->XML($xml);

        // On s'en fout si la dtd est absente.
        $reader->setParserProperty(XMLReader::VALIDATE, false);

        $nouveauTour = new Tour();

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

    public function reconstruction($donnees, $chronoDebut, $chronoFin, $choixSynchro)
    {
        $this->chronoDebut = $chronoDebut;
        $this->chronoFin   = $chronoFin;
        // Ici, on recréé le contenu d'un fichier .trs en fonction des besoins.
        $texteTRS = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Trans SYSTEM "trans-14.dtd">';

        // MÉTADONNÉES
        $texteTRS .= '
<Trans scribe="' . $donnees->getMetaDonnees()['scribe'] . '" version="' . $donnees->getMetaDonnees()['version'] . '"
       audio_filename="' . $donnees->getMetaDonnees()['audio_filename'] . '"
       version_date="' . $donnees->getMetaDonnees()['version_date'] . '"
       elapsed_time="' . $donnees->getMetaDonnees()['elapsed_time'] . '">';

        // LOCUTEURS
        $texteTRS .= '
    <Speakers>';
        foreach($donnees->getListeLocuteurs() as $locuteur)
        {
            $texteTRS .= '
        <Speaker id="' . $locuteur->getId() .
              '" name="' . $locuteur->getName() .
              '" check="' . $locuteur->getCheck() .
              '" dialect="' . $locuteur->getDialect() .
              '" accent="' . $locuteur->getAccent() .
              '" scope="' . $locuteur->getSCope() . '" />';
        }
        $texteTRS .= '
    </Speakers>';

        // TOURS
        $texteTRS .= '
    <Episode>
        ' . $this->genererBaliseSection($donnees->getListeTours(), $chronoDebut, $chronoFin, $choixSynchro);
        $texteTRS .= $this->regenererListeTours($donnees->getListeTours(), $chronoDebut, $chronoFin, $choixSynchro);

        // FIN
        $texteTRS .= '
        </Section>
    </Episode>
</Trans>';
        //echo $texteTRS;
        return $texteTRS;
    }




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
                if($choixSynchro =='recalculer')
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

    public function nettoyerFichiers($nomFichierEnEntree = '', $nomFichierDeSortie = '')
    {
        // Il faut supprimer le fichier original ainsi que la version découpée.

        // Fichier original
        exec('rm uploads/' . $nomFichierEnEntree);

        // Fichier de sortie
        exec('rm resultats/' . $nomFichierDeSortie);
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






}