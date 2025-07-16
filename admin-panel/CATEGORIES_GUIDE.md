# Guide d'utilisation des Catégories

## Vue d'ensemble

Le système de catégories permet d'organiser vos produits de manière hiérarchique. Vous pouvez créer des catégories principales et des sous-catégories pour une meilleure organisation de votre boutique.

## Fonctionnalités

### 📁 Structure hiérarchique
- **Catégories principales** : Catégories de premier niveau
- **Sous-catégories** : Catégories enfants d'une catégorie principale
- **Profondeur illimitée** : Vous pouvez créer autant de niveaux que nécessaire

### 🎯 Gestion des catégories
- **Ajouter** : Créer de nouvelles catégories
- **Modifier** : Modifier les informations d'une catégorie
- **Supprimer** : Supprimer une catégorie (si elle ne contient pas de produits)
- **Activer/Désactiver** : Contrôler la visibilité des catégories

### 📊 Statistiques
- **Compteur de produits** : Voir combien de produits sont dans chaque catégorie
- **Ordre d'affichage** : Contrôler l'ordre d'affichage des catégories

## Utilisation

### 1. Accéder à la gestion des catégories
1. Connectez-vous au panel admin
2. Cliquez sur "Catégories" dans le menu de gauche
3. Vous verrez la liste de toutes vos catégories

### 2. Ajouter une catégorie
1. Cliquez sur "Ajouter Catégorie"
2. Remplissez les informations :
   - **Nom** : Nom de la catégorie (obligatoire)
   - **Description** : Description de la catégorie (optionnel)
   - **Catégorie parente** : Sélectionnez une catégorie parente si c'est une sous-catégorie
   - **Ordre d'affichage** : Numéro pour contrôler l'ordre (0 = premier)
   - **Catégorie active** : Cochez pour rendre la catégorie visible
3. Cliquez sur "Enregistrer"

### 3. Modifier une catégorie
1. Cliquez sur l'icône "Modifier" (crayon) à côté de la catégorie
2. Modifiez les informations souhaitées
3. Cliquez sur "Enregistrer"

### 4. Supprimer une catégorie
1. Cliquez sur l'icône "Supprimer" (poubelle) à côté de la catégorie
2. Confirmez la suppression

**⚠️ Attention** : Une catégorie ne peut être supprimée que si :
- Elle ne contient aucun produit
- Elle n'a pas de sous-catégories

### 5. Assigner une catégorie à un produit
1. Allez dans la gestion des produits
2. Ajoutez ou modifiez un produit
3. Dans le champ "Catégorie", sélectionnez la catégorie appropriée
4. Sauvegardez le produit

## Bonnes pratiques

### 📋 Organisation recommandée
```
Électronique
├── Smartphones
│   ├── iPhone
│   └── Android
├── Ordinateurs
│   ├── Laptops
│   └── Desktop
└── Accessoires
    ├── Câbles
    └── Coques
```

### 🎯 Conseils
1. **Nommage clair** : Utilisez des noms descriptifs et courts
2. **Hiérarchie logique** : Organisez de manière logique pour vos clients
3. **Ordre d'affichage** : Utilisez les numéros pour contrôler l'ordre
4. **Descriptions** : Ajoutez des descriptions pour le SEO

### 🔧 Gestion technique
- **Catégories inactives** : Les catégories inactives ne s'affichent pas dans le formulaire de produits
- **Sous-catégories** : Une catégorie peut avoir plusieurs sous-catégories
- **Produits multiples** : Un produit peut être dans une seule catégorie

## Intégration avec la boutique

Les catégories créées dans le panel admin sont automatiquement disponibles dans :
- Le formulaire d'ajout/modification de produits
- La boutique en ligne (si configurée)
- Les filtres de recherche

## Dépannage

### Problème : Impossible de supprimer une catégorie
**Solution** : Vérifiez que la catégorie ne contient aucun produit et aucune sous-catégorie

### Problème : Catégorie non visible dans le formulaire de produits
**Solution** : Vérifiez que la catégorie est marquée comme "active"

### Problème : Erreur lors de la sauvegarde
**Solution** : Vérifiez que le nom de la catégorie n'existe pas déjà

## Support

Pour toute question ou problème, consultez la documentation ou contactez l'équipe de support.