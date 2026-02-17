# Plateforme Pressing (Laravel 12 - V1)

## Fonctionnalités livrées

### Admin
- Créer des propriétaires (compte + pressing)
- Lister propriétaires, agences, abonnements et pricing
- Admin - Modifier les packs/pricing (nom, prix, limites)
- Admin - Depuis Propriétaires: voir stats détaillées d'un pressing (agences, employés, CA, dépenses, abonnement)

### Propriétaire
- Gestion agences (création, désactivation/réactivation)
- Gestion employés (création, blocage/réactivation)
- Gestion services
- Gestion commandes:
  - création multi-items
  - modification (ajout/suppression d'items)
  - soft delete
  - livraison (adresse + frais)
  - paiement d'avance (montant + moyen de paiement)
- Page **Mon abonnement** avec souscription à un pack
- Paramétrage pressing:
  - infos pressing
  - choix du modèle facture via previews (classic/modern/minimal)
  - couleur principale
  - message de bienvenue
- Gestion factures (liste + détail)
- Gestion des dépenses (ajout, édition, suppression soft delete, suivi par agence)
- Page Statistiques enrichie avec graphiques CA semaine, CA 4 derniers mois, CA vs dépenses, répartition des commandes
- Gestion des demandes employé (signalements) avec notifications et statut Lu
- Cloche notifications enrichie (marquer lu / vider toutes)
- Landing page de présentation
- Côté employé: dashboard CA jour/plage + graphes 7 jours + accès factures

### Employé
- Créer des commandes
- Marquer prête / retirée
- Livraison + paiement d'avance

### Commun
- Profil (modifier infos + mot de passe)
- Cloche notifications dans le header
- Toast JS Bootstrap après actions
- Tableaux en DataTables

## Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Comptes seedés
- Admin: `admin@pressing.local` / `password`
- Owner: `owner@pressing.local` / `password`
- Employee: `employee@pressing.local` / `password`

## Routes UI principales
- Dashboard: `/dashboard`
- Profil: `/profile`
- Owner: `/owner/ui/*`
- Employee: `/employee/ui/*`
- Admin: `/admin/ui/*`

## Dépannage
```bash
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
```
