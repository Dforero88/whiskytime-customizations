# WT Traduction

Specification validee le 6 octobre 2026.

## Objectif

Completer manuellement les traductions anglaises manquantes des descriptions et
recapitulatifs produits via DeepL. Les champs SEO utilisent les fallbacks natifs.

## Perimetre

- Page BO WT Traduction sous le menu Whisky Time existant.
- Pastille rouge sur l'entree menu indiquant le nombre de produits a traduire;
  aucune pastille a zero. Compteur charge en arriere-plan lors de la navigation,
  resultat conserve 60 secondes par employe/boutique dans le navigateur.
  La page du module reutilise son propre nombre sans appel supplementaire.
  La pastille est actualisee apres chaque produit traduit.
- Boutique courante uniquement; produits actifs et inactifs.
- Reference strictement composee de deux chiffres, un tiret et cinq chiffres.
- Description FR non vide et description EN vide, ou recapitulatif FR non vide
  et recapitulatif EN vide. NULL, espaces et HTML sans texte sont vides.
- Liste avec ID, reference, nom, champs a traduire et lien vers la fiche produit.
- Un bouton Traduire traite toute la liste par requetes successives.
- Conservation du HTML, source FR et cible EN-GB.
- Aucun ecrasement de texte EN existant; verification juste avant ecriture.
- Aucun traitement automatique, cron, traduction de nom ou modification SEO.
- Une modification FR apres traduction ne declenche pas de retraduction.

## Interaction

Le bouton est desactive pendant le traitement. La progression compte les produits
traites et les champs traduits. Les traductions sont enregistrees progressivement.
La liste est rechargee a la fin. Une erreur arrete le traitement et affiche un
bandeau rouge; les succes precedents restent acquis et une relance reprend les
champs manquants. Les erreurs ne doivent jamais exposer la cle API.

## DeepL

Cle configurable cote serveur dans le BO, jamais versionnee ni envoyee au navigateur.
API Free: https://api-free.deepl.com/v2/translate; API Pro: https://api.deepl.com/v2/translate.
Authentification par en-tete Authorization, requete POST, traitement HTML.
Les limites PrestaShop du recapitulatif sont verifiees avant enregistrement.

## Validation et livraison

Niveau D: installation module et ecriture de contenus produits.
Tests sur 8090 (PS 9.0.3): acces BO, configuration, liste, traduction nominale,
champ EN existant preserve, reference hors format exclue, champs vides,
erreur API, relance et permissions cache si regeneration.
Validation utilisateur avant preparation du dossier updates pour preprod/prod.
8090 est la derniere instance historique validee; son identite avec la production
actuelle ne peut etre affirmee sans nouvel export de production.
