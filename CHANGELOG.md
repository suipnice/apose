# ApoSE Change log

## 2026

v26.10 - UniCA - DSI SEN :

* Filtre le nombre d'inscrits (IP) par cod_anu
* Corrige des incompatibilités PHP 8.1 / 8.2 :
  * `ldap_get_entries()` n'est plus appelé après un `ldap_search()` en échec (TypeError fatal)
  * Corrige l'appel à une fonction inexistante `report_error()` (remplacée par `reportError()`)
  * Ne passe plus `null` à `str_replace()` ni à `strpos()` (déprécié depuis PHP 8.1)
* Ajoute des vérifications de compatibilité PHP (GitHub Actions) :
  * Lint de la syntaxe sur PHP 8.2 à 8.5, dépendances comprises
  * PHPCompatibility (phpcs) sur le code et sur phpCAS
  * Analyse statique PHPStan (niveau 5) avec baseline, régénérée automatiquement sur `master`
* Ajoute un `Makefile` (lint, phpcs, PHPStan niveaux 5 et 8, baseline) et la configuration VS Code associée

v26.04 - UniCA - DSI SEN :

* Remplace les espaces insécables / tabulations par des underscores pour éviter que 2 codes APOGEE fusionnent en un seul code APOSE
* Ajoute la composante BSC même si elle n'a pas de code RNE (UAI)

## 2025

v25.06 - New version by UniCA - DSI SEN :

* Optimize cron with multi inserts
* Open Syllabus in a new browser tab
* simplify mysqli connect & close
* Affichage des libellé listes quand il y en a plus d'un
* Display tree with asci chars
* Sticky table headers
* Remove included phpCAS, replace it by composer deps
* Upgrade phpCAS from 1.2.2 to 1.6.1+

## 2024

v24.06 - New version by UniCA - DSI SEN :

* Improve Accessibility
* Add "Copy to clipboard" button
* Now table can be sorted

## 2021

v21.03 - New version by Université Toulouse 1
Use "Bulma CSS" layout and Font Awesome icons

## 2014

First version by "CRI Université Lille 2"
