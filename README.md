# PdoMessengerTransport

## General

This Symfony bundle provides a PDO-based transport for the Symfony Messenger component.
It serves as a lightweight alternative to the official Doctrine transport, specifically designed for projects that do not use Doctrine ORM or DBAL but still require a reliable database-backed message queue.

## Installation

### 1. Register the bundle
Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    Tdc\PdoMessengerTransport\TdcPdoMessengerTransport::class => ['all' => true],
];
```

### 2. Prepare the database
Execute the following Query to create the database table:

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

## Configuration

### 1. Register the PDO Service
The bundle requires a `\PDO` instance to be registered in your service container. You can define a new PDO service or use an existing one (e.g., from Doctrine):

```yaml
# config/services.yaml
services:
    PDO:
        class: PDO
        arguments:
            - "mysql:host=%env(DB_HOST)%;dbname=%env(DB_NAME)%"
            - "%env(DB_USER)%"
            - "%env(DB_PASSWORD)%"
```

### 2. Global Bundle Configuration
In your `config/packages/tdc_pdo_messenger_transport.yaml` (optional, but recommended if using a specific PDO service ID):

```yaml
tdc_pdo_messenger_transport:
    pdo_service: 'PDO' # The ID of your PDO service
    table_name: 'messenger_messages' # default
```

### 3. Configure Symfony Messenger
In your messenger configuration, use the `pdoqueue://` DSN.

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: 'pdoqueue://default'
```

### Usage
Run the messenger worker as usual:
```bash
php bin/console messenger:consume async
```
