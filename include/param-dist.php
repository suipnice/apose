<?php

//Set "YES" for mode test
//Set "NO" for mode prod
define("APP_MODE_TEST", "NO");
//Set "YES" pour debug or "NO" pour cacher debug
define("APP_MODE_DEBUG", "NO");

define("UNIV_NAME", "Université XXX");

if (APP_MODE_TEST == "YES") {
    // #### MODE TEST ####
    define("APP_NAME", "ApoSE TEST");

    // CONNEXION MYSQL
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "apose2");
    define("PASSWD_MYSQL", "A_CHANGER");
    DEFINE("MYSQL_BASE_DATAS", "APOSE2");
} else {
    // #### MODE PROD ####
    define("APP_NAME", "ApoSE");

    // PARAMETRES DE CONNEXION MYSQL
    define("HOTE_MYSQL", "mysql.univ.fr");
    define("USER_MYSQL", "apose2");
    define("PASSWD_MYSQL", "A_CHANGER");
    define("MYSQL_BASE_DATAS", "APOSE2");
}

// PARAMETRES DE CONNEXION ORACLE
DEFINE("BASE_ORACLE", "APOPROD");
DEFINE("PASSWD_ORACLE", "A_CHANGER");
DEFINE("USER_ORACLE", "apogee");

// PARAMETRES CAS
DEFINE("CAS_HOST", "login.univ.fr");
DEFINE("CAS_PORT", 443);
DEFINE("CAS_URI", "");

// PARAMETRES LDAP
DEFINE("LDAP_BASE_DN", "ou=people,dc=univ,dc=fr");
DEFINE("LDAP_SERVEUR", "ldap.univ.fr");
DEFINE("LDAP_PORT", 389);
// define("LDAP_BIND_RDN","manager");
// define("LDAP_BIND_PWD","A_CHANGER");
