# Database v2 migrations

PostgreSQL-native migrations for the refactored E-Statement BRI platform.

## Schemas

- `core`: shared master data, users/access control, attachments and audit logs.
- `import`: ingestion batches, source files, normalized statement snapshots and validation errors.
- `pdf`: generated documents and PDF processing lifecycle.
- `messaging`: SMTP configuration, templates, deliveries, recipients, attempts, events and suppression.

## Execution order

Run the numbered SQL files in ascending order. Each migration is transactional.

```bash
for file in database/migrations/v2/[0-9]*.sql; do
  psql "$DATABASE_URL" -v ON_ERROR_STOP=1 -f "$file"
done
```

These migrations intentionally do not modify or drop legacy tables. Legacy-to-v2 data migration will be implemented separately after the mapping is validated.

## Design rules

- Tables are plural and `snake_case`.
- Columns are English and `snake_case`.
- Primary keys use `id`; foreign keys use `<entity>_id`.
- Time values use `TIMESTAMPTZ`.
- Statement periods use the first day of a month as a `DATE`.
- Object storage references use `storage_key`; local absolute file paths are not part of the v2 contract.
- Customer data in `import.records` is a historical snapshot and may intentionally duplicate current master data.
- `import.records` starts unpartitioned. Native monthly partitioning should be introduced only after representative-volume benchmarks justify it.
