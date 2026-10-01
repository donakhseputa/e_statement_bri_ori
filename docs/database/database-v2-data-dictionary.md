# Database V2 Data Dictionary

## Purpose

Database V2 is the canonical PostgreSQL model for E-Statement BRI. It replaces legacy period/product-specific tables and mutable queue/history patterns with four bounded schemas: `core`, `import`, `pdf`, and `messaging`.

Files are stored in object storage; PostgreSQL stores business state, relationships, metadata, checksums, and lifecycle history.

## End-to-end flow

```text
core.products
    |
    +--> import.customer_batches --> import.customer_files --> import.customers --> import.customer_contacts
    |
    +--> import.statement_batches --> import.statement_files --> import.statements
                                                       |
                                                       v
                                                pdf.documents
                                                  /       \
                                            pdf.jobs    pdf.events
                                                       |
                                                       v
                                             messaging.deliveries
                                            /      |       |       \
                                  recipients attachments attempts events
```

## Schema: core

### core.products
Canonical product/service master. Replaces legacy product-code/`flagtrans` concepts and is referenced by imports, templates, schedules, attachments, and test recipients.

### core.users
Application operator/user identity. Stores login identity and account state. Historical records normally keep nullable attribution to the user rather than being deleted with the user.

### core.roles
Authorization role catalog.

### core.permissions
Granular capability catalog such as dashboard, import, product, template, or administration permissions.

### core.user_roles
Many-to-many pivot assigning roles to users.

### core.role_permissions
Many-to-many pivot assigning permissions to roles.

### core.attachments
Reusable non-statement attachments selectable for outbound deliveries. Replaces legacy `m_attach_file`. Physical content is stored externally using `storage_key`; metadata includes size, checksum, MIME type, optional product ownership, and optional content ID.

### core.settings
Global/dynamic application configuration stored as key + JSONB value. It is not intended for secrets.

### core.audit_logs
Application audit trail recording actor, action, entity reference, metadata, IP address, and occurrence time.

## Schema: import

The import domain deliberately distinguishes customer snapshot ingestion from statement ingestion.

### import.customer_batches
Lifecycle header for one customer-import execution for a product and statement period. Tracks status, counts, creator, and start/completion/failure timestamps.

### import.customer_files
Source-file metadata belonging to a customer batch. Stores object-storage key, checksum, byte size, row counts, and processing status.

### import.customers
Period-scoped imported customer snapshot, not a global customer master. Preserves the customer/account identity and address as supplied for a particular import batch. It may retain encrypted PDF-password material when required by the source contract and immutable `raw_data` for traceability.

### import.customer_contacts
Normalized one-to-many customer contacts. Replaces repeating legacy fields such as `email1`, `email2`, and `email3`. Supports email/phone values and preserves source order using `position`.

### import.statement_batches
Lifecycle header for one statement ingestion execution for a product/period. Optionally links to the customer batch used for enrichment/matching.

### import.statement_files
Source-file metadata for statement ingestion, including total/processed/valid/invalid row counters.

### import.statements
Canonical statement/account record. Replaces legacy `detail` and dynamic `detail_MMYYYY_product` tables. Stable cross-product attributes are typed columns; product-specific normalized extensions use `source_attributes`; the original source representation may be retained in `raw_data`.

A statement may reference its imported customer snapshot, but also stores important statement-time customer/account fields so historical output remains reproducible.

### import.errors
Durable row/file import validation or processing errors. Ownership is enforced with explicit customer-batch/file or statement-batch/file foreign keys instead of polymorphic numeric IDs.

### import.events
Import lifecycle/event timeline for customer or statement ingestion. Used for operational traceability independently of error records.

## Schema: pdf

### pdf.documents
Metadata for generated PDF artifacts associated with a statement. One statement may have multiple document versions. `(statement_id, version)` is unique so regeneration does not overwrite historical artifact metadata.

Stores file/object key, size, page count, checksum, encryption state, generation time, and lifecycle status.

### pdf.jobs
Units of work performed against a document, currently covering generate, merge, encrypt, stamp, and validate operations. Tracks attempts, status, timestamps, and failure information.

### pdf.events
Immutable timeline of significant document/job events. Complements `pdf.jobs`: jobs represent work units; events represent what happened and when.

## Schema: messaging

### messaging.mail_servers
Outbound mail-server configuration and limits. Credentials are referenced through `secret_reference`; plaintext SMTP passwords do not belong in this table.

### messaging.templates
Product-scoped email templates containing subject and HTML/text bodies and their selected mail server.

