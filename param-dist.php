<?php
/**
 * ApoSE param.php
 * php version 7
 *
 * @category Education
 * @package  Apose
 * @author   2014 - CRI Université Lille 2 <cri@univ-lille.fr>
 * @author   2021-2024 - UniCA DSI <dsi.sen@univ-cotedazur.fr>
 * @author   2022 - Université Toulouse 1 Capitole <dsi@univ-tlse1.fr>
 * @license  GNU GPL
 * @link     https://github.com/suipnice/apose
 */

// Set "YES" for mode test
// Set "NO" for mode prod
define("APP_MODE_TEST", "NO");
// Set "YES" pour debug or "NO" pour cacher debug
define("APP_MODE_DEBUG", "NO");

define("UNIV_NAME", "Université XXX");

if (APP_MODE_TEST === "YES") {
    // #### MODE TEST ####
    define("APP_NAME", "ApoSE TEST");

    // CONNEXION MYSQL.
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "apose2");
    define("PASSWD_MYSQL", "A_CHANGER");
    DEFINE("MYSQL_BASE_DATAS", "APOSE2");
} else {
    // #### MODE PROD ####
    define("APP_NAME", "ApoSE");

    // PARAMETRES DE CONNEXION MYSQL.
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "apose2");
    define("PASSWD_MYSQL", "A_CHANGER");
    define("MYSQL_BASE_DATAS", "APOSE2");
}

// PARAMETRES DE CONNEXION ORACLE.
DEFINE("BASE_ORACLE", "APOPROD");
DEFINE("PASSWD_ORACLE", "A_CHANGER");
DEFINE("USER_ORACLE", "apogee");

// Nombre d'années précédentes à afficher
// (1 pour l'an passé, 0 pour uniquement l'année courante)
DEFINE("NB_PREV_YEAR", 1);

// PARAMETRES CAS.
DEFINE("CAS_HOST", "login.univ.fr");
DEFINE("CAS_PORT", 443);
DEFINE("CAS_URI", "");

// PARAMETRES LDAP.
DEFINE("LDAP_BASE_DN", "ou=people,dc=univ,dc=fr");
DEFINE("LDAP_SERVEUR", "ldap.univ.fr");
DEFINE("LDAP_PORT", 389);
define("LDAP_BIND_RDN", "cn=manager,dc=univ,dc=fr");
define("LDAP_BIND_PWD", "A_CHANGER");

DEFINE("CHEMIN_PUBLIC", "/var/www/public/");

// Lien vers un syllabus pour les éléments terminaux
// Il doit contenir les champs [[cod_elp]] et [[cod_anu]],
// qui seront remplacés par leur valeurs respectives.
DEFINE(
    "SYLLABUS_LINK",
    "https://syllabus.univ.fr/fr/course/router/[[cod_elp]]/[[cod_anu]]"
);
