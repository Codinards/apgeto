# **Application de gestion d'une tontine**
Une application PHP basée sur pour la gestion complète d'une tontine associative

## **Installation**
Ouvrir la console dans le dossier parent oú vous voulez installer le dossier de l'application et taper les commandes suivantes:
- git clone git@github.com:Codinards/apgeto.git apgeto
- cd apgeto
- composer install
- npm install
- php -S localhost:8005 -t public/
- ouvrir son navigateur web et taper dans la barre d'adresse **http://localhost:8005**

## **Démo Vidéo**
<video width="480" height="320" controls>
    <source src="demo.mp4" type="video/mp4"></source>
</video>

## **Les fonctionnalités de l'application**


- **Création et gestion des adhérants**
    - Affichage du listing des adhérants
    - Ajout des adhérants
    - Edition du profil des adhérants
    - Edition du rôle d'un adhérant
    - Edition des identifiants d'un adhérant (pseudo et mot de passe)
    - Edition de l'image de profil d'un adhérant


- **Création et gestion des comptes adhérants**
    - Affichage du récapitulatif des comptes(fonds, assurances, prêts)
    - Enregistrement des opérations d'entrée fond
    - Enregistrement des opérations de sortie fond
    - Enregistrement des nouveaux prêts
    - Enregistrement des remboursements prêts
    - Enregistrement des reconductions prêts
    - Edition d'un opération d'entrée fond
    - Edition d'un opération de sortie fond
    - Edition d'un prêt
    - Edition d'un remboursement prêt
    - Edition d'une reconduction prêt
    - Suppression d'un opération d'entrée fond
    - Suppression d'un opération de sortie fond
    - Suppression d'un prêt
    - Suppression d'un remboursement prêt
    - Suppression d'une reconduction prêt
    - Enregistrement des remboursement prêts
    - Affichage des états de fond d'un adhérant
    - Affichage des états de prêt d'un adhérant
    - Affichage des états de fond antérieur d'un adhérant
    - Affichage des états de prêt antérieur d'un adhérant
                

- **Création et gestion des rôles et autorisations**
    - Affichage du listing des rôles
    - Création d'un nouveau rôle
    - Edition d'un rôle
    - Edition des autorisations associées à un rôle
    - Suppression d'un rôle


- **Création et gestion des types de tontines, tontines et cotisations**
    - Affichage du listing des types de tontine
    - Création d'un type de tontine
    - Edition d'un type de tontine
    - Affichage du listing des tontines
    - Création d'une tontine associée à un type
    - Affichage des informations d'une tontine (cotisations, achats, relicats, etc.)
    - Edition d'une tontine
    - Suppression d'une tontine
    - Ajout des cotisations à d'une tontine
    - Retrait des cotisations à d'une tontine
    - Enregistrement et annulation de l'achat, blocage  d'une cotisation
    - Enregistrement des échecs de cotisations
    - Affichage des échecs de cotisations (par date ou par adhérant)


- **Création et gestion des types de aides**
    - Création des types d'aide
    - Affichage du listing des types d'aide
    - Edition d'un type d'aide
    - Octroi d'une aide à un adhérant
    - Affichage de la liste des aides
    - Filtrage de la liste des aides par types, par adhérant, par année ou par contributeur 
    - Ajout des contributeurs à une aide
    - Suppression d'une aide


- **Gestion du tableau de bord et des opérations**
    - Affichage du tableau de bord
    - Affichage du tableau de bord des années antérieurs
    - Création d'une opération
    - Affichage des états d'une opération
    - Affichage du listing des opérations
    - Enregistrement des entrées dans une opération
    - Enregistrement des sorties dans une opération
    - Edition des entrées et sorties d'une opération