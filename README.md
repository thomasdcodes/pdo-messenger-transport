# PdoMessengerTransport

## General

This Symfony bundle provides a PDO-based transport for the Symfony Messenger component.
It serves as a lightweight alternative to the official Doctrine transport, specifically designed for projects that do not use Doctrine ORM or DBAL but still require a reliable database-backed message queue.

## Installation
After installing the bundle, execute the follwoing Query to create the database table:

```SQL
CREATE TABLE messenger_messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    body LONGTEXT NOT NULL,
    headers LONGTEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    available_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL
);
```
