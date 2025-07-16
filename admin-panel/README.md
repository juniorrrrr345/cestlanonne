# Panel Administrateur AVEC AMOUR

Un panel administrateur moderne et fonctionnel pour la gestion de votre boutique en ligne.

## 🚀 Fonctionnalités

### ✅ Gestion des Produits
- **Ajout/Modification/Suppression** de produits
- **Catégories multiples** avec sélection dans un menu déroulant
- **Prix multiples** par produit (normal, soldé, gros, promotionnel)
- **Support vidéo et images** pour les médias
- **Recherche avancée** dans les produits
- **Stock et poids** configurables

### ✅ Gestion des Catégories
- **Création de catégories** avec description
- **Hiérarchie** (catégories parentes/enfants)
- **Ordre d'affichage** personnalisable
- **Statistiques** par catégorie

### ✅ Interface Moderne
- **Design responsive** adapté mobile/desktop
- **Animations fluides** et transitions
- **Notifications** en temps réel
- **Interface intuitive** avec icônes Remix Icons

### ✅ Sécurité
- **Authentification** sécurisée
- **Session persistante** avec localStorage
- **Validation des données** côté client
- **Headers de sécurité** configurés

## 🛠️ Installation

### Prérequis
- Node.js >= 18.0.0
- Serveur web (Apache, Nginx, ou serveur Node.js)

### Installation locale
```bash
# Cloner le projet
git clone [votre-repo]

# Installer les dépendances
npm install

# Démarrer le serveur de développement
npm run dev
```

### Déploiement

#### Vercel
```bash
# Installer Vercel CLI
npm i -g vercel

# Déployer
vercel
```

#### Render
1. Connectez votre repository GitHub
2. Créez un nouveau service Web
3. Le fichier `render.yaml` est déjà configuré

#### Netlify
1. Connectez votre repository
2. Déployez automatiquement

## 📁 Structure des fichiers

```
admin-panel/
├── index.html          # Page principale
├── admin.css           # Styles CSS
├── admin.js            # Logique JavaScript
├── .htaccess           # Configuration Apache
├── package.json        # Configuration Node.js
├── vercel.json         # Configuration Vercel
├── render.yaml         # Configuration Render
├── server.js           # Serveur Express
└── README.md           # Documentation
```

## 🔐 Authentification

**Identifiants par défaut :**
- **Utilisateur :** `admin`
- **Mot de passe :** `admin123`

⚠️ **Important :** Changez ces identifiants en production !

## 🎨 Personnalisation

### Couleurs
Modifiez les variables CSS dans `admin.css` :
```css
:root {
  --primary-color: #009CFF;
  --secondary-color: #4DC7A0;
  /* ... autres couleurs */
}
```

### Logo et branding
1. Remplacez le texte "AVEC AMOUR" dans `index.html`
2. Modifiez l'image de fond dans `admin.css`
3. Personnalisez les icônes avec Remix Icons

## 📊 Fonctionnalités avancées

### Prix multiples
Chaque produit peut avoir plusieurs prix :
- **Prix normal** : Prix standard
- **Prix soldé** : Prix en promotion
- **Prix gros** : Prix pour les grossistes
- **Prix promotionnel** : Prix temporaire

### Support vidéo
- Formats supportés : MP4, WebM, OGG
- Lecture automatique en boucle
- Prévisualisation dans les modals

### Catégories hiérarchiques
- Création de sous-catégories
- Ordre d'affichage personnalisable
- Statistiques par catégorie

## 🔧 Configuration

### Variables d'environnement
```bash
# Port du serveur (optionnel)
PORT=3000

# URL de l'API (à configurer)
API_BASE_URL=./api
```

### Base de données
Le panel utilise actuellement des données simulées. Pour connecter une vraie base de données :

1. Créez un dossier `api/`
2. Ajoutez vos endpoints PHP/Node.js
3. Modifiez `admin.js` pour utiliser les vraies APIs

## 🚀 Déploiement rapide

### Vercel (Recommandé)
```bash
npm i -g vercel
vercel --prod
```

### Render
1. Connectez votre GitHub
2. Créez un nouveau service Web
3. Déployez automatiquement

### Netlify
1. Glissez-déposez le dossier
2. Ou connectez votre GitHub

## 📱 Responsive Design

Le panel s'adapte automatiquement :
- **Desktop** : Interface complète
- **Tablet** : Navigation optimisée
- **Mobile** : Interface tactile

## 🔒 Sécurité

### Headers configurés
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`

### Authentification
- Session persistante
- Déconnexion automatique
- Validation des données

## 🐛 Dépannage

### Problèmes courants

**1. Page blanche**
- Vérifiez que tous les fichiers sont présents
- Consultez la console du navigateur

**2. Styles non chargés**
- Vérifiez le chemin vers `admin.css`
- Vérifiez les permissions des fichiers

**3. JavaScript non fonctionnel**
- Vérifiez le chemin vers `admin.js`
- Consultez la console pour les erreurs

**4. Images non affichées**
- Vérifiez le chemin vers `hModuTC.jpeg`
- Ajoutez vos propres images

## 📈 Évolutions futures

- [ ] Intégration base de données réelle
- [ ] Système de commandes
- [ ] Analytics avancées
- [ ] Gestion des utilisateurs
- [ ] Export/Import de données
- [ ] Notifications push
- [ ] Mode sombre

## 🤝 Support

Pour toute question ou problème :
1. Consultez la console du navigateur
2. Vérifiez les logs du serveur
3. Testez en mode local d'abord

## 📄 Licence

MIT License - Libre d'utilisation et modification

---

**AVEC AMOUR** - Panel Administrateur v2.0
*Développé avec ❤️ pour votre boutique en ligne*