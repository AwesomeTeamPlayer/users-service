CREATE TABLE users (
    email varchar(255) NOT NULL,
    'name' varchar(255) NOT NULL,
    is_active BOOLEAN default true NOT NULL,
);

CREATE UNIQUE INDEX projects_users_unique_index
ON projects_users (email);
