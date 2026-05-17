ALTER TABLE `PREFIX_feature_flag`
ADD COLUMN `type` varchar(64) NOT NULL DEFAULT 'env,dotenv,db' AFTER `name`;
