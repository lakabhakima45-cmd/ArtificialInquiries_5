# Diagramme de classes — Ex5 Subtracting the Machine

```mermaid
classDiagram
    class Exercice {
        +String code
        +String titre
        +String consigne
    }

    class Participant {
        +String nom
        +String compteGithub
    }

    class LLM {
        +String nom
        +String version
    }

    class Conversation {
        +String titre
        +Date date
    }

    class Interaction {
        +int numero
        +String extraitUser
        +String extraitLLM
    }

    class Substitution {
        +int tour
        +String evaluation
        +boolean aRaffiner
    }

    class Terme {
        +String libelle
        +TypeTerme type
    }

    class TypeTerme {
        <<enumeration>>
        TITRE_PROFESSIONNEL
        SERVICE_EN_LIGNE
        ENTREPRISE
        ENTITE_INDEFINIE
        PERSONNE_PRIVEE
    }

    class Reflexion {
        +String texte
        +Date date
    }

    Exercice "1" --> "*" Participant : réalisé par
    Participant "1" --> "4" Conversation : choisit
    Conversation "*" --> "1" LLM : avec
    Conversation "1" *-- "1..*" Interaction : contient
    Interaction "1" *-- "1..3" Substitution : tours
    Substitution "*" --> "1" Terme : roleUser
    Substitution "*" --> "1" Terme : roleLLM
    Terme --> TypeTerme
    Participant "1" --> "1" Reflexion : rédige (Ex5b)
    Reflexion "*" --> "*" Terme : commente
```

## Explication

- **Participant** : la personne qui fait l'exercice. Elle choisit **4 conversations**.
- **Conversation** : une conversation passée avec un **LLM**, identifiée par son titre.
- **Interaction** : un échange précis (message + réponse), recopié dans les bulles « User » et « LLM ».
- **Substitution** : un tour de soustraction (1 à 3), qui associe un **terme** au rôle de l'utilisateur et un autre au rôle de la machine.
- **Terme** : un mot de remplacement (« un traiteur », « Wikipedia », « un fantôme »), stocké une seule fois pour construire le **lexique**.
- **Reflexion** : le texte de l'Ex5b sur les termes finaux.
