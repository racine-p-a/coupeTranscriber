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

                        // BALÉKOUYE
                        case 'Speakers':
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


}