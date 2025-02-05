CREATE TABLE IF NOT EXISTS wp_dli_custom_translations (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  label TEXT NOT NULL,
  domain VARCHAR(100) NOT NULL,
  lang    VARCHAR(2) NOT NULL,
  translation TEXT NOT NULL,
  PRIMARY KEY (id),
  KEY idx_text_domain (domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;