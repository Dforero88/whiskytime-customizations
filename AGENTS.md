# Workflow

Pour toute modification applicative sur ce projet, suivre ce workflow par defaut.

## Source de verite

- Le travail versionne se fait dans `repo-github/`.
- Le dossier racine sert de copie locale Prestashop/Docker pour les tests.

## Procedure standard

1. Faire la modification dans `repo-github/` uniquement sur les fichiers necessaires.
2. Resynchroniser les fichiers modifies dans l'instance locale si un test local est necessaire.
3. Verifier rapidement le comportement local et vider le cache Prestashop si besoin.
4. Faire un commit cible dans `repo-github/` sans embarquer les changements hors scope.
5. Pousser sur GitHub.
6. Creer un dossier `updates/<nom-du-patch-date>/`.
7. Mettre dans ce dossier uniquement les fichiers a deployer, avec leur arborescence de production.
8. Ajouter un `README.md` dans `updates/...` avec :
   - les modifications incluses
   - les fichiers a deployer
   - leur emplacement en production
   - la procedure de deploiement

## Regles de deploiement

- Ne pas re-uploader tout le module si seuls quelques fichiers changent.
- Ne pas deployer toute l'instance.
- Preferer des remplacements cibles de fichiers.
- Vider le cache Prestashop apres deploiement.
- Si une modif est purement visuelle sur un module natif, preferer une surcharge theme ou du CSS theme plutot qu'une modif directe du module.
