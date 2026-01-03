CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oauth_id VARCHAR(255) UNIQUE, -- ID z Google/Githubu
    email VARCHAR(255),
    name VARCHAR(255)
);

CREATE TABLE servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Prepojenie na tabuľku users
    server_name VARCHAR(255),
    ip_address VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);