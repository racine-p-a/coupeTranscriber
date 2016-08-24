<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Toutes les classes du modèle hétitent de cette classe (juste pour gérer les erreurs).
 */

class ModeleAbstrait
{
    /**
     * @var string Un message d'erreur écrit sous forme compréhensible ! Il sera affiché tel quel à l'utilisateur
     * pour l'aider.
     */
    protected $erreurs = '';

    /**
     * @return string La liste de toutes les erreurs levées sous une forme lisible par l'utilisateur.
     * Renvoie la liste concaténée de tous les messages d'erreurs levés au cours du processus.
     */
    public function getErreurs()
    {
        return $this->erreurs;
    }

}