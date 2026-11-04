# ShopFlow

## Assets : Tailwind CSS et Webpack Encore

Installation conforme au guide Symfony de Tailwind CSS :
https://tailwindcss.com/docs/installation/framework-guides/symfony

Les assets sont compilés dans un service Node séparé du service PHP.
Installer les dépendances PHP avec `docker compose exec app composer install`
avant le premier démarrage du service Node (Symfony UX utilise des paquets locaux dans `vendor/`).

```bash
docker compose up -d node
docker compose logs -f node
```

Ce service exécute `npm ci --include=dev`, puis `npm run watch`.
Tailwind, PostCSS, Encore et les dépendances JavaScript sont installés dans Docker ;
Node et npm ne sont pas nécessaires sur la machine hôte. Il recompile automatiquement
les modifications dans `assets/` et les classes Tailwind des templates Twig.
Le polling permet de détecter les changements sur les volumes Docker Desktop.
Le dossier `public/` est exclu des sources Tailwind pour éviter les boucles de compilation.

Le conteneur Node utilise par défaut l’utilisateur 1000:1000 pour garder les fichiers
accessibles sur la machine hôte. Si nécessaire sous Linux :

```bash
LOCAL_UID=$(id -u) LOCAL_GID=$(id -g) docker compose up -d node
```

Pour produire les assets optimisés (arrêter le watcher pour éviter les écritures concurrentes) :

```bash
docker compose stop node
docker compose run --rm --no-deps node sh -c "npm ci --include=dev --no-audit --no-fund && npm run build"
docker compose up -d node
```

Les styles personnalisés restent dans `assets/styles/app.css`, après l’import Tailwind.
Le catalogue charge les entrées `app` avec les fonctions Twig `encore_entry_link_tags`
et `encore_entry_script_tags`. Les fichiers compilés sont générés dans `public/build/`.

Pour ajouter ou mettre à jour un paquet npm depuis Docker :

```bash
docker compose stop node
docker compose run --rm --no-deps node npm install --save-dev nom-du-paquet
docker compose up -d node
```

Cette commande met aussi à jour `package.json` et `package-lock.json` dans le projet.
Arrêter le watcher avant l’installation évite de modifier `node_modules` pendant une compilation.

## API JSON sans base de données

L’API est disponible sur `http://localhost:8000`, sans préfixe `/api` :

| Méthode | Route | Résultat |
| --- | --- | --- |
| GET | `/produits` | Tous les produits (`id`, `name`, `price`, `stock`) |
| GET | `/produit/1` | Un produit, ou une erreur JSON 404 |
| POST | `/commande` | Une commande enregistrée, réponse JSON 201 |

```bash
curl http://localhost:8000/produits
curl http://localhost:8000/produit/1
curl -X POST http://localhost:8000/commande \
  -H 'Content-Type: application/json' \
  -d '{"produit_id":1,"quantite":2}'
```

Le POST accepte une seule ligne produit, conformément au contrat fourni.
Les deux champs doivent être des entiers positifs. JSON invalide, champs manquants,
produit inconnu ou stock insuffisant renvoient une erreur JSON 400 sans modifier les données.
Le prix et le total sont calculés côté serveur. Aucun paiement n’est déclenché.

Les produits initiaux se trouvent dans `data/produits.json`.
À la première commande, `var/shopflow.json` est créé : il contient les produits avec leur
stock courant et la liste des commandes. Le catalogue et l’API lisent ce même stockage.
Les écritures sont verrouillées et le fichier est remplacé atomiquement pour sauvegarder
la commande et la diminution de stock ensemble. Le fichier n’est pas exposé dans `public/`.
Le volume Docker `.:/app` conserve les données après un redémarrage des conteneurs.

Pour repartir des produits initiaux, arrêter l’application puis supprimer uniquement
`var/shopflow.json` (cela efface les commandes et réinitialise les stocks).
Les changements du fichier initial prennent effet tant que `var/shopflow.json` n’existe pas.

Le service `app` peut démarrer sans PostgreSQL :

```bash
docker compose up -d app node
```

Les services de base de données et Messenger existants restent disponibles pour les autres
expérimentations du projet. L’API JSON ne les utilise pas. Les prototypes de repository/paiement
inachevés sont exclus de la découverte des services Symfony.

Tests (stockage temporaire isolé, sans modifier les commandes locales) :

```bash
docker compose exec app php vendor/bin/phpunit
```
