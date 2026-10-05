# ArtificialInquiries_5 — Subtracting the Machine

Exercice 5 du protocole **Artificial Inquiries** (Vademecum, septembre 2025, bloc *Qualifying*, p. 31-36), transformé en outil numérique dans le cadre du cours *Langages du Web* (Master THYP, Université Paris 8).

Auteure : Hakima Lakab (`lakabhakima45-cmd`)

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
| Stockage des données | **Omeka S** en local (Apache + PHP + MySQL) |
| Modèle de données | Diagramme de classes (`diagram_class.md`) traduit en **modèles de ressources** Omeka S (Dublin Core, FOAF, schema.org) |
| Communication avec Omeka S | **API REST** d'Omeka S via la bibliothèque JavaScript **omk** |
| Interface de saisie | **HTML / CSS / JavaScript** |
| Visualisation du lexique | **D3.js** |
| Diagrammes | **Mermaid** |
| Versionnement | **Git / GitHub** |

## 4. Étapes de réalisation

1. Installer Omeka S en local et vérifier l'accès à l'API.
2. Créer les modèles de ressources correspondant aux classes du diagramme.
3. Tester la bibliothèque omk : lire et créer un item depuis une page HTML.
4. Développer le formulaire de saisie Ex5a.
5. Développer la page de réflexion Ex5b.
6. Ajouter la visualisation du lexique.

## 5. Fichiers du dépôt

- `README.md` : ce document
- `diagram_class.md` : diagramme de classes Mermaid
