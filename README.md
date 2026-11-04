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

Ce service exécute `npm ci`, puis `npm run watch`. Il recompile automatiquement
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
docker compose run --rm --no-deps node sh -c "npm ci --no-audit --no-fund && npm run build"
docker compose up -d node
```

Les styles personnalisés restent dans `assets/styles/app.css`, après l’import Tailwind.
Le catalogue charge les entrées `app` avec les fonctions Twig `encore_entry_link_tags`
et `encore_entry_script_tags`. Les fichiers compilés sont générés dans `public/build/`.
