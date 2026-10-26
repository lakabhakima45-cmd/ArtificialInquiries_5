# ArtificialInquiries_5 — Subtracting the Machine

Exercice 5 du protocole **Artificial Inquiries** (Vademecum, septembre 2025, bloc *Qualifying*, p. 31-36), transformé en outil numérique dans le cadre du cours *Langages du Web* (Master THYP, Université Paris 8).

Auteure : Hakima Lakab (`lakabhakima45-cmd`)

🔗 **Application en ligne :** https://lakabhakima45-cmd.github.io/ArtificialInquiries_5/response.html

## 1. Description de l'exercice

Quand on parle de nos échanges avec un LLM, on dit spontanément « j'ai demandé à Claude », « l'IA m'a répondu ». L'exercice pose la question : **que reste-t-il si on retire les mots « machine », « IA », « LLM », « Claude » ou même « il » de la description ?**

### Ex5a — A Thought Experiment
1. Choisir **4 interactions** (un message de l'utilisateur + la réponse de la machine) et noter le **titre de la conversation**.
2. Recopier le **début de l'interaction** dans deux bulles : « User » et « LLM ».
3. **Remplacer** les termes habituels par d'autres rôles : l'utilisateur devient « un manager », « un client exigeant »… la machine devient « un conseiller », « Wikipedia », « McKinsey », « un fantôme »…
4. **Évaluer** si ces nouveaux termes décrivent bien l'échange. S'ils sont trop génériques ou répétés, faire **un nouveau tour de soustraction** (jusqu'à 3 tours).

### Ex5b — What Remains After Subtraction
Réflexion écrite sur le **lexique final** :
- Les termes décrivent-ils correctement qui écrit/parle ?
- Certains paraissent-ils étranges (*uncanny*) ? Pourquoi ?
- Le nouveau rôle correspond-il au ton de la réponse ?

## 2. Objectif de l'outil numérique

Remplacer les pages papier par une application web qui permet de :
- saisir les 4 conversations et leurs extraits (bulles User / LLM) ;
- enchaîner les **tours de substitution** pour chaque interaction ;
- conserver l'**historique** des termes essayés (le « lexique ») ;
- rédiger la **réflexion finale** (Ex5b) ;
- **comparer** les lexiques de plusieurs participants.

## 3. Solutions techniques

| Besoin | Solution |
|---|---|
| Stockage des données | **Omeka S** en local (Apache + PHP + MySQL avec XAMPP) |
| Vocabulaire | Vocabulaire **artinq** (`artificial_inquiries.ttl`) : 8 classes et 22 propriétés, espace de noms `https://humanum-p8.fr/omk_artificialinquiries/onto/artinq#` |
| Modèle de données | Diagramme de classes (`diagram_class.md`) traduit en **8 modèles de ressources** Omeka S (`resource-template/`) |
| Communication avec Omeka S | **API REST** d'Omeka S (https://omeka.org/s/docs/developer/api/) |
| Interface CRUD | `response.html` en **HTML / CSS / JavaScript**, publiée avec **GitHub Pages** |
| Authentification | Local (clé API Omeka S), GitHub (jeton d'accès), Google (Google Identity Services) |
| Diagrammes | **Mermaid** |
| Versionnement | **Git / GitHub** |

## 4. Étapes de réalisation

1. ✅ Installer Omeka S en local et vérifier l'accès à l'API.
2. ✅ Tester la bibliothèque omk : lire et créer un item depuis une page HTML.
3. ✅ Créer le vocabulaire **artinq** et l'importer dans Omeka S.
4. ✅ Créer les modèles de ressources correspondant aux classes du diagramme.
5. ✅ Créer des items de test (exemple du vademecum « Birthday celebration dish ideas ») et exporter la base (`omk.sql`).
6. ✅ Créer l'interface CRUD `response.html` et l'activer avec GitHub Pages.
7. ✅ Gérer l'authentification : local, GitHub, Google.
8. ⏳ Authentification CAS.
9. ⏳ Visualisation du lexique.

## 5. Fichiers du dépôt

| Fichier / dossier | Contenu |
|---|---|
| `README.md` | Ce document |
| `response.html` | Interface CRUD de l'exercice (GitHub Pages) |
| `diagram_class.md` | Diagramme de classes Mermaid (aligné sur le vocabulaire artinq) |
| `artificial_inquiries.ttl` | Vocabulaire **artinq** (8 classes, 22 propriétés) |
| `resource-template/` | Les 8 modèles de ressources Omeka S (un fichier JSON par classe) |
| `test-items/` | Page HTML qui crée les items de test dans Omeka S avec la bibliothèque **omk** |
| `omk.sql` | Export de la base de données Omeka S locale |

## 6. Interface CRUD (`response.html`)

**Fonctionnement :**
- 8 onglets, un par classe du vocabulaire (Participants, LLM, Conversations, Interactions, Substitutions, Termes, Évaluations, Réflexions).
- Pour chaque classe : **lister**, **afficher**, **créer**, **modifier** et **supprimer** des items (CRUD) via l'API REST d'Omeka S.
- Les formulaires sont générés automatiquement à partir des **modèles de ressources** `artinq`.
- Les liens entre items (ex. une Interaction → sa Conversation) se choisissent dans des listes, et la fiche d'un item affiche les items qui pointent vers lui.

**Authentification :**

| Méthode | Droits | Principe |
|---|---|---|
| Local (Omeka S) | Lecture + écriture | Email + clé API Omeka S (`key_identity`, `key_credential`) |
| GitHub | Lecture | Jeton d'accès personnel GitHub ; le compte est relié au Participant dont `artinq:compteGithub` correspond |
| Google | Lecture | Google Identity Services (client OAuth créé dans Google Cloud) |
| CAS Paris 8 | — | Non configuré : nécessite l'enregistrement du service auprès de l'université et un serveur de validation |

**Utilisation :**
1. Démarrer XAMPP (Apache + MySQL) : l'Omeka S est installé en local.
2. Ouvrir la page en ligne et indiquer l'adresse de l'API (par défaut `http://localhost/omeka-s/api/`).
3. Se connecter avec une des méthodes.

**Configuration CORS :** pour que la page publiée sur GitHub Pages puisse appeler l'API locale, ces lignes ont été ajoutées au début du fichier `.htaccess` d'Omeka S :

```apache
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, PATCH, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type"
</IfModule>
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]
```

## 7. Utilisation de l'IA

Ce travail a été réalisé avec l'aide de l'IA générative **Claude** (Anthropic, modèle Claude Opus 5.5), utilisée pour m'accompagner dans l'installation, la rédaction et la préparation des fichiers. J'ai moi-même installé et configuré Omeka S, importé le vocabulaire et les modèles, créé et vérifié les items de test, configuré GitHub Pages et l'authentification Google, testé l'interface, puis mis le travail en ligne.
