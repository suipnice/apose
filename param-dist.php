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
// Set "NO" for mode prod.
define("APP_MODE_TEST", "NO");
// Set "YES" pour debug or "NO" pour cacher debug.
define("APP_MODE_DEBUG", "NO");

define("UNIV_NAME", "Université XXX");

if (APP_MODE_TEST === "YES") {
    // #### MODE TEST ####
    define("APP_NAME", "ApoSE TEST");

    // CONNEXION MYSQL.
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "aposetest");
    define("PASSWD_MYSQL", "A_CHANGER");
    define("MYSQL_BASE_DATAS", "APOSETEST");
} else {
    // #### MODE PROD ####
    define("APP_NAME", "ApoSE");

    // PARAMETRES DE CONNEXION MYSQL.
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "apose");
    define("PASSWD_MYSQL", "A_CHANGER");
    define("MYSQL_BASE_DATAS", "APOSE");
}

// PARAMETRES DE CONNEXION ORACLE.
define("BASE_ORACLE", "APOPROD");
define("PASSWD_ORACLE", "A_CHANGER");
define("USER_ORACLE", "apogee");

// Nombre d'années précédentes à afficher
// (1 pour l'an passé, 0 pour uniquement l'année courante).
define("NB_PREV_YEAR", 1);

// PARAMETRES CAS.
define("CAS_HOST", "login.univ.fr");
define("CAS_PORT", 443);
define("CAS_URI", "");

// PARAMETRES LDAP.
define("LDAP_BASE_DN", "ou=people,dc=univ,dc=fr");
define("LDAP_SERVEUR", "ldap.univ.fr");
define("LDAP_PORT", 389);
define("LDAP_BIND_RDN", "cn=manager,dc=univ,dc=fr");
define("LDAP_BIND_PWD", "A_CHANGER");

// Affiliations autorisées à utiliser ApoSE
define(
    "AUTHORIZED",
    [
        "staff",
        "teacher",
        "faculty",
        "researcher",
        "employee"
    ]
);

define("CHEMIN_PUBLIC", "/var/www/public/");

// Lien vers un syllabus pour les éléments terminaux
// Il doit contenir les champs [[cod_elp]] et [[cod_anu]],
// qui seront remplacés par leur valeurs respectives.
define(
    "SYLLABUS_LINK",
    "https://syllabus.univ.fr/fr/course/router/[[cod_elp]]/[[cod_anu]]"
);