### messaging.test_recipients
Reusable/default sample or test destination addresses, optionally product-scoped. This replaces the legacy `sample_email` configuration concept. It is configuration only; actual recipients used for a send are snapshotted in `messaging.recipients`.

### messaging.schedules
Scheduling group/configuration for planned sends. Replaces legacy `m_jadwal`. Multiple deliveries may belong to one schedule.

### messaging.deliveries
Logical outbound delivery for a statement/document. Replaces the mutable legacy queue concept. Stores delivery type, priority, effective scheduling/queue/processing/send/failure timestamps, selected template/server, and optional schedule.

One statement may have multiple deliveries because production send, resend, sample, and test executions are distinct business executions.

### messaging.recipients
Immutable recipient snapshot for a delivery. Optionally references the imported customer contact that originated the address while retaining the exact email/name used historically. Supports TO/CC/BCC.

### messaging.attachments
Attachment snapshot for a delivery. A row references either a generated PDF document or a reusable `core.attachments` item and stores the delivery-time file metadata.

### messaging.delivery_attempts
Individual SMTP/provider send attempts for a logical delivery. Retry creates another attempt, not another delivery. Stores attempt number, server, provider message ID, status, timings, errors, and provider response metadata.

### messaging.delivery_events
Immutable delivery/provider feedback timeline. Represents queued/processing/sent/delivered/opened/clicked/bounced/complained/failed/cancelled events and replaces legacy read/bounce/callback history patterns.

### messaging.suppressions
Addresses that should not receive normal outbound delivery, permanently or until expiry. Covers hard bounce, complaint, unsubscribe, invalid-address, and manual suppression reasons.

## Important lifecycle distinctions

### Delivery vs attempt
A delivery is the business execution. An attempt is one provider/SMTP try within that execution. Retry increments attempts instead of duplicating the delivery.

### Delivery status vs provider event
Application send state and provider feedback are different facts. A message can be successfully sent and later bounce. Therefore `sent` remains historical send truth while `bounced` belongs to the event timeline.

### Customer contact vs recipient
`import.customer_contacts` describes the imported snapshot. `messaging.recipients` records the exact destination actually used for a delivery, protecting historical evidence when source contacts later change.

### Statement vs PDF
The statement is the business record; a PDF is an artifact generated from it. Multiple PDF versions may exist for one statement.

### Configuration vs history
`messaging.test_recipients`, templates, mail servers, schedules, settings, and reusable attachments are configuration/master data. Deliveries, attempts, events, documents, jobs, imports, errors, and audit logs are operational/history data.

## Legacy mapping summary

| Legacy concept | Database V2 |
| --- | --- |
| `mproduk` / `flagtrans` | `core.products` |
| user/menu access tables | `core.users`, `core.roles`, `core.permissions` |
| `m_customer` | `import.customers` + `import.customer_contacts` |
| `log_customer` | customer batches/files/events/errors |
| `m_loading` | statement batches/files |
| `detail`, `detail_MMYYYY_product` | `import.statements` |
| `m_attach_file` | `core.attachments` |
| PDF fields embedded in detail | `pdf.documents` |
| `template_email` | `messaging.templates` |
| `mail_server` | `messaging.mail_servers` |
| `sample_email` | `messaging.test_recipients` |
| `m_jadwal` | `messaging.schedules` |
| `antrian_email` | `messaging.deliveries` + recipients/attachments |
| `antrian_email_history` | delivery lifecycle + immutable events |
| `tr_email` | `messaging.delivery_attempts` |
| `read_email` | delivery event `opened` |
| `bounce_inbox` | delivery event `bounced` + suppression when applicable |
| send-error logs | attempts + events |
| suppression-list variants | `messaging.suppressions` |
| runtime timestamp views | parameterized reporting/query layer; no canonical table |

## Pre-freeze consistency item

The current migration for `messaging.deliveries.status` still permits `bounced`. This conflicts with the canonical lifecycle rule above: bounce is provider feedback and should be represented by `messaging.delivery_events`, while a previously successful send remains `sent`.

Before Schema Freeze v1.0, remove `bounced` from the delivery-status CHECK unless a separately audited application state requires it.

## Ownership and retention

Cross-lifecycle historical relationships default to PostgreSQL RESTRICT/NO ACTION where accidental parent deletion would destroy durable evidence. Cascades are reserved for true dependent children. Cleanup of historical imports, statements, PDFs, and delivery evidence should be an explicit retention/purge workflow.
