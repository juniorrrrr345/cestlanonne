# Panel Admin - Gestion de Boutique

Ce panel admin permet de gérer les produits de votre boutique déployée sur Netlify.

## Fonctionnalités

### 📱 Capture Vidéo/Photo
- **Capture vidéo** : Enregistrez des vidéos directement depuis votre téléphone
- **Capture photo** : Prenez des photos avec la caméra de votre appareil
- **Upload de fichiers** : Téléchargez des fichiers depuis votre appareil

### 🛍️ Gestion des Produits
- Ajouter de nouveaux produits
- Modifier les produits existants
- Supprimer des produits
- Recherche et pagination
- Gestion des médias (photos/vidéos)

### 💰 Gestion des Prix
- Prix multiples par produit
- Devises différentes
- Gestion des promotions

## Configuration

### Base de données
Assurez-vous que votre base de données MySQL contient la table `products` :

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    media VARCHAR(500),
    description TEXT,
    category VARCHAR(100),
    weight VARCHAR(50),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Configuration
Modifiez le fichier `config.php` selon vos paramètres :
- URL de votre boutique Netlify
- Paramètres de base de données
- Dossier d'upload

## Utilisation

### 1. Connexion
- Accédez au panel admin
- Connectez-vous avec vos identifiants

### 2. Ajouter un Produit
1. Cliquez sur "Add Product"
2. Remplissez les informations du produit
3. Pour ajouter une photo/vidéo :
   - **Capture vidéo** : Cliquez sur "Capturer Vidéo" et autorisez l'accès à la caméra
   - **Capture photo** : Cliquez sur "Capturer Photo" et prenez une photo
   - **Upload fichier** : Cliquez sur "Choisir Fichier" pour sélectionner un fichier
4. Cliquez sur "Save"

### 3. Gérer les Produits
- **Modifier** : Cliquez sur "Edit" pour modifier un produit
- **Supprimer** : Cliquez sur "Delete" pour supprimer un produit
- **Rechercher** : Utilisez la barre de recherche pour trouver des produits

## Sécurité

- Session admin avec timeout
- Validation des fichiers uploadés
- Nettoyage des données
- Protection contre les injections SQL

## Déploiement

### Panel Admin
Le panel admin doit être hébergé sur un serveur PHP avec MySQL.

### Boutique
La boutique est déployée sur Netlify et se connecte à la même base de données.

## Support

Pour toute question ou problème, consultez la documentation ou contactez l'équipe de support.