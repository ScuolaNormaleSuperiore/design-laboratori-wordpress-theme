CREATE USER 'admin'@localhost IDENTIFIED BY 'admin';
CREATE DATABASE  doclabdb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
GRANT ALL ON doclabdb.* TO 'admin'@'%' IDENTIFIED BY 'admin' WITH GRANT OPTION;
FLUSH PRIVILEGES;