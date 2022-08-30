DROP DATABASE IF EXISTS task_force;

CREATE DATABASE task_force
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE task_force;

CREATE TABLE users
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(320) UNIQUE NOT NULL,
    password      VARCHAR(256) NOT NULL,
    name          VARCHAR(128) NOT NULL,
    is_performer  BOOLEAN      NOT NULL,
    avatar_path   VARCHAR(320),
    birthday      DATE,
    phone_number  VARCHAR(11),
    telegram      VARCHAR(64),
    registered_at DATETIME     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE specializations
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(128) NOT NULL,
    description   VARCHAR(320)
);

CREATE TABLE user_specializations
(
    user_id           INT NOT NULL,
    specialization_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (specialization_id) REFERENCES specializations (id)
);

CREATE TABLE categories
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(128) NOT NULL
);

CREATE TABLE tasks
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(128) NOT NULL,
    description   VARCHAR(320) NOT NULL,
    category_id   INT          NOT NULL,
    user_id       INT          NOT NULL,
    status        VARCHAR(128) NOT NULL,
    location      VARCHAR(128),
    budget        INT,
    deadline      DATETIME,
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE files
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    task_id       INT          NOT NULL,
    file_path     VARCHAR(320) NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks (id)
);

CREATE TABLE reviews
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    task_id       INT          NOT NULL,
    description   VARCHAR(320) NOT NULL,
    rating        INT          NOT NULL,
    reviewer_id   INT          NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (reviewer_id) REFERENCES users (id)
);

CREATE TABLE canceled_tasks
(
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    task_id       INT          NOT NULL,
    description   VARCHAR(320) NOT NULL,
    user_id       INT          NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);
