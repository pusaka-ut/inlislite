CREATE TABLE IF NOT EXISTS `master_rak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_rak` varchar(50) NOT NULL,
  `nama_rak` varchar(255) NOT NULL,
  `location_id` int(11) NOT NULL,
  `range_ddc_awal` varchar(20) DEFAULT NULL,
  `range_ddc_akhir` varchar(20) DEFAULT NULL,
  `call_number_prefix` varchar(50) DEFAULT NULL,
  `keterangan` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_kode_rak` (`kode_rak`),
  KEY `fk_rak_location` (`location_id`),
  CONSTRAINT `fk_rak_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
