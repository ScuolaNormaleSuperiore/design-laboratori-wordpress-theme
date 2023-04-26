CREATE USER 'admin'@localhost IDENTIFIED BY 'admin';
CREATE DATABASE  demolabdb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
GRANT ALL ON demolabdb.* TO 'admin'@'%' IDENTIFIED BY 'admin' WITH GRANT OPTION;
FLUSH PRIVILEGES;