# AlertHub

AlertHub is a multi-tenant alert management system built with Laravel. It allows organizations to manage projects, subscribers, alert rules, webhook sources, and notification delivery through a pipeline-based architecture.

## Features

* Multi-tenant architecture using organization API tokens
* Project management APIs
* Subscriber management APIs
* Alert rule management
* Webhook source management
* Secure webhook ingestion using HMAC SHA-256 signature verification
* Notification processing using a pipeline pattern
* Queue-based notification dispatching
* Feature tests covering critical workflows and edge cases

---

## Tech Stack

* PHP 8.4
* Laravel 13
* MySQL
* Redis
* Docker & Docker Compose
* PHPUnit

---

## Getting Started

### Start the application

```bash
docker compose up -d
```

### Install Composer dependencies

```bash
docker exec -it alerthub_app composer install
```

### Copy environment configuration

```bash
docker exec -it alerthub_app cp .env.example .env
```

### Generate application key

```bash
docker exec -it alerthub_app php artisan key:generate
```

### Run database migrations and seeders

```bash
docker exec -it alerthub_app php artisan migrate --seed
```

### Start queue worker

```bash
docker exec -it alerthub_app php artisan queue:work
```

---

## Running Tests

Execute the full test suite:

```bash
docker exec -it alerthub_app php artisan test
```

Expected output:

```text
PASS  Tests\Feature\AuthenticationTest
PASS  Tests\Feature\ProjectTest
PASS  Tests\Feature\SubscriberTest
PASS  Tests\Feature\AlertRuleTest
PASS  Tests\Feature\WebhookSourceTest
PASS  Tests\Feature\WebhookTest

Tests:    11 passed (20 assertions)
Duration: 0.58s
```

---

## Authentication

Protected endpoints require an organization API token.

Include the following header:

```text
Authorization: Bearer {organization_api_token}
```

---

## Webhook Security

Webhook requests are authenticated using HMAC SHA-256 signatures.

Generate the signature using the raw request body and the webhook source signing secret:

```php
$signature = hash_hmac(
    'sha256',
    $requestBody,
    $signingSecret
);
```

Include the generated signature in the request header:

```text
X-Signature: {generated_signature}
```

Requests with invalid or missing signatures will be rejected.

---

## API Workflow

1. Create an organization.
2. Obtain the organization's API token.
3. Create a project.
4. Register subscribers under the project.
5. Create alert rules for the project.
6. Configure webhook sources.
7. Send webhook requests with a valid `X-Signature`.
8. Notifications are generated and queued for delivery.

---

## Design Decisions

### Multi-tenancy

Multi-tenancy is enforced through middleware using organization API tokens. All protected resources are scoped to the authenticated organization.

### Webhook Verification

Webhook requests are verified using HMAC SHA-256 signatures to ensure payload authenticity and integrity.

### Notification Pipeline

Alert processing is implemented using a pipeline pattern to separate responsibilities such as:

* Rule matching
* Subscriber matching
* Notification creation
* Notification dispatching

This improves maintainability and extensibility.

### Queue Processing

Notifications are dispatched asynchronously using Laravel queues to prevent webhook requests from being blocked by downstream notification delivery.

---

## Feature Tests

The following scenarios are covered by automated tests:

* Requests without authentication are rejected
* Requests with invalid API tokens are rejected
* Organizations can only access their own projects
* Projects can be created successfully
* Project validation rules are enforced
* Subscribers can be added to projects
* Alert rules can be created
* Webhook sources can be created
* Webhook requests with valid signatures are accepted
* Webhook requests with invalid signatures are rejected
* Webhook processing handles edge cases safely

---

## Assumptions

* Organizations authenticate using API tokens.
* Webhook clients authenticate using HMAC signatures.
* Notification delivery is handled asynchronously through Laravel queues.
* Email is used as the default notification channel in this implementation.

---

## Useful Commands

Clear application cache:

```bash
docker exec -it alerthub_app php artisan optimize:clear
```

Access Laravel container shell:

```bash
docker exec -it alerthub_app sh
```

View queue worker logs:

```bash
docker logs -f alerthub_app
```

Stop the application:

```bash
docker compose down
```
