# Supabase migration workspace

This directory is the PostgreSQL/Supabase target for the PROMEX migration.
It intentionally does not contain a mechanical translation of `install.sql`:
the source dump is MySQL-specific and must be converted and tested by domain.

## Rules

1. Use the `promex` schema, never `public`, for application tables.
2. Keep the Supabase service-role key and the database password out of source
   control and browser code.
3. Use `DB_CONNECTION=pgsql`, `DB_URL`, `DB_SCHEMA=promex` and
   `DB_SSLMODE=require` for a remote Supabase environment.
4. Do not enable a legacy game until it passes a game-session parity test.
5. Payments, crypto, deposits and withdrawals remain disabled during migration.

## Migration order

1. Create the empty `promex` schema with `001_create_application_schema.sql`.
2. Load the MySQL dump into an isolated source database, then run `pgloader`
   against PostgreSQL. pgloader names the imported schema after the MySQL
   source database; rename that schema to `promex` before continuing.
3. Apply `002_fix_numeric_foreign_keys.sql` to restore the ten constraints
   that pgloader cannot create because of the source DECIMAL types.
4. Run `php artisan migrate --force` with `DB_CONNECTION=pgsql`,
   `DB_SCHEMA=promex` and `DB_PREFIX=w_`.
5. Use `install.php` with **PostgreSQL / Supabase** selected to create the
   application environment and administrator. It never imports `install.sql`
   into PostgreSQL.
6. Convert and test one legacy-game family at a time.

Example schema rename after pgloader, where `promex_source` is the temporary
MySQL database name used for the import:

```sql
drop schema promex; -- succeeds only when it is empty
alter schema promex_source rename to promex;
```

Do not run the `drop schema` statement against an environment that already
contains application data.

## Local Laravel against Supabase

The remote project is configured with application tables in `promex`, with
`DB_PREFIX=w_`. To run the local Laravel container against it:

1. Copy `.env.supabase.example` to `.env.supabase` and add the Session Pooler
   host, database username and database password from the Supabase **Connect**
   dialog.
2. Run `start.bat`.
3. Visit `http://127.0.0.1:8081/`. Legacy games remain disabled during their
   security and PostgreSQL review.

Keep `DB_SSLMODE=require`. Do not place service-role keys or database passwords
in application source files.

`001_create_application_schema.sql` is safe to apply to an empty Supabase
project. Later numbered scripts must be reviewed and applied in order.
