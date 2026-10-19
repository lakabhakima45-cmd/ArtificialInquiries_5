# Diagramme de classes — Ex5 Subtracting the Machine

Chaque classe correspond à une classe du vocabulaire `artinq` (fichier `artificial_inquiries.ttl`) et à un modèle de ressource Omeka S (dossier `resource-template/`).

```mermaid
classDiagram
    class Participant {
        +title
        +compteGithub
    }

    class LLM {
        +title
        +version
    }

    class Conversation {
        +title
        +date
    }

    class Interaction {
        +title
        +numero
        +extraitUser
        +extraitLLM
    }

    class Substitution {
        +title
        +tour
        +evaluation
        +aRaffiner
        +estFinal
    }

    class Terme {
        +title
        +typeTerme
    }

    class EvaluationTerme {
        +title
        +adequat
        +etrange
        +correspondTon
        +justification
    }

    class Reflexion {
        +title
        +description
    }

    Conversation "*" --> "1" Participant : aParticipant
    Conversation "*" --> "1" LLM : avecLLM
    Interaction "1..*" --> "1" Conversation : dansConversation
    Substitution "1..3" --> "1" Interaction : pourInteraction
    Substitution "*" --> "1" Terme : roleUser
    Substitution "*" --> "1" Terme : roleLLM
    EvaluationTerme "*" --> "1" Terme : evalueTerme
    Reflexion "*" --> "1" Participant : aParticipant
    Reflexion "1" --> "*" EvaluationTerme : aEvaluation
```

Préfixes : `title`, `date` et `description` viennent de **Dublin Core** (`dcterms:`) ; toutes les autres propriétés et les liens viennent du vocabulaire **artinq** (`artinq:`).

## Explication

| Classe | Rôle dans l'exercice |
|---|---|
| **Participant** | La personne qui fait l'exercice. |
| **LLM** | Le modèle utilisé (ChatGPT, Claude…). |
| **Conversation** | Une conversation passée avec un LLM (4 par participant). |
| **Interaction** | Un échange précis : bulles « User » et « LLM » (Ex5a). |
| **Substitution** | Un tour de soustraction (1 à 3) : un terme pour l'utilisateur, un terme pour le LLM. |
| **Terme** | Un mot de remplacement (« un traiteur », « Wikipedia »…) ; les termes finaux forment le lexique. |
| **EvaluationTerme** | Les 3 questions de l'Ex5b sur un terme final : adéquat ? étrange ? correspond au ton ? |
| **Reflexion** | Le texte de réflexion de l'Ex5b. |

Les liens entre classes sont des propriétés `artinq` de type « Item » dans Omeka S : on les crée de l'enfant vers le parent (une Interaction pointe vers sa Conversation, etc.).

## Utilisation de l'IA

Ce diagramme a été réalisé avec l'aide de l'IA **Claude** (Anthropic, modèle Claude Opus 5.5). Les classes ont été vérifiées et testées dans mon Omeka S local.
