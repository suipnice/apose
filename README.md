# ApoSE : APOGEE Structure des Enseignements

## INFO VERSION

PHP Version 7.1
MYSQL : V15.1 distribution 5.5 - mariaDB
OCI8 Support enabled OCI8 Version 2.1

## INFO GENERALE

ApoSE : APOGEE Structure des Enseignements
Application de consultation de la décomposition d’une étape modélisée dans Apogée. Elle est développée en PHP avec authentification CAS/LDAP.
L'authentification est active pour les personnes présente dans le LDAP avec le edupersonprimaryaffiliation = “employee”
APOSE est une application qui utilise une BDD propre, alimentée avec les données d'Apogée. La BDD APOSE est rafraîchie 3 fois par jour
Elle s’appuie sur une base de données Mysql alimentée 3 fois par jour (7H/12H/16H) à partir de la base de production d’Apogée (APOPROD).

## BASE DE DONNEES

apose.sql est le fichier pour créer la structure de la base de données

## PARAMETRE DE L'APPLICATION

html_apose\include\param.php est le fichier pour la configuration de l'application
L'application présente 2 modes PROD et TEST pour la lecture dans la BDD mysql qui sont à configurer
   //Set "YES" for mode test
   //Set "NO" for mode prod
   define("APP_MODE_TEST","YES");

L'application présente 1 mode DEBUG pour afficher les requetes executées
   //Set "YES" pour debug or "NO" pour cacher debug
   define("APP_MODE_DEBUG","YES");

Dans le fichier param.php, il faut reseigner :

```php
#PARAMETRES DE CONNEXION MYSQL BASE APOSE(pour test et prod)
#PARAMETRES DE CONNEXION ORACLE BASE APOGEE
#PARAMETRES CAS
#PARAMETRES LDAP
```

## RECUPERATION DES DONNEES D'APOGEE

`html_apose\cron\apogee_recup.php` est le script pour lire les données dans Apogee et les insérer dans la BDD MySQL apose
Il est exécuté par le crontab apache sur le fichier `html_apose/cron/apogee_recup.sh`
Il peut également est appelé en direct

```sh
crontab -u apache -e
```

```cron
0 7,12,16 * * * /var/www/html_apose/cron/apogee_recup.sh
```

ATTENTION :
Les variables d'environnements pour oracle ne fonctionnent pas si on appelle le script en direct Les variables sont rajoutées dans le fichier /etc/sysconfig/httpd

```conf
   LANG=C
   ORACLE_HOME=/opt/ora12cli/product/12.2.0/client_1
   LD_LIBRARY_PATH=/opt/ora12cli/product/12.2.0/client_1/lib:/lib:/usr/lib:/usr/lib64
```
