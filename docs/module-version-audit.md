# Module Version Audit

Utilitaire lean pour comparer :
- la version module stockée en base
- la version module présente sur le disque

## Fichier

- `tools/module_version_audit.php`

## Ce que fait l'outil

- détecte automatiquement la racine Prestashop si possible
- lit le préfixe DB via le bootstrap Prestashop
- parcourt les modules installés en base
- lit la version disque via `config.xml`
- fallback sur `<module>.php` si besoin
- affiche :
  - `module`
  - `db_version`
  - `disk_version`
  - `status`
  - `source`
- peut générer le SQL de réalignement pour un module donné

## Usage CLI

```bash
php repo-github/tools/module_version_audit.php --root=/chemin/vers/prestashop
```

Uniquement les mismatchs :

```bash
php repo-github/tools/module_version_audit.php --root=/chemin/vers/prestashop --mismatch-only=1
```

SQL pour un module précis :

```bash
php repo-github/tools/module_version_audit.php --root=/chemin/vers/prestashop --module=ps_linklist --sql=1
```

## Usage HTTP

Si le fichier est copié dans une instance Prestashop :

- `/tools/module_version_audit.php?mismatch_only=1`
- `/tools/module_version_audit.php?module=ps_linklist&sql=1`

## Statuts

- `ok` : version DB = version disque
- `mismatch` : version DB != version disque
- `disk-version-missing` : version disque non détectée
