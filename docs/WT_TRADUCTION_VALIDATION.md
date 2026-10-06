# WT Traduction - validation locale

Date: 2026-10-06. Instance: http://127.0.0.1:8090/admin-whiskytime/.
Conteneur: 0b345592c707_whiskytime-upgrade-prestashop.
PrestaShop 9.0.3, theme wino_child. Base historique: 202 produits avant fixtures,
derniere mise a jour produit 2026-06-09. Pas une copie actuelle de production.

## Controles effectues

- Installation et reinstallation par la commande native prestashop:module.
- Lint PHP sur module et controleur: OK.
- Connexion HTTP authentifiee BO, entree menu et page: OK.
- Submit BO configuration avec cle vide conserve la cle existante: OK.
- Liste non vide avec fiche de test et lien PS9 productId: OK.
- Appel AJAX BO reel traduit description et recapitulatif, puis liste vide: OK.
- Appel reel DeepL avec cle issue du handoff: OK; cle jamais versionnee.
- Tests PHP: NULL / espaces HTML, exclusion reference hors format, conservation
  HTML, anglais existant preserve, relance idempotente, erreur API simulee,
  succes precedents conserves, reprise du seul champ manquant: OK.
- Asset JS servi en HTTP 200; copie dans le chemin reellement servi: OK.
- Tests d'ecriture www-data: var/cache/dev/smarty/compile, var/cache/dev/admin,
  var/logs et var/sessions: OK.

## Test utilisateur attendu

Un produit inactif de demonstration est conserve uniquement sur 8090:
ID 285, reference 99-99998, nom WT TEST Traduction (inactif).
Ses champs EN ont ete remis a vide apres les tests pour permettre un clic reel
sur le bouton global. Les autres fixtures ont ete supprimees.

Ouvrir Whisky Time > WT Traduction, verifier la presentation, cliquer Traduire,
puis verifier les deux textes anglais dans la fiche produit. La liste doit etre
vide apres traitement. Supprimer le produit de demonstration apres validation.

Le bouton dans un navigateur et le rendu visuel restent a valider par l'utilisateur;
les controles HTTP ne remplacent pas cette validation.
Le dossier updates sera prepare apres cette validation.

## Inspect and Adapt

Toujours tester une liste BO non vide: elle exerce les liens vers les fiches
produit et les variables de template que la liste vide ne couvre pas.
