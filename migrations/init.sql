CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    email varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    is_active BOOLEAN default true NOT NULL,
    PRIMARY KEY (id)
);

CREATE UNIQUE INDEX projects_users_unique_index
ON users (email);
