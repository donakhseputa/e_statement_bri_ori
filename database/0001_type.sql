-- ----------------------------
-- Type structure for bt_metap_type
-- ----------------------------
DROP TYPE IF EXISTS "public"."bt_metap_type";
CREATE TYPE "public"."bt_metap_type" AS (
  "magic" int4,
  "version" int4,
  "root" int4,
  "level" int4,
  "fastroot" int4,
  "fastlevel" int4
);

-- ----------------------------
-- Type structure for bt_page_items_type
-- ----------------------------
DROP TYPE IF EXISTS "public"."bt_page_items_type";
CREATE TYPE "public"."bt_page_items_type" AS (
  "itemoffset" int4,
  "ctid" tid,
  "itemlen" int4,
  "nulls" bool,
  "vars" bool,
  "data" text COLLATE "pg_catalog"."default"
);

-- ----------------------------
-- Type structure for bt_page_stats_type
-- ----------------------------
DROP TYPE IF EXISTS "public"."bt_page_stats_type";
CREATE TYPE "public"."bt_page_stats_type" AS (
  "blkno" int4,
  "type" char(1) COLLATE "pg_catalog"."default",
  "live_items" int4,
  "dead_items" int4,
  "avg_item_size" float8,
  "page_size" int4,
  "free_size" int4,
  "btpo_prev" int4,
  "btpo_next" int4,
  "btpo" int4,
  "btpo_flags" int4
);

-- ----------------------------
-- Type structure for chkpass
-- ----------------------------
DROP TYPE IF EXISTS "public"."chkpass";
CREATE TYPE "public"."chkpass";

-- ----------------------------
-- Type structure for dblink_pkey_results
-- ----------------------------
DROP TYPE IF EXISTS "public"."dblink_pkey_results";
CREATE TYPE "public"."dblink_pkey_results" AS (
  "position" int4,
  "colname" text COLLATE "pg_catalog"."default"
);

-- ----------------------------
-- Type structure for intbig_gkey
-- ----------------------------
DROP TYPE IF EXISTS "public"."intbig_gkey";
CREATE TYPE "public"."intbig_gkey" (
  INPUT = "public"."_intbig_in",
  OUTPUT = "public"."_intbig_out",
  INTERNALLENGTH = VARIABLE,
  CATEGORY = u,
  DELIMITER = ','
);

-- ----------------------------
-- Type structure for pgstatindex_type
-- ----------------------------
DROP TYPE IF EXISTS "public"."pgstatindex_type";
CREATE TYPE "public"."pgstatindex_type" AS (
  "version" int4,
  "tree_level" int4,
  "index_size" int4,
  "root_block_no" int4,
  "internal_pages" int4,
  "leaf_pages" int4,
  "empty_pages" int4,
  "deleted_pages" int4,
  "avg_leaf_density" float8,
  "leaf_fragmentation" float8
);

-- ----------------------------
-- Type structure for pgstattuple_type
-- ----------------------------
DROP TYPE IF EXISTS "public"."pgstattuple_type";
CREATE TYPE "public"."pgstattuple_type" AS (
  "table_len" int8,
  "tuple_count" int8,
  "tuple_len" int8,
  "tuple_percent" float8,
  "dead_tuple_count" int8,
  "dead_tuple_len" int8,
  "dead_tuple_percent" float8,
  "free_space" int8,
  "free_percent" float8
);

-- ----------------------------
-- Type structure for query_int
-- ----------------------------
DROP TYPE IF EXISTS "public"."query_int";
CREATE TYPE "public"."query_int" (
  INPUT = "public"."bqarr_in",
  OUTPUT = "public"."bqarr_out",
  INTERNALLENGTH = VARIABLE,
  CATEGORY = u,
  DELIMITER = ','
);

-- ----------------------------
-- Type structure for tablefunc_crosstab_2
-- ----------------------------
DROP TYPE IF EXISTS "public"."tablefunc_crosstab_2";
CREATE TYPE "public"."tablefunc_crosstab_2" AS (
  "row_name" text COLLATE "pg_catalog"."default",
  "category_1" text COLLATE "pg_catalog"."default",
  "category_2" text COLLATE "pg_catalog"."default"
);

-- ----------------------------
-- Type structure for tablefunc_crosstab_3
-- ----------------------------
DROP TYPE IF EXISTS "public"."tablefunc_crosstab_3";
CREATE TYPE "public"."tablefunc_crosstab_3" AS (
  "row_name" text COLLATE "pg_catalog"."default",
  "category_1" text COLLATE "pg_catalog"."default",
  "category_2" text COLLATE "pg_catalog"."default",
  "category_3" text COLLATE "pg_catalog"."default"
);

-- ----------------------------
-- Type structure for tablefunc_crosstab_4
-- ----------------------------
DROP TYPE IF EXISTS "public"."tablefunc_crosstab_4";
CREATE TYPE "public"."tablefunc_crosstab_4" AS (
  "row_name" text COLLATE "pg_catalog"."default",
  "category_1" text COLLATE "pg_catalog"."default",
  "category_2" text COLLATE "pg_catalog"."default",
  "category_3" text COLLATE "pg_catalog"."default",
  "category_4" text COLLATE "pg_catalog"."default"
);
