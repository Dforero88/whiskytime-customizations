-- PS 9 schema catch-up - priority fixes
-- Target: production schema using prefix d1jy_
-- Execute once after taking a database backup.

ALTER TABLE `d1jy_image_type`
    CHANGE `id_image_type` `id_image_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
    CHANGE `width` `width` int(10) unsigned NOT NULL,
    CHANGE `height` `height` int(10) unsigned NOT NULL,
    CHANGE `products` `products` tinyint(1) NOT NULL DEFAULT '1',
    CHANGE `manufacturers` `manufacturers` tinyint(1) NOT NULL DEFAULT '1',
    CHANGE `stores` `stores` tinyint(1) NOT NULL DEFAULT '1',
    DROP KEY `image_type_name`,
    ADD UNIQUE KEY `UNIQ_907C95215E237E06` (`name`);

ALTER TABLE `d1jy_access`
    ADD KEY `IDX_564352A15FCA037F` (`id_profile`),
    ADD KEY `IDX_564352A18C6DE0E5` (`id_authorization_role`);

ALTER TABLE `d1jy_employee_session`
    ADD KEY `IDX_B10E26A1D449934` (`id_employee`);

ALTER TABLE `d1jy_feature_flag`
    CHANGE `label_wording` `label_wording` varchar(191) NOT NULL DEFAULT '',
    CHANGE `description_wording` `description_wording` varchar(191) NOT NULL DEFAULT '';

ALTER TABLE `d1jy_mail`
    CHANGE `recipient` `recipient` varchar(255) NOT NULL,
    CHANGE `subject` `subject` varchar(255) NOT NULL;

ALTER TABLE `d1jy_meta_lang`
    CHANGE `url_rewrite` `url_rewrite` varchar(255) NOT NULL;

ALTER TABLE `d1jy_orders`
    CHANGE `reference` `reference` varchar(255) DEFAULT NULL,
    ADD KEY `invoice_date` (`invoice_date`);

ALTER TABLE `d1jy_order_payment`
    CHANGE `order_reference` `order_reference` varchar(255) DEFAULT NULL;

ALTER TABLE `d1jy_product`
    MODIFY COLUMN `ean13` varchar(20) DEFAULT NULL;

ALTER TABLE `d1jy_product_attribute`
    MODIFY COLUMN `ean13` varchar(20) DEFAULT NULL;

ALTER TABLE `d1jy_stock`
    MODIFY COLUMN `ean13` varchar(20) DEFAULT NULL;

ALTER TABLE `d1jy_supply_order_detail`
    MODIFY COLUMN `ean13` varchar(20) DEFAULT NULL;

ALTER TABLE `d1jy_product_download`
    ADD UNIQUE KEY `id_product` (`id_product`),
    ADD KEY `product_active` (`id_product`, `active`);

ALTER TABLE `d1jy_product_shop`
    MODIFY COLUMN `redirect_type` enum(
        '404',
        '410',
        '301-product',
        '302-product',
        '301-category',
        '302-category',
        '200-displayed',
        '404-displayed',
        '410-displayed',
        'default'
    ) NOT NULL DEFAULT 'default';

ALTER TABLE `d1jy_shop_url`
    CHANGE `id_shop_url` `id_shop_url` int(11) unsigned NOT NULL AUTO_INCREMENT,
    CHANGE `id_shop` `id_shop` int(11) unsigned NOT NULL,
    CHANGE `domain` `domain` varchar(255) NOT NULL,
    CHANGE `domain_ssl` `domain_ssl` varchar(255) NOT NULL,
    ADD UNIQUE KEY `full_shop_url` (`domain`, `physical_uri`, `virtual_uri`),
    ADD UNIQUE KEY `full_shop_url_ssl` (`domain_ssl`, `physical_uri`, `virtual_uri`),
    ADD KEY `id_shop` (`id_shop`, `main`);

ALTER TABLE `d1jy_stock_mvt`
    MODIFY `id_supply_order` int(11) DEFAULT '0';

ALTER TABLE `d1jy_tax_rules_group`
    CHANGE `name` `name` varchar(64) NOT NULL;
