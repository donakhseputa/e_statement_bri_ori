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


## Implemented Laravel migration revision
The Laravel migration draft has now been aligned with the audited semantics:
- Removed `core.customers`, `core.customer_accounts`, and `core.customer_contacts`.
- Added period-scoped `import.customer_batches`, `import.customer_files`, `import.customers`, and `import.customer_contacts`.
- Split statement ingestion into `import.statement_batches`, `import.statement_files`, and `import.statements`.
- `import.statements.customer_id` links a statement to the imported customer snapshot when a matching customer file exists.
- PDF lifecycle now references `import.statements` through `pdf.documents.statement_id`.
- Delivery lifecycle references `import.statements`; recipients may retain the source `import.customer_contacts` reference while snapshotting the actual email used.
- SMTP secrets remain external through `secret_reference`; the database does not store the plaintext legacy password.
- Legacy CID attachment semantics are preserved as `core.attachments.content_id`.

### Deliberate snapshot duplication
`messaging.recipients.email` remains even when `customer_contact_id` is present. This is intentional: delivery history must record the exact destination used at send time even if imported/contact data is corrected later.

### Remaining validation before canonical freeze
- Extract the complete column inventory of product-specific legacy detail tables and classify common vs product-specific attributes.
- Validate scheduling semantics from `m_jadwal` and related scripts before adding a schedule table.
- Validate sample/test-email behavior before adding a dedicated test-recipient table.
- Determine which imported statement attributes deserve typed relational columns versus source-specific JSONB.
- Benchmark representative statement volume before introducing native PostgreSQL partitioning.


## Legacy detail-table field audit

Representative loaders audited: B, BE, NC, BC, CK, CO, PK, SL, A1 and CE, plus multiple historical variants.

### Canonical relational fields
Fields repeatedly used across product loaders and/or operational queries are promoted to typed v2 columns:
- legacy `nomor_customer` -> `customer_number`
- `nomor_rekening` -> `account_number`
- `nama` -> `customer_name`
- `no_rek_asli` -> `original_account_number`
- `tipe_kartu` -> `account_type`
- `ket_produk` -> `product_description`
- `kode_cab` -> `branch_code`
- `cabang` -> `branch_name`
- `kurir` -> `courier_name`
- `barcode` -> `barcode`
- date-like recurring business fields -> typed `statement_date`, `billing_date`, `due_date` where the importer can parse them reliably.

### Fields moved out of statements
- `pdf_name`, `jml_hlm`, `size_pdf` -> `pdf.documents`.
- `password_pdf` -> encrypted/secret PDF handling, not ordinary statement data.
- legacy combined `email` and `n_email` -> normalized customer contacts and delivery recipients.
- address columns -> imported customer snapshot; do not duplicate them in every statement unless a source proves statement-specific address semantics.
- `flagtrans` -> `core.products` relationship through the batch.
- `blth` -> typed `statement_period` on the batch.
- `nama_file` -> `import.statement_files`.

### Product-specific attributes
Fields that exist only for individual loaders or whose semantics vary by product are kept in `import.statements.source_attributes` JSONB initially. They should be promoted to relational columns only when they are part of cross-product filtering/reporting, integrity constraints, or stable application behavior.

`raw_data` remains the immutable source-row representation for traceability. `source_attributes` is the normalized-but-product-specific extension payload. They serve different purposes and must not be treated interchangeably.

### Consequence
The v2 statement table is intentionally a hybrid: stable cross-product fields are typed and indexed; heterogeneous source-specific fields remain JSONB. This avoids both a giant sparse legacy-style table and an opaque JSON-only design.


## Delivery lifecycle and reporting audit

Audited legacy scheduling, queue, send, feedback and summary paths including `m_jadwal`, `antrian_email`, `antrian_email_history`, `tr_email`, `read_email`, `bounce_inbox`, error logs and summary queries.

### Canonical lifecycle
- `m_jadwal` -> `messaging.schedules`. A schedule is a reusable/group entity because multiple queued deliveries share one scheduled execution time.
- `antrian_email` -> `messaging.deliveries` with scheduled/queued state.
- `antrian_email_history` -> no copied history table. State transitions are retained through `messaging.delivery_events`.
- `tr_email` -> `messaging.delivery_attempts` plus delivery timestamps/state.
- send error logs -> failed attempt data plus delivery events.
- `read_email` -> delivery event type `opened` (source/provider details in metadata).
- `bounce_inbox` -> bounce delivery events; permanent addresses also create/update `messaging.suppressions`.
- legacy sample flags -> `messaging.deliveries.delivery_type` = production/sample/test.

### Reporting compatibility
Legacy summary reporting repeatedly calculates:
- statement/PDF count and page count,
- queued count,
- attempted/sent count,
- successful and failed count,
- opened/read count,
- bounce/other-feedback count,
- sample success/failure,
- scheduled send time.

The canonical schema can derive these from batches/documents/deliveries/attempts/events without mutable duplicated summary tables. At application scale, dashboard queries should use targeted indexes and optionally materialized/warehouse projections rather than reintroducing transactional summary counters as source of truth.

### Important semantic correction
A delivery status represents the application's send lifecycle. Provider feedback such as opened, bounced and complained is an event timeline and must not overwrite all historical send facts. A message can be successfully sent and later bounce; therefore attempts and events remain separate.
