# PS9 invoice PDF fix on Infomaniak

## Symptome

- BO commande accessible
- clic sur `Generer la facture PDF`
- erreur `500`
- log :
  - `admin_orders_generate_invoice_pdf`
  - puis `Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException: The file "" does not exist`

## Diagnostic retenu

Le symptome correspond au probleme documente par Infomaniak sur `PrestaShop 9.x` pour la generation PDF de facture.

Cause probable :
- incompatibilite entre la police PDF par defaut `helvetica`
- et l'environnement d'hebergement

Correctif recommande par Infomaniak :
- forcer `PDF_FONT_NAME_MAIN` a `freesans`
- via `config/defines_custom.inc.php`

## Correctif

Fichier a deployer :

```php
<?php
define('PDF_FONT_NAME_MAIN', 'freesans');
```

## Source

- Infomaniak FAQ:
  - [Fix a PDF error on PrestaShop 9.x](https://www.infomaniak.com/en/support/faq/2690/fix-a-pdf-error-on-prestashop-9x)
