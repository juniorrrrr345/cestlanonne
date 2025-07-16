# Panel Admin - Ma Boutique

## 🛍️ Vue d'ensemble

Ce panel admin a été spécialement conçu pour gérer votre boutique e-commerce. Il remplace l'ancien panel générique par une interface moderne et adaptée aux besoins d'une boutique en ligne.

## ✨ Nouvelles fonctionnalités

### 📊 Dashboard intelligent
- **Statistiques en temps réel** : Nombre de produits, commandes, clients et chiffre d'affaires
- **Activité récente** : Suivi des dernières actions (nouveaux produits, commandes, clients)
- **Actions rapides** : Accès direct aux fonctions principales
- **Mise à jour automatique** : Les statistiques se rafraîchissent toutes les 30 secondes

### 🎨 Interface moderne
- **Design adapté e-commerce** : Couleurs et icônes spécifiques à la vente en ligne
- **Navigation intuitive** : Menu latéral avec icônes claires
- **Responsive design** : Compatible mobile et tablette
- **Animations fluides** : Transitions et effets visuels modernes

### 📦 Gestion des produits améliorée
- **Vue d'ensemble** : Tableau avec aperçu des images et informations essentielles
- **Recherche intelligente** : Filtrage en temps réel par nom, description ou catégorie
- **Pagination optimisée** : Navigation facile entre les pages
- **Actions groupées** : Boutons d'édition et suppression côte à côte

### 📸 Capture de média avancée
- **Capture photo/vidéo** : Utilisation de la caméra directement depuis le navigateur
- **Upload de fichiers** : Support des formats image et vidéo
- **Prévisualisation** : Aperçu immédiat des médias sélectionnés
- **Gestion des erreurs** : Messages d'erreur clairs en cas de problème

### 🔧 Fonctionnalités techniques
- **API RESTful** : Communication moderne avec le serveur
- **Gestion d'état** : État local pour une meilleure performance
- **Validation côté client** : Vérification des données avant envoi
- **Gestion des erreurs** : Messages d'erreur informatifs

## 🚀 Installation et configuration

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)

### Installation
1. **Cloner le projet** dans votre répertoire web
2. **Configurer la base de données** :
   ```sql
   -- Exécuter le script database_setup.sql
   source database_setup.sql
   ```
3. **Configurer la connexion** dans `config.php`
4. **Créer un utilisateur admin** :
   ```sql
   INSERT INTO admin_users (username, password, email, role) 
   VALUES ('admin', '$2y$10$...', 'admin@votreboutique.com', 'admin');
   ```

### Configuration
Modifiez le fichier `config.php` avec vos paramètres :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_base');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

## 📱 Utilisation

### Connexion
1. Accédez à `signin.html`
2. Utilisez les identifiants admin créés
3. Vous serez redirigé vers le dashboard

### Gestion des produits
1. **Ajouter un produit** : Cliquez sur "Ajouter un produit"
2. **Remplir le formulaire** : Nom, prix, description, catégorie
3. **Ajouter des médias** : Photo/vidéo via caméra ou upload
4. **Sauvegarder** : Le produit apparaît dans le tableau

### Navigation
- **Dashboard** : Vue d'ensemble et statistiques
- **Catégories** : Gestion des catégories de produits
- **Commandes** : Gestion des commandes (à venir)
- **Clients** : Gestion des clients (à venir)
- **Analytics** : Statistiques détaillées (à venir)
- **Paramètres** : Configuration de la boutique (à venir)

## 🎯 Fonctionnalités à venir

### Phase 2 - Gestion des commandes
- [ ] Interface de gestion des commandes
- [ ] Statuts de commande (en attente, expédiée, livrée)
- [ ] Notifications automatiques
- [ ] Génération de factures

### Phase 3 - Gestion des clients
- [ ] Base de données clients
- [ ] Historique des achats
- [ ] Système de fidélité
- [ ] Communication client

### Phase 4 - Analytics avancées
- [ ] Graphiques de vente
- [ ] Analyse des produits populaires
- [ ] Rapports de performance
- [ ] Export de données

### Phase 5 - Paramètres boutique
- [ ] Configuration générale
- [ ] Gestion des paiements
- [ ] Paramètres de livraison
- [ ] Personnalisation de l'interface

## 🔒 Sécurité

### Authentification
- Sessions sécurisées
- Protection contre les injections SQL
- Validation des données côté serveur
- Gestion des permissions utilisateur

### Protection des données
- Chiffrement des mots de passe
- Validation des fichiers uploadés
- Protection CSRF
- Logs d'activité

## 🛠️ Maintenance

### Sauvegarde
```bash
# Sauvegarde de la base de données
mysqldump -u user -p database > backup.sql

# Sauvegarde des fichiers
tar -czf admin_backup.tar.gz admin-panel/
```

### Mise à jour
1. Sauvegarder les données actuelles
2. Remplacer les fichiers modifiés
3. Exécuter les scripts de migration si nécessaire
4. Tester les fonctionnalités

## 📞 Support

### Problèmes courants
1. **Erreur de connexion** : Vérifiez les paramètres de base de données
2. **Images non affichées** : Vérifiez les permissions du dossier uploads
3. **Capture caméra** : Assurez-vous que le site est en HTTPS

### Logs
Les erreurs sont enregistrées dans :
- `error.log` : Erreurs PHP
- `access.log` : Accès au panel
- Base de données : Table `activity_logs`

## 🎨 Personnalisation

### Couleurs
Modifiez les variables CSS dans `style.css` :
```css
:root {
    --primary-color: #votre_couleur;
    --secondary-color: #votre_couleur;
    /* ... */
}
```

### Logo et branding
- Remplacez les icônes FontAwesome
- Modifiez le titre "Ma Boutique"
- Personnalisez les couleurs du gradient

## 📈 Performance

### Optimisations
- Images compressées
- CSS et JS minifiés
- Requêtes SQL optimisées
- Cache des données fréquentes

### Monitoring
- Temps de chargement des pages
- Utilisation de la base de données
- Erreurs JavaScript
- Performance des requêtes

---

**Version** : 2.0  
**Dernière mise à jour** : Décembre 2024  
**Auteur** : Assistant IA  
**Licence** : Libre d'utilisation