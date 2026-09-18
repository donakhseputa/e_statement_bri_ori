-- ----------------------------
-- Function structure for _int_inter
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."_int_inter"(_int4, _int4);
CREATE FUNCTION "public"."_int_inter"(_int4, _int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', '_int_inter'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for _int_union
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."_int_union"(_int4, _int4);
CREATE FUNCTION "public"."_int_union"(_int4, _int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', '_int_union'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for _intbig_in
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."_intbig_in"(cstring);
CREATE FUNCTION "public"."_intbig_in"(cstring)
  RETURNS "public"."intbig_gkey" AS '$libdir/_int', '_intbig_in'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for _intbig_out
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."_intbig_out"("public"."intbig_gkey");
CREATE FUNCTION "public"."_intbig_out"("public"."intbig_gkey")
  RETURNS "pg_catalog"."cstring" AS '$libdir/_int', '_intbig_out'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for armor
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."armor"(bytea);
CREATE FUNCTION "public"."armor"(bytea)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pg_armor'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for bqarr_in
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."bqarr_in"(cstring);
CREATE FUNCTION "public"."bqarr_in"(cstring)
  RETURNS "public"."query_int" AS '$libdir/_int', 'bqarr_in'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for bqarr_out
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."bqarr_out"("public"."query_int");
CREATE FUNCTION "public"."bqarr_out"("public"."query_int")
  RETURNS "pg_catalog"."cstring" AS '$libdir/_int', 'bqarr_out'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for connectby
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."connectby"(text, text, text, text, text, int4, text);
CREATE FUNCTION "public"."connectby"(text, text, text, text, text, int4, text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'connectby_text_serial'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for connectby
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."connectby"(text, text, text, text, int4);
CREATE FUNCTION "public"."connectby"(text, text, text, text, int4)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'connectby_text'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for connectby
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."connectby"(text, text, text, text, int4, text);
CREATE FUNCTION "public"."connectby"(text, text, text, text, int4, text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'connectby_text'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for connectby
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."connectby"(text, text, text, text, text, int4);
CREATE FUNCTION "public"."connectby"(text, text, text, text, text, int4)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'connectby_text_serial'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab"(text, text);
CREATE FUNCTION "public"."crosstab"(text, text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'crosstab_hash'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab"(text);
CREATE FUNCTION "public"."crosstab"(text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'crosstab'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab"(text, int4);
CREATE FUNCTION "public"."crosstab"(text, int4)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/tablefunc', 'crosstab'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab2
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab2"(text);
CREATE FUNCTION "public"."crosstab2"(text)
  RETURNS SETOF "public"."tablefunc_crosstab_2" AS '$libdir/tablefunc', 'crosstab'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab3
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab3"(text);
CREATE FUNCTION "public"."crosstab3"(text)
  RETURNS SETOF "public"."tablefunc_crosstab_3" AS '$libdir/tablefunc', 'crosstab'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crosstab4
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crosstab4"(text);
CREATE FUNCTION "public"."crosstab4"(text)
  RETURNS SETOF "public"."tablefunc_crosstab_4" AS '$libdir/tablefunc', 'crosstab'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for crypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."crypt"(text, text);
CREATE FUNCTION "public"."crypt"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pg_crypt'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink"(text);
CREATE FUNCTION "public"."dblink"(text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_record'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink"(text, text);
CREATE FUNCTION "public"."dblink"(text, text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_record'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink"(text, bool);
CREATE FUNCTION "public"."dblink"(text, bool)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_record'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink"(text, text, bool);
CREATE FUNCTION "public"."dblink"(text, text, bool)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_record'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_build_sql_delete
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_build_sql_delete"(text, int2vector, int4, _text);
CREATE FUNCTION "public"."dblink_build_sql_delete"(text, int2vector, int4, _text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_build_sql_delete'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_build_sql_insert
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_build_sql_insert"(text, int2vector, int4, _text, _text);
CREATE FUNCTION "public"."dblink_build_sql_insert"(text, int2vector, int4, _text, _text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_build_sql_insert'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_build_sql_update
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_build_sql_update"(text, int2vector, int4, _text, _text);
CREATE FUNCTION "public"."dblink_build_sql_update"(text, int2vector, int4, _text, _text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_build_sql_update'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_cancel_query
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_cancel_query"(text);
CREATE FUNCTION "public"."dblink_cancel_query"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_cancel_query'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_close
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_close"(text);
CREATE FUNCTION "public"."dblink_close"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_close'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_close
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_close"(text, bool);
CREATE FUNCTION "public"."dblink_close"(text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_close'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_close
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_close"(text, text);
CREATE FUNCTION "public"."dblink_close"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_close'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_close
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_close"(text, text, bool);
CREATE FUNCTION "public"."dblink_close"(text, text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_close'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_connect
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_connect"(text);
CREATE FUNCTION "public"."dblink_connect"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_connect'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_connect
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_connect"(text, text);
CREATE FUNCTION "public"."dblink_connect"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_connect'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_current_query
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_current_query"();
CREATE FUNCTION "public"."dblink_current_query"()
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_current_query'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for dblink_disconnect
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_disconnect"();
CREATE FUNCTION "public"."dblink_disconnect"()
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_disconnect'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_disconnect
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_disconnect"(text);
CREATE FUNCTION "public"."dblink_disconnect"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_disconnect'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_error_message
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_error_message"(text);
CREATE FUNCTION "public"."dblink_error_message"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_error_message'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_exec
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_exec"(text);
CREATE FUNCTION "public"."dblink_exec"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_exec'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_exec
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_exec"(text, text);
CREATE FUNCTION "public"."dblink_exec"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_exec'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_exec
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_exec"(text, bool);
CREATE FUNCTION "public"."dblink_exec"(text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_exec'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_exec
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_exec"(text, text, bool);
CREATE FUNCTION "public"."dblink_exec"(text, text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_exec'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_fetch
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_fetch"(text, int4);
CREATE FUNCTION "public"."dblink_fetch"(text, int4)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_fetch'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_fetch
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_fetch"(text, int4, bool);
CREATE FUNCTION "public"."dblink_fetch"(text, int4, bool)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_fetch'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_fetch
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_fetch"(text, text, int4);
CREATE FUNCTION "public"."dblink_fetch"(text, text, int4)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_fetch'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_fetch
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_fetch"(text, text, int4, bool);
CREATE FUNCTION "public"."dblink_fetch"(text, text, int4, bool)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_fetch'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_get_connections
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_get_connections"();
CREATE FUNCTION "public"."dblink_get_connections"()
  RETURNS "pg_catalog"."_text" AS '$libdir/dblink', 'dblink_get_connections'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for dblink_get_pkey
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_get_pkey"(text);
CREATE FUNCTION "public"."dblink_get_pkey"(text)
  RETURNS SETOF "public"."dblink_pkey_results" AS '$libdir/dblink', 'dblink_get_pkey'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_get_result
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_get_result"(text);
CREATE FUNCTION "public"."dblink_get_result"(text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_get_result'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_get_result
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_get_result"(text, bool);
CREATE FUNCTION "public"."dblink_get_result"(text, bool)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/dblink', 'dblink_get_result'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for dblink_is_busy
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_is_busy"(text);
CREATE FUNCTION "public"."dblink_is_busy"(text)
  RETURNS "pg_catalog"."int4" AS '$libdir/dblink', 'dblink_is_busy'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_open
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_open"(text, text);
CREATE FUNCTION "public"."dblink_open"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_open'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_open
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_open"(text, text, bool);
CREATE FUNCTION "public"."dblink_open"(text, text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_open'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_open
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_open"(text, text, text);
CREATE FUNCTION "public"."dblink_open"(text, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_open'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_open
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_open"(text, text, text, bool);
CREATE FUNCTION "public"."dblink_open"(text, text, text, bool)
  RETURNS "pg_catalog"."text" AS '$libdir/dblink', 'dblink_open'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dblink_send_query
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dblink_send_query"(text, text);
CREATE FUNCTION "public"."dblink_send_query"(text, text)
  RETURNS "pg_catalog"."int4" AS '$libdir/dblink', 'dblink_send_query'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for dearmor
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."dearmor"(text);
CREATE FUNCTION "public"."dearmor"(text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_dearmor'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."decrypt"(bytea, bytea, text);
CREATE FUNCTION "public"."decrypt"(bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_decrypt'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for decrypt_iv
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."decrypt_iv"(bytea, bytea, bytea, text);
CREATE FUNCTION "public"."decrypt_iv"(bytea, bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_decrypt_iv'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for digest
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."digest"(text, text);
CREATE FUNCTION "public"."digest"(text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_digest'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for digest
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."digest"(bytea, text);
CREATE FUNCTION "public"."digest"(bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_digest'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for encrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."encrypt"(bytea, bytea, text);
CREATE FUNCTION "public"."encrypt"(bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_encrypt'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for encrypt_iv
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."encrypt_iv"(bytea, bytea, bytea, text);
CREATE FUNCTION "public"."encrypt_iv"(bytea, bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_encrypt_iv'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for f_isvalidemail
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."f_isvalidemail"(text);
CREATE FUNCTION "public"."f_isvalidemail"(text)
  RETURNS "pg_catalog"."bool" AS $BODY$select $1 ~ '^[^@s]+@[^@s]+(.[^@s]+)+$' as result
$BODY$
  LANGUAGE sql VOLATILE
  COST 100;

-- ----------------------------
-- Function structure for g_int_compress
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_compress"(internal);
CREATE FUNCTION "public"."g_int_compress"(internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_int_compress'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_int_consistent
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_consistent"(internal, _int4, int4);
CREATE FUNCTION "public"."g_int_consistent"(internal, _int4, int4)
  RETURNS "pg_catalog"."bool" AS '$libdir/_int', 'g_int_consistent'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_int_decompress
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_decompress"(internal);
CREATE FUNCTION "public"."g_int_decompress"(internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_int_decompress'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_int_penalty
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_penalty"(internal, internal, internal);
CREATE FUNCTION "public"."g_int_penalty"(internal, internal, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_int_penalty'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for g_int_picksplit
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_picksplit"(internal, internal);
CREATE FUNCTION "public"."g_int_picksplit"(internal, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_int_picksplit'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_int_same
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_same"(_int4, _int4, internal);
CREATE FUNCTION "public"."g_int_same"(_int4, _int4, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_int_same'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_int_union
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_int_union"(internal, internal);
CREATE FUNCTION "public"."g_int_union"(internal, internal)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'g_int_union'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_compress
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_compress"(internal);
CREATE FUNCTION "public"."g_intbig_compress"(internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_intbig_compress'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_consistent
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_consistent"(internal, internal, int4);
CREATE FUNCTION "public"."g_intbig_consistent"(internal, internal, int4)
  RETURNS "pg_catalog"."bool" AS '$libdir/_int', 'g_intbig_consistent'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_decompress
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_decompress"(internal);
CREATE FUNCTION "public"."g_intbig_decompress"(internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_intbig_decompress'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_penalty
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_penalty"(internal, internal, internal);
CREATE FUNCTION "public"."g_intbig_penalty"(internal, internal, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_intbig_penalty'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_picksplit
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_picksplit"(internal, internal);
CREATE FUNCTION "public"."g_intbig_picksplit"(internal, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_intbig_picksplit'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_same
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_same"(internal, internal, internal);
CREATE FUNCTION "public"."g_intbig_same"(internal, internal, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'g_intbig_same'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for g_intbig_union
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."g_intbig_union"(internal, internal);
CREATE FUNCTION "public"."g_intbig_union"(internal, internal)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'g_intbig_union'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for gen_random_bytes
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."gen_random_bytes"(int4);
CREATE FUNCTION "public"."gen_random_bytes"(int4)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_random_bytes'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for gen_salt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."gen_salt"(text);
CREATE FUNCTION "public"."gen_salt"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pg_gen_salt'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for gen_salt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."gen_salt"(text, int4);
CREATE FUNCTION "public"."gen_salt"(text, int4)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pg_gen_salt_rounds'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for get_timetravel
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."get_timetravel"(name);
CREATE FUNCTION "public"."get_timetravel"(name)
  RETURNS "pg_catalog"."int4" AS '$libdir/timetravel', 'get_timetravel'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ginint4_consistent
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ginint4_consistent"(internal, int2, internal);
CREATE FUNCTION "public"."ginint4_consistent"(internal, int2, internal)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'ginint4_consistent'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for ginint4_queryextract
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ginint4_queryextract"(internal, internal, int2);
CREATE FUNCTION "public"."ginint4_queryextract"(internal, internal, int2)
  RETURNS "pg_catalog"."internal" AS '$libdir/_int', 'ginint4_queryextract'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for hmac
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."hmac"(text, text, text);
CREATE FUNCTION "public"."hmac"(text, text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_hmac'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for hmac
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."hmac"(bytea, bytea, text);
CREATE FUNCTION "public"."hmac"(bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pg_hmac'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for icount
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."icount"(_int4);
CREATE FUNCTION "public"."icount"(_int4)
  RETURNS "pg_catalog"."int4" AS '$libdir/_int', 'icount'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for idx
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."idx"(_int4, int4);
CREATE FUNCTION "public"."idx"(_int4, int4)
  RETURNS "pg_catalog"."int4" AS '$libdir/_int', 'idx'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for insert_username
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."insert_username"();
CREATE FUNCTION "public"."insert_username"()
  RETURNS "pg_catalog"."trigger" AS '$libdir/insert_username', 'insert_username'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for intarray_del_elem
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intarray_del_elem"(_int4, int4);
CREATE FUNCTION "public"."intarray_del_elem"(_int4, int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intarray_del_elem'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for intarray_push_array
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intarray_push_array"(_int4, _int4);
CREATE FUNCTION "public"."intarray_push_array"(_int4, _int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intarray_push_array'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for intarray_push_elem
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intarray_push_elem"(_int4, int4);
CREATE FUNCTION "public"."intarray_push_elem"(_int4, int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intarray_push_elem'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for intset
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intset"(int4);
CREATE FUNCTION "public"."intset"(int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intset'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for intset_subtract
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intset_subtract"(_int4, _int4);
CREATE FUNCTION "public"."intset_subtract"(_int4, _int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intset_subtract'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for intset_union_elem
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."intset_union_elem"(_int4, int4);
CREATE FUNCTION "public"."intset_union_elem"(_int4, int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'intset_union_elem'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for lo_manage
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."lo_manage"();
CREATE FUNCTION "public"."lo_manage"()
  RETURNS "pg_catalog"."trigger" AS '$libdir/lo', 'lo_manage'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for moddatetime
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."moddatetime"();
CREATE FUNCTION "public"."moddatetime"()
  RETURNS "pg_catalog"."trigger" AS '$libdir/moddatetime', 'moddatetime'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for normal_rand
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."normal_rand"(int4, float8, float8);
CREATE FUNCTION "public"."normal_rand"(int4, float8, float8)
  RETURNS SETOF "pg_catalog"."float8" AS '$libdir/tablefunc', 'normal_rand'
  LANGUAGE c VOLATILE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for pg_relpages
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pg_relpages"(text);
CREATE FUNCTION "public"."pg_relpages"(text)
  RETURNS "pg_catalog"."int4" AS '$libdir/pgstattuple', 'pg_relpages'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_key_id
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_key_id"(bytea);
CREATE FUNCTION "public"."pgp_key_id"(bytea)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_key_id_w'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt"(bytea, bytea);
CREATE FUNCTION "public"."pgp_pub_decrypt"(bytea, bytea)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_text'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt"(bytea, bytea, text);
CREATE FUNCTION "public"."pgp_pub_decrypt"(bytea, bytea, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_text'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt"(bytea, bytea, text, text);
CREATE FUNCTION "public"."pgp_pub_decrypt"(bytea, bytea, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_text'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt_bytea"(bytea, bytea);
CREATE FUNCTION "public"."pgp_pub_decrypt_bytea"(bytea, bytea)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_bytea'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt_bytea"(bytea, bytea, text);
CREATE FUNCTION "public"."pgp_pub_decrypt_bytea"(bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_bytea'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_decrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_decrypt_bytea"(bytea, bytea, text, text);
CREATE FUNCTION "public"."pgp_pub_decrypt_bytea"(bytea, bytea, text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_decrypt_bytea'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_encrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_encrypt"(text, bytea);
CREATE FUNCTION "public"."pgp_pub_encrypt"(text, bytea)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_encrypt_text'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_encrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_encrypt"(text, bytea, text);
CREATE FUNCTION "public"."pgp_pub_encrypt"(text, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_encrypt_text'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_encrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_encrypt_bytea"(bytea, bytea);
CREATE FUNCTION "public"."pgp_pub_encrypt_bytea"(bytea, bytea)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_encrypt_bytea'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_pub_encrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_pub_encrypt_bytea"(bytea, bytea, text);
CREATE FUNCTION "public"."pgp_pub_encrypt_bytea"(bytea, bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_pub_encrypt_bytea'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_decrypt"(bytea, text);
CREATE FUNCTION "public"."pgp_sym_decrypt"(bytea, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_sym_decrypt_text'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_decrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_decrypt"(bytea, text, text);
CREATE FUNCTION "public"."pgp_sym_decrypt"(bytea, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgcrypto', 'pgp_sym_decrypt_text'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_decrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_decrypt_bytea"(bytea, text);
CREATE FUNCTION "public"."pgp_sym_decrypt_bytea"(bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_decrypt_bytea'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_decrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_decrypt_bytea"(bytea, text, text);
CREATE FUNCTION "public"."pgp_sym_decrypt_bytea"(bytea, text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_decrypt_bytea'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_encrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_encrypt"(text, text, text);
CREATE FUNCTION "public"."pgp_sym_encrypt"(text, text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_encrypt_text'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_encrypt
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_encrypt"(text, text);
CREATE FUNCTION "public"."pgp_sym_encrypt"(text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_encrypt_text'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_encrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_encrypt_bytea"(bytea, text);
CREATE FUNCTION "public"."pgp_sym_encrypt_bytea"(bytea, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_encrypt_bytea'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgp_sym_encrypt_bytea
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgp_sym_encrypt_bytea"(bytea, text, text);
CREATE FUNCTION "public"."pgp_sym_encrypt_bytea"(bytea, text, text)
  RETURNS "pg_catalog"."bytea" AS '$libdir/pgcrypto', 'pgp_sym_encrypt_bytea'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgstatindex
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgstatindex"(text);
CREATE FUNCTION "public"."pgstatindex"(text)
  RETURNS "public"."pgstatindex_type" AS '$libdir/pgstattuple', 'pgstatindex'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgstattuple
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgstattuple"(text);
CREATE FUNCTION "public"."pgstattuple"(text)
  RETURNS "public"."pgstattuple_type" AS '$libdir/pgstattuple', 'pgstattuple'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for pgstattuple
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."pgstattuple"(oid);
CREATE FUNCTION "public"."pgstattuple"(oid)
  RETURNS "public"."pgstattuple_type" AS '$libdir/pgstattuple', 'pgstattuplebyid'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for querytree
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."querytree"("public"."query_int");
CREATE FUNCTION "public"."querytree"("public"."query_int")
  RETURNS "pg_catalog"."text" AS '$libdir/_int', 'querytree'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for set_timetravel
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."set_timetravel"(name, int4);
CREATE FUNCTION "public"."set_timetravel"(name, int4)
  RETURNS "pg_catalog"."int4" AS '$libdir/timetravel', 'set_timetravel'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for sort
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."sort"(_int4);
CREATE FUNCTION "public"."sort"(_int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'sort'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for sort
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."sort"(_int4, text);
CREATE FUNCTION "public"."sort"(_int4, text)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'sort'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for sort_asc
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."sort_asc"(_int4);
CREATE FUNCTION "public"."sort_asc"(_int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'sort_asc'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for sort_desc
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."sort_desc"(_int4);
CREATE FUNCTION "public"."sort_desc"(_int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'sort_desc'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_client_cert_present
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_client_cert_present"();
CREATE FUNCTION "public"."ssl_client_cert_present"()
  RETURNS "pg_catalog"."bool" AS '$libdir/sslinfo', 'ssl_client_cert_present'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_client_dn
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_client_dn"();
CREATE FUNCTION "public"."ssl_client_dn"()
  RETURNS "pg_catalog"."text" AS '$libdir/sslinfo', 'ssl_client_dn'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_client_dn_field
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_client_dn_field"(text);
CREATE FUNCTION "public"."ssl_client_dn_field"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/sslinfo', 'ssl_client_dn_field'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_client_serial
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_client_serial"();
CREATE FUNCTION "public"."ssl_client_serial"()
  RETURNS "pg_catalog"."numeric" AS '$libdir/sslinfo', 'ssl_client_serial'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_is_used
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_is_used"();
CREATE FUNCTION "public"."ssl_is_used"()
  RETURNS "pg_catalog"."bool" AS '$libdir/sslinfo', 'ssl_is_used'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_issuer_dn
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_issuer_dn"();
CREATE FUNCTION "public"."ssl_issuer_dn"()
  RETURNS "pg_catalog"."text" AS '$libdir/sslinfo', 'ssl_issuer_dn'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for ssl_issuer_field
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."ssl_issuer_field"(text);
CREATE FUNCTION "public"."ssl_issuer_field"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/sslinfo', 'ssl_issuer_field'
  LANGUAGE c VOLATILE STRICT
  COST 1;

-- ----------------------------
-- Function structure for subarray
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."subarray"(_int4, int4);
CREATE FUNCTION "public"."subarray"(_int4, int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'subarray'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for subarray
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."subarray"(_int4, int4, int4);
CREATE FUNCTION "public"."subarray"(_int4, int4, int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'subarray'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for timetravel
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."timetravel"();
CREATE FUNCTION "public"."timetravel"()
  RETURNS "pg_catalog"."trigger" AS '$libdir/timetravel', 'timetravel'
  LANGUAGE c VOLATILE
  COST 1;

-- ----------------------------
-- Function structure for uniq
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."uniq"(_int4);
CREATE FUNCTION "public"."uniq"(_int4)
  RETURNS "pg_catalog"."_int4" AS '$libdir/_int', 'uniq'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xml_encode_special_chars
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xml_encode_special_chars"(text);
CREATE FUNCTION "public"."xml_encode_special_chars"(text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xml_encode_special_chars'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xml_is_well_formed
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xml_is_well_formed"(text);
CREATE FUNCTION "public"."xml_is_well_formed"(text)
  RETURNS "pg_catalog"."bool" AS '$libdir/pgxml', 'xml_is_well_formed'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xml_valid
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xml_valid"(text);
CREATE FUNCTION "public"."xml_valid"(text)
  RETURNS "pg_catalog"."bool" AS '$libdir/pgxml', 'xml_is_well_formed'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_bool
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_bool"(text, text);
CREATE FUNCTION "public"."xpath_bool"(text, text)
  RETURNS "pg_catalog"."bool" AS '$libdir/pgxml', 'xpath_bool'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_list
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_list"(text, text, text);
CREATE FUNCTION "public"."xpath_list"(text, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xpath_list'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_list
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_list"(text, text);
CREATE FUNCTION "public"."xpath_list"(text, text)
  RETURNS "pg_catalog"."text" AS $BODY$SELECT xpath_list($1,$2,',')$BODY$
  LANGUAGE sql IMMUTABLE STRICT
  COST 100;

-- ----------------------------
-- Function structure for xpath_nodeset
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_nodeset"(text, text, text);
CREATE FUNCTION "public"."xpath_nodeset"(text, text, text)
  RETURNS "pg_catalog"."text" AS $BODY$SELECT xpath_nodeset($1,$2,'',$3)$BODY$
  LANGUAGE sql IMMUTABLE STRICT
  COST 100;

-- ----------------------------
-- Function structure for xpath_nodeset
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_nodeset"(text, text, text, text);
CREATE FUNCTION "public"."xpath_nodeset"(text, text, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xpath_nodeset'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_nodeset
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_nodeset"(text, text);
CREATE FUNCTION "public"."xpath_nodeset"(text, text)
  RETURNS "pg_catalog"."text" AS $BODY$SELECT xpath_nodeset($1,$2,'','')$BODY$
  LANGUAGE sql IMMUTABLE STRICT
  COST 100;

-- ----------------------------
-- Function structure for xpath_number
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_number"(text, text);
CREATE FUNCTION "public"."xpath_number"(text, text)
  RETURNS "pg_catalog"."float4" AS '$libdir/pgxml', 'xpath_number'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_string
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_string"(text, text);
CREATE FUNCTION "public"."xpath_string"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xpath_string'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xpath_table
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xpath_table"(text, text, text, text, text);
CREATE FUNCTION "public"."xpath_table"(text, text, text, text, text)
  RETURNS SETOF "pg_catalog"."record" AS '$libdir/pgxml', 'xpath_table'
  LANGUAGE c STABLE STRICT
  COST 1
  ROWS 1000;

-- ----------------------------
-- Function structure for xslt_process
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xslt_process"(text, text);
CREATE FUNCTION "public"."xslt_process"(text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xslt_process'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

-- ----------------------------
-- Function structure for xslt_process
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."xslt_process"(text, text, text);
CREATE FUNCTION "public"."xslt_process"(text, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xslt_process'
  LANGUAGE c VOLATILE STRICT
  COST 1;
