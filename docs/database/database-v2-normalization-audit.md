# Database v2 normalization audit

## Objective
Database v2 preserves legacy business semantics while replacing legacy physical design with normalized, maintainable PostgreSQL structures. New concepts are allowed when they remove duplication, make lifecycle/state explicit, improve integrity, or support current service boundaries.

## Confirmed legacy findings
- `m_customer` is period/import-scoped: application queries bind it to `blth`, `flagtrans`, and `log_customer_id`. It is not a clean global customer master.
- Customer email addresses are repeating columns (`email1`, `email2`, `email3`). Legacy views UNION these columns to obtain recipients.
- Statement detail tables are generated dynamically as `detail_<MMYYYY>_<product>`.
- Delivery transaction tables also exist per period/product as `tr_email_<MMYYYY>_<product>`.
- `m_loading` is the import/statement-file lifecycle header and carries product/period/file totals.
- `antrian_email` is a mutable queue; after successful processing it is copied to `antrian_email_history` and deleted.
- `tr_email` records send activity while `read_email`, `bounce_inbox`, error logs and callback columns separately represent delivery feedback.
- `template_email` belongs to a product and mail server.
- `m_attach_file` is reused by templates/deliveries and stores filesystem-oriented attachment metadata.
- Mail server credentials are stored directly in the legacy database.
- Runtime code creates temporary timestamp-named views for reporting/filtering.
- Legacy code retrieves inserted identifiers through sequence `last_value`, which is unsafe under concurrency compared with INSERT ... RETURNING / ORM-generated IDs.

## v2 decisions
| Legacy concept | v2 decision | Target |
|---|---|---|
| mproduk | retain + rename | core.products |
| user1/access/menu tables | replace | Laravel-compatible users/roles/permissions |
| m_customer + history variants | normalize as imported customer snapshots | import.customers + import.customer_contacts |
| log_customer | merge into customer import batch/file lifecycle | import.customer_batches + import.customer_files |
| m_loading | normalize | import.statement_batches + import.statement_files |
| detail + detail_MMYYYY_product | consolidate | import.statements |
| repeated email1..email3 | normalize | import.customer_contacts |
| m_attach_file | retain concept + object-storage metadata | core.attachments |
| template_email | normalize | messaging.templates |
| mail_server | normalize + external secret reference | messaging.mail_servers |
| antrian_email | replace mutable queue-table pattern | messaging.deliveries |
| antrian_email_history | eliminate copy/delete history pattern | delivery status + immutable events |
| antrian_email_attach_* | merge | messaging.attachments |
| tr_email + tr_email_MMYYYY_product | consolidate | messaging.delivery_attempts |
| read_email | merge as immutable event | messaging.delivery_events |
| bounce_inbox | merge raw provider feedback/event | messaging.delivery_events (+ suppressions when applicable) |
| suppression variants | consolidate | messaging.suppressions |
| log_error_kirim | merge | messaging.delivery_attempts/events |
| runtime vw_DDMMYYHHMMSS | discard | parameterized queries/reporting layer |
| PDF fields embedded in detail | extract lifecycle | pdf.documents + pdf.jobs/events |
| m_jadwal / scheduling scripts | replace concept | messaging.schedules (if scheduling remains a product requirement) |
| sample_email | retain concept | messaging.test_recipients |

## Important correction from first draft
Do not model imported `m_customer` rows primarily as `core.customers`. The legacy semantics show that customer/recipient data is a period-bound snapshot. v2 therefore keeps imported customer snapshots under the `import` domain. A future canonical customer master may be added to `core` only if a verified business requirement requires cross-period identity/master-data management.

## Normalization policy
- Master/configuration data: target 3NF.
- Imported statement/customer data: normalized for repeating groups and entity relationships, while preserving immutable period snapshots.
- Operational event history: append-only event/attempt tables rather than copying current rows to history tables.
- Historical values required to reproduce a statement/email may be snapshotted deliberately; this is controlled denormalization.
- JSONB is reserved for source payload/provider metadata that is not part of the relational query contract.

## Performance policy
- No application-visible table-per-month/product.
- Use one logical relation per entity.
- Index from actual access patterns: batch+period, account+period, queue status+priority+time, delivery+attempt/event time.
- Queue workers should claim rows using PostgreSQL-safe concurrency (e.g. FOR UPDATE SKIP LOCKED) or RabbitMQ, not delete/move rows as the queue mechanism.
- Partition high-volume statement/delivery-event relations only after volume/query benchmarks; partitioning is a physical optimization, not a domain model.
- Files/PDFs live in object storage; PostgreSQL stores keys, checksums and metadata.

## Migration status
Existing Laravel migrations on this branch are a draft and must be revised to this audited model before being treated as canonical.
