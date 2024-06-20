# ApoSE : APOGEE Structure des Enseignements

Application de consultation de la décomposition d’une étape modélisée dans Apogée.
Elle est développée en PHP avec authentification CAS/LDAP.
L'authentification est active pour les personnes présente dans le LDAP avec le `edupersonprimaryaffiliation = "staff", "teacher", "faculty", "researcher", "employee"`
ApoSE est une application qui utilise une BDD propre, alimentée avec les données d’Apogée.
Elle s’appuie sur une base de données Mysql alimentée 3 fois par jour (7H/12H/16H) à partir de la base de production d’Apogée (APOPROD).

## VERSION TESTÉE

* PHP Version 7.1
* MYSQL : V15.1 distribution 5.5.68 - mariaDB
* OCI8 Support enabled OCI8 Version 2.1.8

## BASE DE DONNÉES

Utilisez le fichier `apose.sql` pour créer la structure de la base de données.

## INSTALLATION

Placez tout le contenu de ce dépôt sur votre serveur Web, mais partagez uniquement le dossier `public`, le dossier parent ne doit pas être accessible par le web.
De manière générale, ne donnez jamais accès à un dossier .git directement sur le web.

## PARAMÈTRES DE L'APPLICATION

Copiez le fichier `param-dist.php` en `param.php` et modifiez-le pour configurer votre application.
ApoSE présente 2 modes : PROD et TEST qui peuvent chacun utiliser une BDD mysql différente.

```php
// Set "YES" for test mode or "NO" for prod mode
define("APP_MODE_TEST","YES");
```

L’application présente 1 mode DEBUG pour afficher les requetes executées

```php
// Set "YES" for debug
define("APP_MODE_DEBUG","YES");
```

Dans le fichier param.php, il faut reseigner :

```php
// PARAMETRES DE CONNEXION MYSQL BASE APOSE(pour test et prod)
// PARAMETRES DE CONNEXION ORACLE BASE APOGEE
// PARAMETRES CAS
// PARAMETRES LDAP
```

## RÉCUPÉRATION DES DONNÉES D’APOGEE

`html_apose\cron\apogee_recup.php` est le script pour lire les données dans Apogee et les insérer dans la BDD MySQL apose
Il est exécuté par le crontab apache sur le fichier `html_apose/cron/apogee_recup.sh`
Il peut également est appelé en direct

```sh
crontab -u apache -e
0 7,12,16 * * * /var/www/html_apose/cron/apogee_recup.sh
```

**ATTENTION**
Les variables d’environnements pour Oracle ne fonctionnent pas si on appelle le script en direct.
Les variables sont ajoutées dans le fichier `/etc/sysconfig/httpd`.

```conf
LANG=C
ORACLE_HOME=/opt/ora12cli/product/12.2.0/client_1
LD_LIBRARY_PATH=/opt/ora12cli/product/12.2.0/client_1/lib:/lib:/usr/lib:/usr/lib64
```
