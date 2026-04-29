---------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS `journal_sms`;
CREATE TABLE IF NOT EXISTS `journal_sms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact` varchar(20) NOT NULL,
  `contenu` LONGTEXT NOT NULL,
  `status_envoi` enum('CREE','ENVOYE') NOT NULL,
  `date_envoi` datetime NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
    FOREIGN KEY (user_id) 
        REFERENCES users(id)
);


ALTER TABLE `demande_vehicules` ADD COLUMN `beneficiaire_id` int(10) UNSIGNED DEFAULT NULL;
ALTER TABLE `demande_vehicules` ADD FOREIGN KEY (beneficiaire_id) REFERENCES users(id);

ALTER TABLE `journal_sms` CHANGE `status_envoi` `status_envoi` ENUM('CREE','ENVOYE','ECHEC') NOT NULL;