<?php
/**
 * Created by PhpStorm.
 * User: pracine
 * Date: 18/08/16
 * Time: 14:41
 */

/*
 * L'application RESTful peut être contactée par formulaire, ligne de commande ou autre...
 * Deux cas sont possibles :
 * - on reçoit un fichier de transcription seul --> renvoi de deux listes (la liste des balises de début et celle de fin)
 * - on reçoit un fichier de transcription (plus éventuellement un fichier sonore) et deux balises temporelles
 *   |--> découpage de la transcription (et du fichier sonore si il existe) et téléchargement forcé.
 */