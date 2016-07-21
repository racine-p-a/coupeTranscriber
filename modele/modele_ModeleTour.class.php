<?php

include_once('modele/modele_ModeleEvent.class.php');
include_once('modele/modele_ModeleComment.class.php');
include_once('modele/modele_ModeleWho.class.php');
include_once('modele/modele_ModeleBackground.class.php');

class Tour
{
    /**
     * @var string Quand commence ce tour.
     */
    protected $chronoDebut = '';

    /**
     * @var string Quand s'achève ce tour.
     */
    protected $chronoFin = '';

    /**
     * @var string Quels sont les locuteurs de ce tour.
     */
    protected $locuteurs = '';

    /**
     * @var array Ce tableau représente la succession d'évènements dans ce tour.
     */
    protected $deroulementDuTour = array();


    /**
     * @param string $locuteurs Agit (pour le moment) comme un setter des locuteurs pour ce tour.
     */
    public function ajoutLocuteurs($locuteurs = '')
    {
        if('' . $locuteurs != '')
        {
            $this->locuteurs = $locuteurs;
            //echo 'ajout de ' . $locuteurs . '<br>';
        }
    }

    /**
     * @param string $texteAAjouter L texte à ajouter au déroulement du tour.
     */
    public function insertionTexte($texteAAjouter = '')
    {
        array_push($this->deroulementDuTour, $texteAAjouter);
        //echo 'ajout du texte : ' . $texteAAjouter . '<br>';
    }

    /**
     * Permet d'ajouter un event au tour.
     * @param string $desc
     * @param string $type
     * @param string $extent
     */
    public function insertionEvent($desc='', $type='', $extent='')
    {
        array_push($this->deroulementDuTour, new Event($desc, $type, $extent));
    }

    public function insertionBackground($time='', $type='', $level='')
    {
        array_push($this->deroulementDuTour, new Background($time, $type, $level));
    }

    /**
     * Permet d'ajouter un comment au tour.
     * @param string $desc
     */
    public function insertionComment($desc='')
    {
        array_push($this->deroulementDuTour, new Comment($desc));
    }

    public function insertionWho($nb='1')
    {
        array_push($this->deroulementDuTour, new Who($nb));
    }

    /**
     * @param string $chronoDebut
     */
    public function setChronoDebut($chronoDebut)
    {
        $this->chronoDebut = $chronoDebut;
    }

    /**
     * @param string $chronoFin
     */
    public function setChronoFin($chronoFin)
    {
        $this->chronoFin = $chronoFin;
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
    public function getLocuteurs()
    {
        return $this->locuteurs;
    }

    /**
     * @return array
     */
    public function getDeroulementDuTour()
    {
        return $this->deroulementDuTour;
    }




}