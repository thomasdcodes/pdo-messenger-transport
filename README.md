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

### 1. Configure PDO Service
The bundle requires a `\PDO` instance to be registered in your service container. Ensure that a PDO instance is available for autowiring:

```yaml
# config/services.yaml
services:
    PDO:
        class: PDO
        arguments:
            - "mysql:host=%env(DB_HOST)%;dbname=%env(DB_NAME)%"
            - "%env(DB_USER)%"
            - "%env(DB_PASSWORD)%"
        calls:
            - [setAttribute, [3, 2]] # PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
```

### 2. Configure Symfony Messenger
In your messenger configuration, use the `pdoqueue://` DSN. You can specify the queue name and table name if needed.

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'pdoqueue://default'
                options:
                    queue_name: 'high_priority' # optional, defaults to 'default'
                    table_name: 'messenger_messages' # optional, defaults to global bundle config
```

### 3. Global Bundle Configuration (Optional)
You can set a default table name for all transports in a separate configuration file:

```yaml
# config/packages/tdc_pdo_messenger_transport.yaml
tdc_pdo_messenger_transport:
    table_name: 'messenger_messages'
```

### Usage
Run the messenger worker as usual:
```bash
php bin/console messenger:consume async
```
