# WhiskyTime Customizations

Base Git pour les personnalisations WhiskyTime issues de la production et reprises en local.

Contenu versionne:

- `themes/wino/`
- `override/`
- `modules/` limites aux modules retenus dans Notion avec l'etat:
  - `Installe et configure`
  - `Installe et configure mais a revoir`

Modules inclus:

- `aei_brandlogo`
- `aei_categoryslider`
- `aei_cmsbanner`
- `aei_cmspayment`
- `aei_cmstop`
- `aei_imageslider`
- `aei_leftbanner`
- `aei_sidespecials`
- `ageverification`
- `ets_blog`
- `ets_htmlbox`
- `eventsmanager`
- `giftcard`
- `pagenotfound`
- `eventtest`
- `generatepdfgiftcard`
- `limitoneproduct`
- `passwordrenewlog`
- `pdfcustomerslist`
- `productwithouttags`

Contenu volontairement exclu:

- base de donnees
- images produit/globales
- modules natifs non modifies
- modules tiers non retenus
- stack Docker locale

Usage recommande:

1. developper en local
2. committer les modifications par module/theme/override
3. deployer uniquement les fichiers touches en production
4. vider le cache Prestashop apres deploiement
