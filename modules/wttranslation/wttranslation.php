<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WtTranslationException extends RuntimeException
{
}

class WtTranslation extends Module
{
    public function __construct()
    {
        $this->name = 'wttranslation';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Whisky Time';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = 'WT Traduction';
        $this->description = 'Traduction manuelle FR vers EN des contenus produits manquants.';
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }
        $parent = (int) Tab::getIdFromClassName('WHISKYTIME');
        if (!$parent) {
            $parent = (int) Tab::getIdFromClassName('AdminWhiskyTime');
        }
        if (!$parent) {
            $tab = new Tab();
            $tab->class_name = 'WHISKYTIME';
            $tab->id_parent = 0;
            $tab->module = '';
            $tab->active = 1;
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[$lang['id_lang']] = 'Whisky Time';
            }
            if (!$tab->add()) {
                return false;
            }
            $parent = (int) $tab->id;
        }
        $tab = new Tab();
        $tab->class_name = 'AdminWtTranslation';
        $tab->module = $this->name;
        $tab->id_parent = $parent;
        $tab->active = 1;
        $tab->icon = 'translate';
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'WT Traduction';
        }
        return (bool) $tab->add() && $this->registerHook('displayBackOfficeHeader');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (!$this->context->employee || !$this->context->employee->id
            || Shop::getContext() !== Shop::CONTEXT_SHOP) {
            return;
        }
        $this->context->controller->addJS($this->_path . 'views/js/menu.js');
        $this->context->controller->addCSS($this->_path . 'views/css/menu.css');
        Media::addJsDef(['wtTranslationMenu' => [
            'url' => $this->context->link->getAdminLink('AdminWtTranslation'),
            'cacheKey' => 'wtTranslationCount_' . (int) $this->context->employee->id . '_' . (int) $this->context->shop->id,
        ]]);
    }

    public function uninstall()
    {
        $id = (int) Tab::getIdFromClassName('AdminWtTranslation');
        if ($id && !(new Tab($id))->delete()) {
            return false;
        }
        Configuration::deleteByName('WTTRANSLATION_KEY');
        return parent::uninstall();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminWtTranslation'));
    }

    public static function hasText($value)
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
        return preg_match('/[^\s\x{00A0}\x{200B}]/u', $text) === 1;
    }

    public function candidates($idProduct = 0)
    {
        $fr = (int) Language::getIdByIso('fr');
        $en = (int) Language::getIdByIso('en');
        if (!$fr || !$en) {
            throw new WtTranslationException('Les langues FR et EN doivent être installées.');
        }
        $shop = (int) $this->context->shop->id;
        $rows = Db::getInstance()->executeS('SELECT p.id_product, p.reference,
            f.name, f.description AS fr_description, f.description_short AS fr_short,
            e.description AS en_description, e.description_short AS en_short
            FROM ' . _DB_PREFIX_ . 'product p
            INNER JOIN ' . _DB_PREFIX_ . 'product_lang f ON f.id_product=p.id_product
              AND f.id_lang=' . $fr . ' AND f.id_shop=' . $shop . '
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang e ON e.id_product=p.id_product
              AND e.id_lang=' . $en . ' AND e.id_shop=' . $shop . '
            WHERE p.reference REGEXP \'^[0-9]{2}-[0-9]{5}$\''
            . ($idProduct ? ' AND p.id_product=' . (int) $idProduct : '') . ' ORDER BY p.id_product DESC');
        if ($rows === false) {
            throw new WtTranslationException('Impossible de lire les produits.');
        }
        return array_values(array_filter($rows, function ($row) {
            return (self::hasText($row['fr_description']) && !self::hasText($row['en_description']))
                || (self::hasText($row['fr_short']) && !self::hasText($row['en_short']));
        }));
    }

    protected function requestTranslation(array $texts)
    {
        $key = Configuration::get('WTTRANSLATION_KEY');
        if (!$key) {
            throw new WtTranslationException('Renseignez la clé DeepL avant de traduire.');
        }
        $url = substr($key, -3) === ':fx' ? 'https://api-free.deepl.com/v2/translate' : 'https://api.deepl.com/v2/translate';
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => ['Authorization: DeepL-Auth-Key ' . $key, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['text' => array_values($texts), 'source_lang' => 'FR',
                'target_lang' => 'EN-GB', 'tag_handling' => 'html'], JSON_THROW_ON_ERROR)]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body === false || $status !== 200) {
            $messages = [403 => 'Clé DeepL invalide ou abonnement incompatible.',
                456 => 'Quota DeepL épuisé.', 429 => 'DeepL est temporairement surchargé. Relancez plus tard.'];
            throw new WtTranslationException($messages[$status] ?? 'Erreur de connexion DeepL (HTTP ' . $status . ').');
        }
        $result = json_decode($body, true);
        if (count($result['translations'] ?? []) !== count($texts)) {
            throw new WtTranslationException('Réponse DeepL incomplète.');
        }
        return array_column($result['translations'], 'text');
    }

    public function translateProduct($id)
    {
        $row = null;
        foreach ($this->candidates($id) as $candidate) {
            if ((int) $candidate['id_product'] === (int) $id) {
                $row = $candidate;
                break;
            }
        }
        if (!$row) {
            return 0;
        }
        $texts = [];
        foreach (['description' => ['fr_description', 'en_description'], 'description_short' => ['fr_short', 'en_short']] as $field => $keys) {
            if (self::hasText($row[$keys[0]]) && !self::hasText($row[$keys[1]])) {
                $texts[$field] = $row[$keys[0]];
            }
        }
        $translations = $this->requestTranslation($texts);
        $en = (int) Language::getIdByIso('en');
        $fr = (int) Language::getIdByIso('fr');
        $shop = (int) $this->context->shop->id;
        $count = 0;
        foreach (array_keys($texts) as $i => $field) {
            $translation = $translations[$i];
            if (!self::hasText($translation) || !Validate::isCleanHtml($translation)) {
                throw new WtTranslationException('La traduction reçue est vide ou contient du HTML non autorisé.');
            }
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($field === 'description_short' && $limit > 0 && Tools::strlen(strip_tags($translation)) > $limit) {
                throw new WtTranslationException('Le récapitulatif traduit dépasse la limite de ' . $limit . ' caractères.');
            }
            // Lock both language rows so an edit during the API call is never overwritten.
            $db = Db::getInstance();
            $db->execute('START TRANSACTION');
            try {
                $fresh = $db->executeS('SELECT id_lang, `' . $field . '` FROM ' . _DB_PREFIX_ . 'product_lang
                    WHERE id_product=' . (int) $id . ' AND id_shop=' . $shop . ' AND id_lang IN (' . $fr . ',' . $en . ') FOR UPDATE');
                $values = array_column($fresh, $field, 'id_lang');
                if (!array_key_exists($en, $values)) {
                    throw new WtTranslationException('La ligne anglaise du produit est absente. Enregistrez sa fiche avant de relancer.');
                }
                if (!self::hasText($values[$en]) && $values[$fr] === $texts[$field]) {
                    if (!$db->update('product_lang', [$field => pSQL($translation, true)],
                        'id_product=' . (int) $id . ' AND id_shop=' . $shop . ' AND id_lang=' . $en)) {
                        throw new WtTranslationException('Impossible de sauvegarder la traduction.');
                    }
                    ++$count;
                }
                $db->execute('COMMIT');
            } catch (Throwable $e) {
                $db->execute('ROLLBACK');
                throw $e;
            }
        }
        if ($count) {
            Cache::clean('objectmodel_Product_' . (int) $id . '_*');
        }
        return $count;
    }
}
