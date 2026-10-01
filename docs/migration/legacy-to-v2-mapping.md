# Legacy to Database V2 Mapping Matrix

This matrix records the canonical disposition of legacy database concepts during migration to Database V2.

| Legacy object / pattern | V2 destination | Action | Transformation / rationale |
| --- | --- | --- | --- |
| `mproduk`, product `flagtrans` | `core.products` | Normalize | One canonical product master; legacy codes become product codes. |
| `user1` and equivalent user data | `core.users` | Normalize | English columns, explicit active/login state, password handled by application migration policy. |
| `menu1`, `menugroup1` | Application navigation | Discard as DB authorization | Navigation belongs to application code; it is not the authorization source of truth. |
| `usermenu1` / legacy access assignments | roles + permissions | Replace | Convert legacy access semantics to `core.roles`, `core.permissions`, pivots. |
| legacy global/config values | `core.settings` | Normalize selectively | Only genuine dynamic application configuration; secrets excluded. |
| `m_attach_file` | `core.attachments` | Normalize | File moved to object storage; retain metadata/key/checksum/product association. |
| operational user activity | `core.audit_logs` | Replace | Canonical actor/action/entity audit trail. |
| `log_customer` | `import.customer_batches`, `customer_files`, events/errors | Decompose | Separate execution, physical source file, lifecycle event, and error concerns. |
| `m_customer` + history variants | `import.customers` | Normalize as snapshot | Customer is period/import snapshot, not mutable global master. |
| `email1`, `email2`, `email3` | `import.customer_contacts` | Normalize | Repeating columns become one-to-many typed contacts. |
| `m_loading` | `import.statement_batches`, `statement_files` | Decompose | Separate batch lifecycle from source-file metadata. |
| `detail` | `import.statements` | Normalize | Stable fields become typed canonical columns. |
| `detail_MMYYYY_product` | `import.statements` | Merge | Eliminate table-per-period/product. Period/product are relational attributes through batch/product. |
| product-specific detail columns | `import.statements.source_attributes` | Controlled extension | JSONB only for source attributes outside stable relational query contract. |
| original imported row | `raw_data` | Retain for audit | Preserve source evidence without making it the canonical query model. |
| import validation/error logs | `import.errors` | Normalize | Explicit FK ownership to customer or statement batch/file. |
| import processing logs | `import.events` | Normalize | Durable lifecycle timeline. |
| embedded `pdf_name`, `jml_hlm`, PDF metadata | `pdf.documents` | Extract | PDF becomes versioned artifact entity with object-storage metadata. |
| PDF processing state/scripts | `pdf.jobs`, `pdf.events` | Replace | Explicit work units and immutable event history. |
| legacy PDF password | encrypted snapshot material | Retain securely | Never plaintext; migration must preserve required statement-generation semantics. |
| `mail_server` | `messaging.mail_servers` | Normalize | SMTP metadata and limits retained; secret becomes external `secret_reference`. |
| `template_email` | `messaging.templates` | Normalize | Product-scoped template with explicit mail-server relationship. |
| `sample_email` | `messaging.test_recipients` | Normalize | Reusable test/sample recipient configuration, not send history. |
| `m_jadwal` | `messaging.schedules` | Normalize | Scheduling group/config entity. |
| `antrian_email` | `messaging.deliveries` | Replace | Durable logical delivery lifecycle; rows are not moved/deleted to represent queue state. |
| queue recipient fields | `messaging.recipients` | Normalize + snapshot | TO/CC/BCC rows preserve actual execution destination. |
| `antrian_email_attach_*` | `messaging.attachments` | Normalize + snapshot | Explicit document/reusable attachment relationship with execution-time metadata. |
| `antrian_email_history` | delivery state + events | Eliminate copy-history pattern | History remains in canonical row + immutable event/attempt records. |
| `tr_email`, monthly variants | `messaging.delivery_attempts` | Merge | One logical attempt table; no period-specific physical tables. |
| send error logs / `log_error_kirim` | attempts + delivery events | Merge | Failure belongs to attempt/event lifecycle. |
| `read_email` | `messaging.delivery_events` | Replace | Convert to `opened` event. |
| `bounce_inbox` | delivery event + suppression | Replace | Bounce is provider feedback; permanent consequence may create suppression. |
| suppression variants/derived lists | `messaging.suppressions` | Normalize | Explicit reason/source/permanence/expiry. |
| sample/test send flag | `deliveries.delivery_type` | Normalize | Explicit production/sample/test execution type. |
| runtime `vw_<timestamp>` views | Query/report layer | Discard | Replace dynamic DDL with parameterized queries/reporting. |
| `vw_email`, `vw_email_bayar`, `vw_email_gratis` | contacts/deliveries reporting | Replace | UNION over email1/2/3 becomes normalized contacts and canonical delivery reporting. |
| `mst_produk` projection | `core.products` + app config | Replace | Remove hard-coded view metadata from canonical DB model. |
| legacy mutable summary/report counters | canonical queries / future warehouse | Do not migrate as truth | Recompute from canonical lifecycle; ClickHouse/materialized reporting may be introduced separately. |
| PostgreSQL extension/internal dump objects | none unless required | Discard | Infrastructure/internal objects are not application-domain schema. |

## ETL ordering

```text
1. core.products
2. core.users / roles / permissions
3. core.attachments / settings
4. import.customer_batches / files
5. import.customers / contacts
6. import.statement_batches / files
7. import.statements
8. pdf.documents
9. messaging.mail_servers / templates / test_recipients / schedules
10. messaging.deliveries / recipients / attachments
11. messaging.delivery_attempts / delivery_events / suppressions
12. audit and reconciliation
```

## Reconciliation requirements

For each migrated period/product, ETL must reconcile at minimum:
- source vs target customer count;
- source vs target statement/account count;
- normalized contact counts and addresses;
- PDF artifact count and page totals where available;
- queued/sent/failed/sample counts;
- read/open and bounce counts;
- attachment associations;
- unmapped/invalid rows with explicit quarantine/error evidence.

No legacy source row should disappear silently. A row is either migrated, transformed/merged with traceability, intentionally discarded by a documented rule, or quarantined with a reason.
