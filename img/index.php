<?php
/**
 * @author Pierre-Alexandre RACINE
 * @licence CeCILL-B
 * @license FR http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html
 * @license EN http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 * Sert à ramener tout visiteur trop curieux à l'accueil.
 */

$adresse = $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//echo dirname($adresse);
header('Location: http://' . dirname($adresse));
exit();