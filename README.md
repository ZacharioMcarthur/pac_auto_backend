# Service Backend Parc Automobile PAC

Service backend du projet Parc Automobile PAC

## Installation

**Prérequis :**
* Composer [Installer Composer](https://getcomposer.org/download/)
* Un éditeur de code (Visual Studio Code par exemple) [Installer Visual Studio code](https://code.visualstudio.com/download)

**Cloner le projet backend :**
```bash
git clone https://github.com/bescportcotonou/parcautopacservices.git
```
<br>

**Installer les dépendances :**
```bash
composer install
```
<br>

**Jouer les migrations :**
```bash
php artisan migrate
```
<br>

**Installer Laravel Passport pour l'authentification :**
```bash
php artisan passport:install
```
<br>


**Lancement du projet :** <br>
```bash
 php artisan serve
```
