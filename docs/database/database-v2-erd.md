# Database V2 Canonical ERD

This document is the canonical relationship view for Database V2 after successful Laravel `migrate:fresh` validation.

## Core and authorization

```mermaid
erDiagram
  PRODUCTS ||--o{ ATTACHMENTS : owns
  USERS ||--o{ USER_ROLES : assigned
  ROLES ||--o{ USER_ROLES : contains
  ROLES ||--o{ ROLE_PERMISSIONS : grants
  PERMISSIONS ||--o{ ROLE_PERMISSIONS : contains
  USERS ||--o{ AUDIT_LOGS : creates
```

## Import

```mermaid
erDiagram
  PRODUCTS ||--o{ CUSTOMER_BATCHES : imports
  CUSTOMER_BATCHES ||--o{ CUSTOMER_FILES : contains
  CUSTOMER_BATCHES ||--o{ CUSTOMERS : snapshots
  CUSTOMER_FILES o|--o{ CUSTOMERS : sources
  CUSTOMERS ||--o{ CUSTOMER_CONTACTS : has

  PRODUCTS ||--o{ STATEMENT_BATCHES : imports
  CUSTOMER_BATCHES o|--o{ STATEMENT_BATCHES : enriches
  STATEMENT_BATCHES ||--o{ STATEMENT_FILES : contains
  STATEMENT_BATCHES ||--o{ STATEMENTS : contains
  STATEMENT_FILES o|--o{ STATEMENTS : sources
  CUSTOMERS o|--o{ STATEMENTS : matches

  CUSTOMER_BATCHES o|--o{ IMPORT_ERRORS : owns
  CUSTOMER_FILES o|--o{ IMPORT_ERRORS : sources
  STATEMENT_BATCHES o|--o{ IMPORT_ERRORS : owns
  STATEMENT_FILES o|--o{ IMPORT_ERRORS : sources

  CUSTOMER_BATCHES o|--o{ IMPORT_EVENTS : owns
  CUSTOMER_FILES o|--o{ IMPORT_EVENTS : sources
  STATEMENT_BATCHES o|--o{ IMPORT_EVENTS : owns
  STATEMENT_FILES o|--o{ IMPORT_EVENTS : sources
```

## PDF

```mermaid
erDiagram
  STATEMENTS ||--o{ DOCUMENTS : generates
  DOCUMENTS ||--o{ PDF_JOBS : processes
  DOCUMENTS ||--o{ PDF_EVENTS : records
  PDF_JOBS o|--o{ PDF_EVENTS : emits
```

A statement has zero or many PDF document versions. `(statement_id, version)` uniquely identifies a version.

## Messaging

```mermaid
erDiagram
  PRODUCTS ||--o{ TEMPLATES : owns
  MAIL_SERVERS ||--o{ TEMPLATES : sends_with
  PRODUCTS ||--o{ TEST_RECIPIENTS : configures
  PRODUCTS ||--o{ SCHEDULES : schedules

  STATEMENTS ||--o{ DELIVERIES : delivers
  DOCUMENTS o|--o{ DELIVERIES : primary_document
  TEMPLATES ||--o{ DELIVERIES : renders
  MAIL_SERVERS ||--o{ DELIVERIES : sends_with
  SCHEDULES o|--o{ DELIVERIES : groups

  DELIVERIES ||--o{ RECIPIENTS : targets
  CUSTOMER_CONTACTS o|--o{ RECIPIENTS : originates
  DELIVERIES ||--o{ MESSAGE_ATTACHMENTS : includes
  DOCUMENTS o|--o{ MESSAGE_ATTACHMENTS : source
  ATTACHMENTS o|--o{ MESSAGE_ATTACHMENTS : source

  DELIVERIES ||--o{ DELIVERY_ATTEMPTS : retries
  MAIL_SERVERS ||--o{ DELIVERY_ATTEMPTS : uses
  DELIVERIES ||--o{ DELIVERY_EVENTS : records
  DELIVERY_ATTEMPTS o|--o{ DELIVERY_EVENTS : relates
```

## End-to-end cardinality

```text
Product
  |
  +-- Customer Batch --< Customer File
  |        |
  |        +--< Customer --< Contact
  |
  +-- Statement Batch --< Statement File
           |
           +--< Statement
                  |
                  +--< PDF Document (versioned)
                  |       +--< PDF Job
                  |       +--< PDF Event
                  |
                  +--< Delivery
                         +--< Recipient
                         +--< Attachment
                         +--< Attempt
                         +--< Event
```

## Relationship rules

- Imported customers are period snapshots, not a mutable global customer master.
- Statements retain statement-time fields even when linked to an imported customer snapshot.
- A statement can produce multiple PDF versions.
- A statement can produce multiple logical deliveries (production, resend through a new execution, sample, or test).
- A delivery can have multiple SMTP/provider attempts.
- Provider feedback such as delivered/opened/bounced is an event, not a replacement for send history.
- Delivery recipients and attachments preserve execution-time snapshots.
- Historical parent relationships use restrictive deletion by default; true dependent children may cascade.
