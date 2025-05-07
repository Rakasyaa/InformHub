CREATE DATABASE simple_login;

USE simple_login;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- User default (email: user@example.com, password: 123456)
INSERT INTO users (email, password) VALUES (
    'user',
	'123'
);
