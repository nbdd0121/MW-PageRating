CREATE TABLE pagerating_records (
  prr_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  prr_pageid INT(10) UNSIGNED NOT NULL,
  prr_user VARCHAR(40) NOT NULL,
  # A user column with varchar(40) is used here by intention
  # 40 is large enough to store IPv6 address, IPv4 address, and user id
  prr_timestamp BINARY(14) NOT NULL,
  prr_score TINYINT NOT NULL
);
	
CREATE UNIQUE INDEX pagerating_index ON pagerating_records (prr_pageid, prr_user);

