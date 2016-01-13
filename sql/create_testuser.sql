GRANT USAGE ON *.* TO 'testuser'@'localhost';

DROP USER 'testuser'@'localhost';

CREATE USER 'testuser'@'localhost' IDENTIFIED BY 'testuser';

GRANT USAGE ON *.* TO 'testuser'@'localhost' IDENTIFIED BY PASSWORD '*3A2EB9C80F7239A4DE3933AE266DB76A7846BCB8';

GRANT SELECT ON `visqlizer`.* TO 'testuser'@'localhost';