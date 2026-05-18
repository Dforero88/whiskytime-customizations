-- PS 9 schema catch-up - secondary fixes
-- Target: production schema using prefix d1jy_
-- Execute after the priority script and after taking a database backup.

ALTER TABLE `d1jy_attachment`
    MODIFY COLUMN `file_name` varchar(255) NOT NULL;

ALTER TABLE `d1jy_attachment_lang`
    MODIFY COLUMN `name` varchar(255) DEFAULT NULL;

ALTER TABLE `d1jy_customized_data`
    MODIFY `value` varchar(1024) NOT NULL;

ALTER TABLE `d1jy_employee`
    ADD KEY `IDX_1D8DF9EBBA299860` (`id_lang`);

ALTER TABLE `d1jy_customer_message`
    ADD COLUMN `id_product` int(10) unsigned DEFAULT NULL AFTER `id_employee`,
    MODIFY COLUMN `user_agent` varchar(255) DEFAULT NULL,
    ADD KEY `id_product` (`id_product`);
