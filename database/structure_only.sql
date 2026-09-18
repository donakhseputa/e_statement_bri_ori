-- ============================================================
-- Reordered PostgreSQL 9 import
-- Fixes circular dependencies:
--   intbig_gkey <-> _intbig_in/_intbig_out
--   query_int   <-> bqarr_in/bqarr_out
-- ============================================================

-- Step 1: create shell types required by C function signatures
DROP TYPE IF EXISTS "public"."intbig_gkey";
CREATE TYPE "public"."intbig_gkey";

DROP TYPE IF EXISTS "public"."query_int";
CREATE TYPE "public"."query_int";

-- Step 2: create other types/sequences that functions may reference
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

-- ----------------------------
-- Sequence structure for _log_baca__read_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."_log_baca__read_email_id_seq";
CREATE SEQUENCE "public"."_log_baca__read_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for _suppression_list_aws_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."_suppression_list_aws_id_seq";
CREATE SEQUENCE "public"."_suppression_list_aws_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for _tr_email_log_bulan_lalu_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."_tr_email_log_bulan_lalu_id_seq";
CREATE SEQUENCE "public"."_tr_email_log_bulan_lalu_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for _urutan_no_pdf_ematerai_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."_urutan_no_pdf_ematerai_id_seq";
CREATE SEQUENCE "public"."_urutan_no_pdf_ematerai_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for access1_accessid_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."access1_accessid_seq";
CREATE SEQUENCE "public"."access1_accessid_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for antrian_email_antrian_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."antrian_email_antrian_id_seq";
CREATE SEQUENCE "public"."antrian_email_antrian_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for antrian_email_attach_file_antrian_email_attach_file_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."antrian_email_attach_file_antrian_email_attach_file_id_seq";
CREATE SEQUENCE "public"."antrian_email_attach_file_antrian_email_attach_file_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq";
CREATE SEQUENCE "public"."antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for antrian_email_history_antrian_history_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."antrian_email_history_antrian_history_id_seq";
CREATE SEQUENCE "public"."antrian_email_history_antrian_history_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for attach_selektif_attach_selektif_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."attach_selektif_attach_selektif_id_seq";
CREATE SEQUENCE "public"."attach_selektif_attach_selektif_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for autofeedback_history_autofeedback_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."autofeedback_history_autofeedback_id_seq";
CREATE SEQUENCE "public"."autofeedback_history_autofeedback_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for bounce_inbox_bounce_inbox_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."bounce_inbox_bounce_inbox_id_seq";
CREATE SEQUENCE "public"."bounce_inbox_bounce_inbox_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for code_email_code_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."code_email_code_email_id_seq";
CREATE SEQUENCE "public"."code_email_code_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_032025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_032025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_032025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_032025_co_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_032025_co_detail_id_seq";
CREATE SEQUENCE "public"."detail_032025_co_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_032025_mt_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_032025_mt_detail_id_seq";
CREATE SEQUENCE "public"."detail_032025_mt_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_052025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_052025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_052025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_052025_co_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_052025_co_detail_id_seq";
CREATE SEQUENCE "public"."detail_052025_co_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_072025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_072025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_072025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_082025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_082025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_082025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_102025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_102025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_102025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_102025_co_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_102025_co_detail_id_seq";
CREATE SEQUENCE "public"."detail_102025_co_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_102025_mt_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_102025_mt_detail_id_seq";
CREATE SEQUENCE "public"."detail_102025_mt_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_112025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_112025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_112025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_122025_bc_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_122025_bc_detail_id_seq";
CREATE SEQUENCE "public"."detail_122025_bc_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_cetak_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_cetak_detail_id_seq";
CREATE SEQUENCE "public"."detail_cetak_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for detail_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."detail_detail_id_seq";
CREATE SEQUENCE "public"."detail_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for jobs_loading_detail_non_ematerai_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."jobs_loading_detail_non_ematerai_id_seq";
CREATE SEQUENCE "public"."jobs_loading_detail_non_ematerai_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for log_approval_log_approval_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."log_approval_log_approval_id_seq";
CREATE SEQUENCE "public"."log_approval_log_approval_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for log_customer_ku_log_customer_ku_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."log_customer_ku_log_customer_ku_id_seq";
CREATE SEQUENCE "public"."log_customer_ku_log_customer_ku_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for log_customer_log_customer_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."log_customer_log_customer_id_seq";
CREATE SEQUENCE "public"."log_customer_log_customer_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for log_error_kirim_log_error_kirim_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."log_error_kirim_log_error_kirim_id_seq";
CREATE SEQUENCE "public"."log_error_kirim_log_error_kirim_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for log_temp_excel_pk_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."log_temp_excel_pk_id_seq";
CREATE SEQUENCE "public"."log_temp_excel_pk_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_attach_file_m_attach_file_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_attach_file_m_attach_file_id_seq";
CREATE SEQUENCE "public"."m_attach_file_m_attach_file_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_customer_history_m_customer_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_customer_history_m_customer_id_seq";
CREATE SEQUENCE "public"."m_customer_history_m_customer_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_customer_ku_m_customer_ku_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_customer_ku_m_customer_ku_id_seq";
CREATE SEQUENCE "public"."m_customer_ku_m_customer_ku_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_customer_m_customer_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_customer_m_customer_id_seq";
CREATE SEQUENCE "public"."m_customer_m_customer_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_customer_x_m_customer_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_customer_x_m_customer_id_seq";
CREATE SEQUENCE "public"."m_customer_x_m_customer_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_jadwal_jadwal_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_jadwal_jadwal_id_seq";
CREATE SEQUENCE "public"."m_jadwal_jadwal_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for m_loading_loading_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."m_loading_loading_id_seq";
CREATE SEQUENCE "public"."m_loading_loading_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for mail_server_mail_server_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."mail_server_mail_server_id_seq";
CREATE SEQUENCE "public"."mail_server_mail_server_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_hana_bank_id_master_hana_bank_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_hana_bank_id_master_hana_bank_seq";
CREATE SEQUENCE "public"."master_hana_bank_id_master_hana_bank_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_kurir_id_master_kurir_seq1
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_kurir_id_master_kurir_seq1";
CREATE SEQUENCE "public"."master_kurir_id_master_kurir_seq1" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_file_ce_from_bri_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_bri_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_bri_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_file_ce_from_produksi_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_file_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_file_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_file_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for master_proses_after_ematerai_produksi_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."master_proses_after_ematerai_produksi_id_seq";
CREATE SEQUENCE "public"."master_proses_after_ematerai_produksi_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for menu1_menuid_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."menu1_menuid_seq";
CREATE SEQUENCE "public"."menu1_menuid_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for menugroup1_menugroupid_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."menugroup1_menugroupid_seq";
CREATE SEQUENCE "public"."menugroup1_menugroupid_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for menugroup1_menugroupid_seq1
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."menugroup1_menugroupid_seq1";
CREATE SEQUENCE "public"."menugroup1_menugroupid_seq1" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for mkurir_id_mkurir_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."mkurir_id_mkurir_seq";
CREATE SEQUENCE "public"."mkurir_id_mkurir_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for pdf_jobs_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."pdf_jobs_id_seq";
CREATE SEQUENCE "public"."pdf_jobs_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for pdf_queue_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."pdf_queue_id_seq";
CREATE SEQUENCE "public"."pdf_queue_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for read_email_read_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."read_email_read_email_id_seq";
CREATE SEQUENCE "public"."read_email_read_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tampung_tampung_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tampung_tampung_id_seq";
CREATE SEQUENCE "public"."tampung_tampung_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for temp_excel_all_saved_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."temp_excel_all_saved_id_seq";
CREATE SEQUENCE "public"."temp_excel_all_saved_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for temp_excel_pk_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."temp_excel_pk_id_seq";
CREATE SEQUENCE "public"."temp_excel_pk_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for temp_excel_sl_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."temp_excel_sl_id_seq";
CREATE SEQUENCE "public"."temp_excel_sl_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for template_email_template_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."template_email_template_email_id_seq";
CREATE SEQUENCE "public"."template_email_template_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq";
CREATE SEQUENCE "public"."tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq";
CREATE SEQUENCE "public"."tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq";
CREATE SEQUENCE "public"."tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq";
CREATE SEQUENCE "public"."tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tmp_split_detail_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tmp_split_detail_id_seq";
CREATE SEQUENCE "public"."tmp_split_detail_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_032025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_032025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_032025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_032025_co_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_032025_co_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_032025_co_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_032025_mt_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_032025_mt_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_032025_mt_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_052025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_052025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_052025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_052025_co_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_052025_co_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_052025_co_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_072025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_072025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_072025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_082025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_082025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_082025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_102025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_102025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_102025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_102025_co_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_102025_co_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_102025_co_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_102025_mt_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_102025_mt_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_102025_mt_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_112025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_112025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_112025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_122025_bc_tr_email_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_122025_bc_tr_email_id_seq";
CREATE SEQUENCE "public"."tr_email_122025_bc_tr_email_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_history_tr_email_history_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_history_tr_email_history_id_seq";
CREATE SEQUENCE "public"."tr_email_history_tr_email_history_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tr_email_tr_email_id_seq1
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tr_email_tr_email_id_seq1";
CREATE SEQUENCE "public"."tr_email_tr_email_id_seq1" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ============================================================
-- Step 3: create functions
-- ============================================================

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
DROP FUNCTION IF EXISTS "public"."xpath_nodeset"(text, text, text, text);
CREATE FUNCTION "public"."xpath_nodeset"(text, text, text, text)
  RETURNS "pg_catalog"."text" AS '$libdir/pgxml', 'xpath_nodeset'
  LANGUAGE c IMMUTABLE STRICT
  COST 1;

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

-- ============================================================
-- Step 4: complete shell type definitions after I/O functions exist
-- ============================================================

-- ----------------------------
-- Type structure for intbig_gkey
-- ----------------------------
CREATE TYPE "public"."intbig_gkey" (
  INPUT = "public"."_intbig_in",
  OUTPUT = "public"."_intbig_out",
  INTERNALLENGTH = VARIABLE,
  CATEGORY = u,
  DELIMITER = ','
);

-- ----------------------------
-- Type structure for query_int
-- ----------------------------
CREATE TYPE "public"."query_int" (
  INPUT = "public"."bqarr_in",
  OUTPUT = "public"."bqarr_out",
  INTERNALLENGTH = VARIABLE,
  CATEGORY = u,
  DELIMITER = ','
);

-- ----------------------------
-- Table structure for _list_convert_emeterai
-- ----------------------------
DROP TABLE IF EXISTS "public"."_list_convert_emeterai";
CREATE TABLE "public"."_list_convert_emeterai" (
  "nomor_rekening" varchar(200) COLLATE "pg_catalog"."default",
  "status" bool
)
;
COMMENT ON COLUMN "public"."_list_convert_emeterai"."nomor_rekening" IS 'listing untuk convert PDF BRI EMETERAI';

-- ----------------------------
-- Table structure for _list_convert_excel
-- ----------------------------
DROP TABLE IF EXISTS "public"."_list_convert_excel";
CREATE TABLE "public"."_list_convert_excel" (
  "nomor_rekening" varchar(200) COLLATE "pg_catalog"."default"
)
;
COMMENT ON COLUMN "public"."_list_convert_excel"."nomor_rekening" IS 'listing untuk convert PDF BRI CORP';

-- ----------------------------
-- Table structure for _list_no_exist
-- ----------------------------
DROP TABLE IF EXISTS "public"."_list_no_exist";
CREATE TABLE "public"."_list_no_exist" (
  "nomor_rekening" varchar(200) COLLATE "pg_catalog"."default"
)
;
COMMENT ON COLUMN "public"."_list_no_exist"."nomor_rekening" IS 'listing untuk no_exist';

-- ----------------------------
-- Table structure for _list_queue_mail_server
-- ----------------------------
DROP TABLE IF EXISTS "public"."_list_queue_mail_server";
CREATE TABLE "public"."_list_queue_mail_server" (
  "lama_queue" varchar(200) COLLATE "pg_catalog"."default",
  "size_lampiran" varchar(200) COLLATE "pg_catalog"."default",
  "id_mail_server" varchar(200) COLLATE "pg_catalog"."default",
  "pengirim" varchar(500) COLLATE "pg_catalog"."default",
  "email_tujuan" varchar(500) COLLATE "pg_catalog"."default",
  "waktu" varchar(500) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for _list_script_pdftk
-- ----------------------------
DROP TABLE IF EXISTS "public"."_list_script_pdftk";
CREATE TABLE "public"."_list_script_pdftk" (
  "script_pdftk" varchar(800) COLLATE "pg_catalog"."default",
  "waktu" timestamp(6),
  "script_pdftk_lokal" varchar(800) COLLATE "pg_catalog"."default"
)
;
COMMENT ON COLUMN "public"."_list_script_pdftk"."script_pdftk" IS 'script untuk pdftk';

-- ----------------------------
-- Table structure for _log_baca_
-- ----------------------------
DROP TABLE IF EXISTS "public"."_log_baca_";
CREATE TABLE "public"."_log_baca_" (
  "read_email_id" int4 NOT NULL DEFAULT nextval('_log_baca__read_email_id_seq'::regclass),
  "tr_email_id" int4,
  "waktu_read_email" timestamp(6),
  "body_email" text COLLATE "pg_catalog"."default",
  "keterangan" varchar(50) COLLATE "pg_catalog"."default",
  "tr_email_id2" int8,
  "waktu_insert_data" timestamp(6),
  "tanggal2" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for _suppression_list_aws
-- ----------------------------
DROP TABLE IF EXISTS "public"."_suppression_list_aws";
CREATE TABLE "public"."_suppression_list_aws" (
  "id" int4 NOT NULL DEFAULT nextval('_suppression_list_aws_id_seq'::regclass),
  "email" text COLLATE "pg_catalog"."default",
  "keterangan" text COLLATE "pg_catalog"."default",
  "waktu" timestamp(6)
)
;

-- ----------------------------
-- Table structure for _tr_email_log_bulan_lalu
-- ----------------------------
DROP TABLE IF EXISTS "public"."_tr_email_log_bulan_lalu";
CREATE TABLE "public"."_tr_email_log_bulan_lalu" (
  "id" int4 NOT NULL DEFAULT nextval('_tr_email_log_bulan_lalu_id_seq'::regclass),
  "tr_email_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default",
  "waktu_update" timestamp(6)
)
;
COMMENT ON TABLE "public"."_tr_email_log_bulan_lalu" IS 'tabel berisi log email bulan lalu';

-- ----------------------------
-- Table structure for _urutan_no_pdf_ematerai
-- ----------------------------
DROP TABLE IF EXISTS "public"."_urutan_no_pdf_ematerai";
CREATE TABLE "public"."_urutan_no_pdf_ematerai" (
  "id" int4 NOT NULL DEFAULT nextval('_urutan_no_pdf_ematerai_id_seq'::regclass),
  "urutan" int4
)
;

-- ----------------------------
-- Table structure for access1
-- ----------------------------
DROP TABLE IF EXISTS "public"."access1";
CREATE TABLE "public"."access1" (
  "accessid" int4 NOT NULL DEFAULT nextval('access1_accessid_seq'::regclass),
  "access_name" varchar(15) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for antrian_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."antrian_email";
CREATE TABLE "public"."antrian_email" (
  "antrian_id" int4 NOT NULL DEFAULT nextval('antrian_email_antrian_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "pdf_location" varchar(100) COLLATE "pg_catalog"."default",
  "userid" varchar(20) COLLATE "pg_catalog"."default",
  "tgl_antrian" timestamp(6),
  "template_email_id" int4,
  "email" varchar(304) COLLATE "pg_catalog"."default",
  "loading_id" int4,
  "jadwal_id" int4,
  "status_sample" bool,
  "log_approval_id" int4,
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "error_info" text COLLATE "pg_catalog"."default",
  "tgl_error_info" timestamp(6),
  "tipe_gagal_rev1" text COLLATE "pg_catalog"."default",
  "prioritas_kirim" int4
)
;
COMMENT ON TABLE "public"."antrian_email" IS 'berisi data yang masih di antrian';

-- ----------------------------
-- Table structure for antrian_email_attach_file
-- ----------------------------
DROP TABLE IF EXISTS "public"."antrian_email_attach_file";
CREATE TABLE "public"."antrian_email_attach_file" (
  "antrian_email_attach_file_id" int4 NOT NULL DEFAULT nextval('antrian_email_attach_file_antrian_email_attach_file_id_seq'::regclass),
  "antrian_id" int4 NOT NULL,
  "m_attach_file_id" varchar(100) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for antrian_email_attach_pdf
-- ----------------------------
DROP TABLE IF EXISTS "public"."antrian_email_attach_pdf";
CREATE TABLE "public"."antrian_email_attach_pdf" (
  "antrian_email_attach_pdf_id" int4 NOT NULL DEFAULT nextval('antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq'::regclass),
  "antrian_id" int4 NOT NULL,
  "detail_id" varchar(100) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for antrian_email_history
-- ----------------------------
DROP TABLE IF EXISTS "public"."antrian_email_history";
CREATE TABLE "public"."antrian_email_history" (
  "antrian_history_id" int4 NOT NULL DEFAULT nextval('antrian_email_history_antrian_history_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "pdf_location" varchar(100) COLLATE "pg_catalog"."default",
  "userid" varchar(20) COLLATE "pg_catalog"."default",
  "tgl_antrian" timestamp(6),
  "template_email_id" int4,
  "user_modify" varchar(20) COLLATE "pg_catalog"."default",
  "tgl_modify" timestamp(6),
  "email" varchar(304) COLLATE "pg_catalog"."default",
  "loading_id" int4,
  "antrian_id" int4,
  "jadwal_id" int4,
  "status_sample" bool
)
;
COMMENT ON TABLE "public"."antrian_email_history" IS 'berisi data yang sudah dihapus dari antrian';

-- ----------------------------
-- Table structure for attach_selektif
-- ----------------------------
DROP TABLE IF EXISTS "public"."attach_selektif";
CREATE TABLE "public"."attach_selektif" (
  "attach_selektif_id" int4 NOT NULL DEFAULT nextval('attach_selektif_attach_selektif_id_seq'::regclass),
  "m_attach_file_id" int4,
  "m_loading_id" int4,
  "urutan_attach_file" int4
)
;

-- ----------------------------
-- Table structure for autofeedback_history
-- ----------------------------
DROP TABLE IF EXISTS "public"."autofeedback_history";
CREATE TABLE "public"."autofeedback_history" (
  "autofeedback_id" int4 NOT NULL DEFAULT nextval('autofeedback_history_autofeedback_id_seq'::regclass),
  "autofeedback_date" timestamp(6),
  "autofeedback_gagal" int4 DEFAULT 0,
  "autofeedback_sukses_img" int4 DEFAULT 0,
  "autofeedback_sukses" int4 DEFAULT 0,
  "autofeedback_others" int4 DEFAULT 0
)
;

-- ----------------------------
-- Table structure for bounce_inbox
-- ----------------------------
DROP TABLE IF EXISTS "public"."bounce_inbox";
CREATE TABLE "public"."bounce_inbox" (
  "bounce_inbox_id" int4 NOT NULL DEFAULT nextval('bounce_inbox_bounce_inbox_id_seq'::regclass),
  "body_bounce" text COLLATE "pg_catalog"."default",
  "date_bounce" timestamp(6),
  "header_bounce" varchar(200) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "tr_email_id" varchar(30) COLLATE "pg_catalog"."default",
  "email" varchar(60) COLLATE "pg_catalog"."default",
  "m_loading_id" int4,
  "status" varchar(10) COLLATE "pg_catalog"."default",
  "sample_status" bool
)
;
COMMENT ON TABLE "public"."bounce_inbox" IS 'tabel berisi feedback email';

-- ----------------------------
-- Table structure for cek_nolbyte
-- ----------------------------
DROP TABLE IF EXISTS "public"."cek_nolbyte";
CREATE TABLE "public"."cek_nolbyte" (
  "namafile_bc" varchar(255) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(255) COLLATE "pg_catalog"."default",
  "namafile_mt" varchar(255) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for cek_nolbyte_copy1
-- ----------------------------
DROP TABLE IF EXISTS "public"."cek_nolbyte_copy1";
CREATE TABLE "public"."cek_nolbyte_copy1" (
  "namafile_bc" varchar(255) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(255) COLLATE "pg_catalog"."default",
  "namafile_mt" varchar(255) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for cek_pdf
-- ----------------------------
DROP TABLE IF EXISTS "public"."cek_pdf";
CREATE TABLE "public"."cek_pdf" (
  "cif_cek" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for cek_pdf_new
-- ----------------------------
DROP TABLE IF EXISTS "public"."cek_pdf_new";
CREATE TABLE "public"."cek_pdf_new" (
  "nomor_rekening" varchar(255) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(255) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(255) COLLATE "pg_catalog"."default",
  "new_pdf" varchar(255) COLLATE "pg_catalog"."default",
  "nama_file" varchar(255) COLLATE "pg_catalog"."default",
  "nama_pdf_norek" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for code_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."code_email";
CREATE TABLE "public"."code_email" (
  "code_email_id" int4 NOT NULL DEFAULT nextval('code_email_code_email_id_seq'::regclass),
  "code_name" varchar(20) COLLATE "pg_catalog"."default",
  "code_email" varchar(20) COLLATE "pg_catalog"."default",
  "code_php" varchar(50) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "status" bool
)
;
COMMENT ON TABLE "public"."code_email" IS 'master untuk replace email';

-- ----------------------------
-- Table structure for customer_online
-- ----------------------------
DROP TABLE IF EXISTS "public"."customer_online";
CREATE TABLE "public"."customer_online" (
  "customerid" int4 NOT NULL,
  "kodeproduk" varchar(12) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_cycle" varchar(50) COLLATE "pg_catalog"."default",
  "cus_name" varchar(100) COLLATE "pg_catalog"."default",
  "barcode" varchar(25) COLLATE "pg_catalog"."default",
  "cardno" varchar(25) COLLATE "pg_catalog"."default",
  "zip_code" varchar(6) COLLATE "pg_catalog"."default",
  "flag_status" varchar(1) COLLATE "pg_catalog"."default",
  "insert_date" timestamp(6),
  "email" varchar(100) COLLATE "pg_catalog"."default",
  "cardno2" varchar(25) COLLATE "pg_catalog"."default",
  "kode_gabungan" varchar(25) COLLATE "pg_catalog"."default",
  "kolektibilitas" varchar(1) COLLATE "pg_catalog"."default",
  "userid" varchar(30) COLLATE "pg_catalog"."default",
  "flag_update" bool DEFAULT false,
  "flag_update_date" timestamp(6),
  "flag_approve_date" timestamp(6),
  "flag_denied_date" timestamp(6)
)
;

-- ----------------------------
-- Table structure for detail
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail";
CREATE TABLE "public"."detail" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(30) COLLATE "pg_catalog"."default",
  "alamat2" varchar(30) COLLATE "pg_catalog"."default",
  "alamat3" varchar(30) COLLATE "pg_catalog"."default",
  "city" varchar(30) COLLATE "pg_catalog"."default",
  "zipcode" varchar(5) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "cabang" varchar(30) COLLATE "pg_catalog"."default",
  "email" varchar(100) COLLATE "pg_catalog"."default",
  "n_email" int4,
  "barcode" varchar(60) COLLATE "pg_catalog"."default",
  "alamat4" varchar(40) COLLATE "pg_catalog"."default",
  "alamat5" varchar(40) COLLATE "pg_catalog"."default",
  "propinsi" varchar(60) COLLATE "pg_catalog"."default",
  "area" varchar(50) COLLATE "pg_catalog"."default",
  "salutation" varchar(20) COLLATE "pg_catalog"."default",
  "nama_depan" varchar(60) COLLATE "pg_catalog"."default",
  "nama_belakang" varchar(60) COLLATE "pg_catalog"."default",
  "tgl_surat" varchar(60) COLLATE "pg_catalog"."default",
  "no_urut1" int4,
  "no_urut2" varchar(4) COLLATE "pg_catalog"."default",
  "trx_amount" varchar(20) COLLATE "pg_catalog"."default",
  "trx_date" varchar(30) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(100) COLLATE "pg_catalog"."default",
  "size_pdf" numeric,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default"
)
;
COMMENT ON TABLE "public"."detail" IS 'detail customer';

-- ----------------------------
-- Table structure for detail_032025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_032025_bc";
CREATE TABLE "public"."detail_032025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_032025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_032025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_032025_co";
CREATE TABLE "public"."detail_032025_co" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_032025_co_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(200) COLLATE "pg_catalog"."default",
  "alamat2" varchar(200) COLLATE "pg_catalog"."default",
  "alamat3" varchar(200) COLLATE "pg_catalog"."default",
  "alamat4" varchar(200) COLLATE "pg_catalog"."default",
  "alamat5" varchar(200) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(10) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "kurir" varchar(15) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_032025_mt
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_032025_mt";
CREATE TABLE "public"."detail_032025_mt" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_032025_mt_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default",
  "password_admin_pdf" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_052025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_052025_bc";
CREATE TABLE "public"."detail_052025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_052025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_052025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_052025_co";
CREATE TABLE "public"."detail_052025_co" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_052025_co_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(200) COLLATE "pg_catalog"."default",
  "alamat2" varchar(200) COLLATE "pg_catalog"."default",
  "alamat3" varchar(200) COLLATE "pg_catalog"."default",
  "alamat4" varchar(200) COLLATE "pg_catalog"."default",
  "alamat5" varchar(200) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(10) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "kurir" varchar(15) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_072025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_072025_bc";
CREATE TABLE "public"."detail_072025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_072025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_082025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_082025_bc";
CREATE TABLE "public"."detail_082025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_082025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_102025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_102025_bc";
CREATE TABLE "public"."detail_102025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_102025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_102025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_102025_co";
CREATE TABLE "public"."detail_102025_co" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_102025_co_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(200) COLLATE "pg_catalog"."default",
  "alamat2" varchar(200) COLLATE "pg_catalog"."default",
  "alamat3" varchar(200) COLLATE "pg_catalog"."default",
  "alamat4" varchar(200) COLLATE "pg_catalog"."default",
  "alamat5" varchar(200) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(10) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "kurir" varchar(15) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_102025_mt
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_102025_mt";
CREATE TABLE "public"."detail_102025_mt" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_102025_mt_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default",
  "password_admin_pdf" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_112025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_112025_bc";
CREATE TABLE "public"."detail_112025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_112025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_122025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_122025_bc";
CREATE TABLE "public"."detail_122025_bc" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_122025_bc_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(150) COLLATE "pg_catalog"."default",
  "alamat2" varchar(150) COLLATE "pg_catalog"."default",
  "alamat3" varchar(150) COLLATE "pg_catalog"."default",
  "alamat4" varchar(150) COLLATE "pg_catalog"."default",
  "alamat5" varchar(150) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(50) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default",
  "tipe_proses_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for detail_cetak
-- ----------------------------
DROP TABLE IF EXISTS "public"."detail_cetak";
CREATE TABLE "public"."detail_cetak" (
  "detail_id" int4 NOT NULL DEFAULT nextval('detail_cetak_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(30) COLLATE "pg_catalog"."default",
  "alamat2" varchar(30) COLLATE "pg_catalog"."default",
  "alamat3" varchar(30) COLLATE "pg_catalog"."default",
  "city" varchar(30) COLLATE "pg_catalog"."default",
  "zipcode" varchar(10) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "barcode" varchar(20) COLLATE "pg_catalog"."default",
  "kurir" varchar(15) COLLATE "pg_catalog"."default",
  "total_ematerai" text COLLATE "pg_catalog"."default",
  "jumlah_kartu_ematerai" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for doble_nolbyte
-- ----------------------------
DROP TABLE IF EXISTS "public"."doble_nolbyte";
CREATE TABLE "public"."doble_nolbyte" (
  "pdf_name" varchar(255) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for jobs_loading_detail_non_ematerai
-- ----------------------------
DROP TABLE IF EXISTS "public"."jobs_loading_detail_non_ematerai";
CREATE TABLE "public"."jobs_loading_detail_non_ematerai" (
  "id" int4 NOT NULL DEFAULT nextval('jobs_loading_detail_non_ematerai_id_seq'::regclass),
  "job_id" varchar(64) COLLATE "pg_catalog"."default" NOT NULL,
  "pr" text COLLATE "pg_catalog"."default" NOT NULL,
  "status" varchar(20) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 'pending'::character varying,
  "message" text COLLATE "pg_catalog"."default",
  "total" int4,
  "not_exists" text COLLATE "pg_catalog"."default",
  "waktu" int4,
  "flagtrans" varchar(20) COLLATE "pg_catalog"."default",
  "created_at" timestamp(6) DEFAULT now(),
  "updated_at" timestamp(6) DEFAULT now()
)
;

-- ----------------------------
-- Table structure for kalender
-- ----------------------------
DROP TABLE IF EXISTS "public"."kalender";
CREATE TABLE "public"."kalender" (
  "nourut" int4,
  "tanggal" timestamp(6) NOT NULL
)
;

-- ----------------------------
-- Table structure for log_approval
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_approval";
CREATE TABLE "public"."log_approval" (
  "log_approval_id" int4 NOT NULL DEFAULT nextval('log_approval_log_approval_id_seq'::regclass),
  "m_loading_id" int4,
  "is_approval" bool,
  "approval_date" timestamp(6),
  "note" text COLLATE "pg_catalog"."default",
  "total_email" int4,
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "approval_user" varchar(15) COLLATE "pg_catalog"."default",
  "check_date" timestamp(6),
  "note_check" text COLLATE "pg_catalog"."default",
  "check_userid" varchar(60) COLLATE "pg_catalog"."default",
  "is_check" int4,
  "rec_split" int4 DEFAULT 0,
  "jeda" int4 DEFAULT 0
)
;

-- ----------------------------
-- Table structure for log_customer
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_customer";
CREATE TABLE "public"."log_customer" (
  "log_customer_id" int4 NOT NULL DEFAULT nextval('log_customer_log_customer_id_seq'::regclass),
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "total_customer" int4
)
;

-- ----------------------------
-- Table structure for log_customer_copy1
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_customer_copy1";
CREATE TABLE "public"."log_customer_copy1" (
  "log_customer_id" int4 NOT NULL DEFAULT nextval('log_customer_log_customer_id_seq'::regclass),
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "total_customer" int4
)
;

-- ----------------------------
-- Table structure for log_customer_copy2
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_customer_copy2";
CREATE TABLE "public"."log_customer_copy2" (
  "log_customer_id" int4 NOT NULL DEFAULT nextval('log_customer_log_customer_id_seq'::regclass),
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "total_customer" int4
)
;

-- ----------------------------
-- Table structure for log_customer_ku
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_customer_ku";
CREATE TABLE "public"."log_customer_ku" (
  "log_customer_ku_id" int4 NOT NULL DEFAULT nextval('log_customer_ku_log_customer_ku_id_seq'::regclass),
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "location_noexist" text COLLATE "pg_catalog"."default",
  "noexist" int4,
  "total_customer" int4,
  "status_ku" varchar(1) COLLATE "pg_catalog"."default" DEFAULT 0,
  "ku_date" timestamp(6)
)
;

-- ----------------------------
-- Table structure for log_error_kirim
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_error_kirim";
CREATE TABLE "public"."log_error_kirim" (
  "log_error_kirim_id" int4 NOT NULL DEFAULT nextval('log_error_kirim_log_error_kirim_id_seq'::regclass),
  "antrian_id" int4,
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "email" varchar(304) COLLATE "pg_catalog"."default",
  "loading_id" int4,
  "jadwal_id" int4,
  "status_sample" bool,
  "error_info" text COLLATE "pg_catalog"."default",
  "tgl_kirim" timestamp(6),
  "email_host" text COLLATE "pg_catalog"."default",
  "email_account" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for log_hapusdata
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_hapusdata";
CREATE TABLE "public"."log_hapusdata" (
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_tabel" varchar(30) COLLATE "pg_catalog"."default",
  "jumlah_records" int4,
  "User" varchar(12) COLLATE "pg_catalog"."default",
  "waktu" timestamp(6),
  "produk_id" varchar(12) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for log_temp_excel_pk
-- ----------------------------
DROP TABLE IF EXISTS "public"."log_temp_excel_pk";
CREATE TABLE "public"."log_temp_excel_pk" (
  "id" int4 NOT NULL DEFAULT nextval('log_temp_excel_pk_id_seq'::regclass),
  "no" text COLLATE "pg_catalog"."default",
  "no_ticket_2" text COLLATE "pg_catalog"."default",
  "card_number" text COLLATE "pg_catalog"."default",
  "nama" text COLLATE "pg_catalog"."default",
  "tgl_close_card" text COLLATE "pg_catalog"."default",
  "tgl_lunas_bayar" text COLLATE "pg_catalog"."default",
  "no_surat" text COLLATE "pg_catalog"."default",
  "tgl_surat" text COLLATE "pg_catalog"."default",
  "alamat_1" text COLLATE "pg_catalog"."default",
  "alamat_2" text COLLATE "pg_catalog"."default",
  "alamat_3" text COLLATE "pg_catalog"."default",
  "alamat_4" text COLLATE "pg_catalog"."default",
  "alamat_5" text COLLATE "pg_catalog"."default",
  "cr_addr_email" text COLLATE "pg_catalog"."default",
  "m_loading_id" text COLLATE "pg_catalog"."default",
  "nama_file" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for m_attach_file
-- ----------------------------
DROP TABLE IF EXISTS "public"."m_attach_file";
CREATE TABLE "public"."m_attach_file" (
  "m_attach_file_id" int4 NOT NULL DEFAULT nextval('m_attach_file_m_attach_file_id_seq'::regclass),
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "name_file" varchar(100) COLLATE "pg_catalog"."default",
  "location_file" varchar(100) COLLATE "pg_catalog"."default",
  "status" bool,
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "modify_date" timestamp(6),
  "keterangan" varchar(100) COLLATE "pg_catalog"."default",
  "ukuran" int8,
  "cid" varchar(8) COLLATE "pg_catalog"."default"
)
;
COMMENT ON TABLE "public"."m_attach_file" IS 'berisi daftar attachment file';

-- ----------------------------
-- Table structure for m_customer
-- ----------------------------
DROP TABLE IF EXISTS "public"."m_customer";
CREATE TABLE "public"."m_customer" (
  "m_customer_id" int4 NOT NULL DEFAULT nextval('m_customer_m_customer_id_seq'::regclass),
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "email1" varchar(100) COLLATE "pg_catalog"."default",
  "email2" varchar(100) COLLATE "pg_catalog"."default",
  "email3" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "log_customer_id" int4
)
;
COMMENT ON TABLE "public"."m_customer" IS 'master customer';

-- ----------------------------
-- Table structure for m_customer_x
-- ----------------------------
DROP TABLE IF EXISTS "public"."m_customer_x";
CREATE TABLE "public"."m_customer_x" (
  "m_customer_id" int4 NOT NULL DEFAULT nextval('m_customer_x_m_customer_id_seq'::regclass),
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "email1" varchar(100) COLLATE "pg_catalog"."default",
  "email2" varchar(100) COLLATE "pg_catalog"."default",
  "email3" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "log_customer_id" int4
)
;
COMMENT ON TABLE "public"."m_customer_x" IS 'master customer temporary';

-- ----------------------------
-- Table structure for m_jadwal
-- ----------------------------
DROP TABLE IF EXISTS "public"."m_jadwal";
CREATE TABLE "public"."m_jadwal" (
  "jadwal_id" int4 NOT NULL DEFAULT nextval('m_jadwal_jadwal_id_seq'::regclass),
  "tgl_jadwal" timestamp(6),
  "flag_jadwal" bool,
  "status" bool,
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "user_create" varchar(50) COLLATE "pg_catalog"."default",
  "date_create" timestamp(6),
  "user_modify" varchar(50) COLLATE "pg_catalog"."default",
  "date_modify" timestamp(6),
  "date_sent" timestamp(6),
  "total" int4 DEFAULT 0
)
;
COMMENT ON TABLE "public"."m_jadwal" IS 'tabel untuk master jadwal kirim';

-- ----------------------------
-- Table structure for m_loading
-- ----------------------------
DROP TABLE IF EXISTS "public"."m_loading";
CREATE TABLE "public"."m_loading" (
  "m_loading_id" int4 NOT NULL DEFAULT nextval('m_loading_loading_id_seq'::regclass),
  "userid" varchar(15) COLLATE "pg_catalog"."default",
  "create_date" timestamp(6),
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "loading_file" varchar(50) COLLATE "pg_catalog"."default",
  "total_halaman" int4,
  "total_customer" int4,
  "total_halaman_cetak" int4,
  "total_customer_cetak" int4,
  "status_report_sftp_excel" bool,
  "waktu_status_report_sftp_excel" timestamp(6),
  "n_fsrv_done" int4,
  "fsrv_start" text COLLATE "pg_catalog"."default",
  "fsrv_end" text COLLATE "pg_catalog"."default",
  "n_sisa_diantrian" int4,
  "blast_start" text COLLATE "pg_catalog"."default",
  "blast_end" text COLLATE "pg_catalog"."default",
  "last_updated" timestamp(6),
  "lama_blast_estat" text COLLATE "pg_catalog"."default",
  "lama_transfer_fsrv" text COLLATE "pg_catalog"."default",
  "n_kenaikan" int4
)
;
COMMENT ON TABLE "public"."m_loading" IS 'log_data detail';

-- ----------------------------
-- Table structure for mail_server
-- ----------------------------
DROP TABLE IF EXISTS "public"."mail_server";
CREATE TABLE "public"."mail_server" (
  "mail_server_id" int4 NOT NULL DEFAULT nextval('mail_server_mail_server_id_seq'::regclass),
  "email_host" varchar(30) COLLATE "pg_catalog"."default",
  "email_from" varchar(100) COLLATE "pg_catalog"."default",
  "email_pass" varchar(20) COLLATE "pg_catalog"."default",
  "status" bool,
  "userid" varchar(50) COLLATE "pg_catalog"."default",
  "waktu" timestamp(6),
  "email_inbox" varchar(200) COLLATE "pg_catalog"."default",
  "email_bounce_back" varchar(100) COLLATE "pg_catalog"."default"
)
;
COMMENT ON TABLE "public"."mail_server" IS 'tabel berisi nama server dan password';

-- ----------------------------
-- Table structure for master_hana_bank
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_hana_bank";
CREATE TABLE "public"."master_hana_bank" (
  "id_master_hana_bank" int4 NOT NULL DEFAULT nextval('master_hana_bank_id_master_hana_bank_seq'::regclass),
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "cycle" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for master_kurir
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_kurir";
CREATE TABLE "public"."master_kurir" (
  "id_master_kurir" int4 NOT NULL DEFAULT nextval('master_kurir_id_master_kurir_seq1'::regclass),
  "kodeproduk" varchar(20) COLLATE "pg_catalog"."default",
  "cardno" varchar(50) COLLATE "pg_catalog"."default",
  "cfull_name" varchar(100) COLLATE "pg_catalog"."default",
  "pic" varchar(100) COLLATE "pg_catalog"."default",
  "alamat" varchar(200) COLLATE "pg_catalog"."default",
  "alamat2" varchar(200) COLLATE "pg_catalog"."default",
  "kota" varchar(100) COLLATE "pg_catalog"."default",
  "kodepos" varchar(10) COLLATE "pg_catalog"."default",
  "kurir" varchar(20) COLLATE "pg_catalog"."default",
  "userid" varchar(12) COLLATE "pg_catalog"."default",
  "created" timestamp(6) DEFAULT now(),
  "modified" timestamp(6),
  "is_active" bool DEFAULT false
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai";
CREATE TABLE "public"."master_proses_after_ematerai" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_id_seq'::regclass),
  "keterangan" varchar(15) COLLATE "pg_catalog"."default",
  "jumlah_proses" int4,
  "waktu" timestamp(6)
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_file
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_file";
CREATE TABLE "public"."master_proses_after_ematerai_file" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_file_id_seq'::regclass),
  "nama_file" text COLLATE "pg_catalog"."default",
  "direktori" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_file_ce_from_bri
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_bri";
CREATE TABLE "public"."master_proses_after_ematerai_file_ce_from_bri" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_file_ce_from_bri_id_seq'::regclass),
  "nama_file" text COLLATE "pg_catalog"."default",
  "direktori" text COLLATE "pg_catalog"."default",
  "waktu_proses" timestamp(6),
  "status_password_pdf" bool
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_file_ce_from_produksi
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi";
CREATE TABLE "public"."master_proses_after_ematerai_file_ce_from_produksi" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_file_ce_from_produksi_id_seq'::regclass),
  "nama_file" text COLLATE "pg_catalog"."default",
  "direktori" text COLLATE "pg_catalog"."default",
  "waktu_proses" timestamp(6)
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_file_ce_from_produksi_ocr
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr";
CREATE TABLE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq'::regclass),
  "nama_file" text COLLATE "pg_catalog"."default",
  "direktori" text COLLATE "pg_catalog"."default",
  "waktu_proses" timestamp(6)
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_file_ce_from_produksi_ocr_summary
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_summary";
CREATE TABLE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_summary" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq'::regclass),
  "nama_cust" text COLLATE "pg_catalog"."default",
  "cif" text COLLATE "pg_catalog"."default",
  "mulai" int4,
  "selesai" int4,
  "total_halaman" int4,
  "nama_file" text COLLATE "pg_catalog"."default",
  "direktori" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_produksi
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_produksi";
CREATE TABLE "public"."master_proses_after_ematerai_produksi" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_produksi_id_seq'::regclass),
  "nama_proses" text COLLATE "pg_catalog"."default",
  "keterangan" varchar(15) COLLATE "pg_catalog"."default",
  "status" bool,
  "waktu" timestamp(6),
  "waktu_selesai" timestamp(6)
)
;

-- ----------------------------
-- Table structure for master_proses_after_ematerai_produksi_copy1
-- ----------------------------
DROP TABLE IF EXISTS "public"."master_proses_after_ematerai_produksi_copy1";
CREATE TABLE "public"."master_proses_after_ematerai_produksi_copy1" (
  "id" int4 NOT NULL DEFAULT nextval('master_proses_after_ematerai_produksi_id_seq'::regclass),
  "nama_proses" text COLLATE "pg_catalog"."default",
  "keterangan" varchar(15) COLLATE "pg_catalog"."default",
  "status" bool,
  "waktu" timestamp(6),
  "waktu_selesai" timestamp(6)
)
;

-- ----------------------------
-- Table structure for menu1
-- ----------------------------
DROP TABLE IF EXISTS "public"."menu1";
CREATE TABLE "public"."menu1" (
  "menuid" int4 NOT NULL DEFAULT nextval('menu1_menuid_seq'::regclass),
  "menu" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default" NOT NULL,
  "lokasi" varchar(100) COLLATE "pg_catalog"."default",
  "urutan" int4,
  "menugroupid" int4,
  "status" bool
)
;

-- ----------------------------
-- Table structure for menugroup1
-- ----------------------------
DROP TABLE IF EXISTS "public"."menugroup1";
CREATE TABLE "public"."menugroup1" (
  "menugroupid" int4 NOT NULL DEFAULT nextval('menugroup1_menugroupid_seq1'::regclass),
  "menugroup" varchar(25) COLLATE "pg_catalog"."default",
  "urutan" int4,
  "accessid" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for menugroup1_
-- ----------------------------
DROP TABLE IF EXISTS "public"."menugroup1_";
CREATE TABLE "public"."menugroup1_" (
  "menugroupid" int4 NOT NULL DEFAULT nextval('menugroup1_menugroupid_seq'::regclass),
  "menugroup" varchar(25) COLLATE "pg_catalog"."default",
  "urutan" int4
)
;

-- ----------------------------
-- Table structure for mkurir
-- ----------------------------
DROP TABLE IF EXISTS "public"."mkurir";
CREATE TABLE "public"."mkurir" (
  "id_mkurir" int4 NOT NULL DEFAULT nextval('mkurir_id_mkurir_seq'::regclass),
  "nomor_rekening" varchar(50) COLLATE "pg_catalog"."default",
  "kurir" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for mproduk
-- ----------------------------
DROP TABLE IF EXISTS "public"."mproduk";
CREATE TABLE "public"."mproduk" (
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default" NOT NULL,
  "produk" varchar(50) COLLATE "pg_catalog"."default",
  "aplikasi_id" int4,
  "group_aplikasi_id" int4,
  "status" bool DEFAULT true,
  "dashboard_online" bool
)
;

-- ----------------------------
-- Table structure for pdf_jobs
-- ----------------------------
DROP TABLE IF EXISTS "public"."pdf_jobs";
CREATE TABLE "public"."pdf_jobs" (
  "id" int8 NOT NULL DEFAULT nextval('pdf_jobs_id_seq'::regclass),
  "job_id" varchar(64) COLLATE "pg_catalog"."default" NOT NULL,
  "description" text COLLATE "pg_catalog"."default",
  "status" varchar(20) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 'pending'::character varying,
  "total_items" int4 DEFAULT 0,
  "processed" int4 DEFAULT 0,
  "failed" int4 DEFAULT 0,
  "created_at" timestamp(6) DEFAULT now(),
  "updated_at" timestamp(6) DEFAULT now()
)
;

-- ----------------------------
-- Table structure for pdf_queue
-- ----------------------------
DROP TABLE IF EXISTS "public"."pdf_queue";
CREATE TABLE "public"."pdf_queue" (
  "id" int8 NOT NULL DEFAULT nextval('pdf_queue_id_seq'::regclass),
  "job_id" varchar(64) COLLATE "pg_catalog"."default" NOT NULL,
  "ref_id" int8 NOT NULL,
  "status" varchar(20) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 'pending'::character varying,
  "last_error" text COLLATE "pg_catalog"."default",
  "updated_at" timestamp(6) DEFAULT now()
)
;

-- ----------------------------
-- Table structure for read_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."read_email";
CREATE TABLE "public"."read_email" (
  "read_email_id" int4 NOT NULL DEFAULT nextval('read_email_read_email_id_seq'::regclass),
  "tr_email_id" int4,
  "waktu_read_email" timestamp(6),
  "body_email" text COLLATE "pg_catalog"."default",
  "keterangan" varchar(50) COLLATE "pg_catalog"."default",
  "tr_email_id2" int8
)
;
COMMENT ON TABLE "public"."read_email" IS 'tabel untuk simpan history read email _log_baca_062023_CO';

-- ----------------------------
-- Table structure for sample_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."sample_email";
CREATE TABLE "public"."sample_email" (
  "email_sample" text COLLATE "pg_catalog"."default" NOT NULL
)
;
COMMENT ON TABLE "public"."sample_email" IS 'berisi email-email sample';

-- ----------------------------
-- Table structure for tampung
-- ----------------------------
DROP TABLE IF EXISTS "public"."tampung";
CREATE TABLE "public"."tampung" (
  "tampung_id" int4 NOT NULL DEFAULT nextval('tampung_tampung_id_seq'::regclass),
  "email" varchar(60) COLLATE "pg_catalog"."default",
  "body_email" text COLLATE "pg_catalog"."default",
  "tr_email_id" varchar(60) COLLATE "pg_catalog"."default",
  "tgl_read" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for temp_excel_all_saved
-- ----------------------------
DROP TABLE IF EXISTS "public"."temp_excel_all_saved";
CREATE TABLE "public"."temp_excel_all_saved" (
  "id" int4 NOT NULL DEFAULT nextval('temp_excel_all_saved_id_seq'::regclass),
  "no" text COLLATE "pg_catalog"."default",
  "no_ticket_2" text COLLATE "pg_catalog"."default",
  "card_number" text COLLATE "pg_catalog"."default",
  "nama" text COLLATE "pg_catalog"."default",
  "tgl_close_card" text COLLATE "pg_catalog"."default",
  "tgl_lunas_bayar" text COLLATE "pg_catalog"."default",
  "no_surat" text COLLATE "pg_catalog"."default",
  "tgl_surat" text COLLATE "pg_catalog"."default",
  "alamat_1" text COLLATE "pg_catalog"."default",
  "alamat_2" text COLLATE "pg_catalog"."default",
  "alamat_3" text COLLATE "pg_catalog"."default",
  "alamat_4" text COLLATE "pg_catalog"."default",
  "alamat_5" text COLLATE "pg_catalog"."default",
  "cr_addr_email" text COLLATE "pg_catalog"."default",
  "nama_pdf" text COLLATE "pg_catalog"."default",
  "divisi" text COLLATE "pg_catalog"."default",
  "jabatan_kadiv" text COLLATE "pg_catalog"."default",
  "barcode_kadiv" text COLLATE "pg_catalog"."default",
  "hp" text COLLATE "pg_catalog"."default",
  "m_loading_id" text COLLATE "pg_catalog"."default",
  "nama_file" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for temp_excel_pk
-- ----------------------------
DROP TABLE IF EXISTS "public"."temp_excel_pk";
CREATE TABLE "public"."temp_excel_pk" (
  "id" int4 NOT NULL DEFAULT nextval('temp_excel_pk_id_seq'::regclass),
  "no" text COLLATE "pg_catalog"."default",
  "no_ticket_2" text COLLATE "pg_catalog"."default",
  "card_number" text COLLATE "pg_catalog"."default",
  "nama" text COLLATE "pg_catalog"."default",
  "tgl_close_card" text COLLATE "pg_catalog"."default",
  "tgl_lunas_bayar" text COLLATE "pg_catalog"."default",
  "no_surat" text COLLATE "pg_catalog"."default",
  "tgl_surat" text COLLATE "pg_catalog"."default",
  "alamat_1" text COLLATE "pg_catalog"."default",
  "alamat_2" text COLLATE "pg_catalog"."default",
  "alamat_3" text COLLATE "pg_catalog"."default",
  "alamat_4" text COLLATE "pg_catalog"."default",
  "alamat_5" text COLLATE "pg_catalog"."default",
  "cr_addr_email" text COLLATE "pg_catalog"."default",
  "nama_pdf" text COLLATE "pg_catalog"."default",
  "divisi" text COLLATE "pg_catalog"."default",
  "jabatan_kadiv" text COLLATE "pg_catalog"."default",
  "barcode_kadiv" text COLLATE "pg_catalog"."default",
  "hp" text COLLATE "pg_catalog"."default",
  "m_loading_id" text COLLATE "pg_catalog"."default",
  "nama_file" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for temp_excel_sl
-- ----------------------------
DROP TABLE IF EXISTS "public"."temp_excel_sl";
CREATE TABLE "public"."temp_excel_sl" (
  "id" int4 NOT NULL DEFAULT nextval('temp_excel_sl_id_seq'::regclass),
  "no" text COLLATE "pg_catalog"."default",
  "no_ticket_2" text COLLATE "pg_catalog"."default",
  "card_number" text COLLATE "pg_catalog"."default",
  "nama" text COLLATE "pg_catalog"."default",
  "tgl_close_card" text COLLATE "pg_catalog"."default",
  "tgl_lunas_bayar" text COLLATE "pg_catalog"."default",
  "no_surat" text COLLATE "pg_catalog"."default",
  "no_surat1" text COLLATE "pg_catalog"."default",
  "no_surat2" text COLLATE "pg_catalog"."default",
  "no_surat3" text COLLATE "pg_catalog"."default",
  "tgl_surat" text COLLATE "pg_catalog"."default",
  "alamat_1" text COLLATE "pg_catalog"."default",
  "alamat_2" text COLLATE "pg_catalog"."default",
  "alamat_3" text COLLATE "pg_catalog"."default",
  "alamat_4" text COLLATE "pg_catalog"."default",
  "alamat_5" text COLLATE "pg_catalog"."default",
  "cr_addr_email" text COLLATE "pg_catalog"."default",
  "nama_pdf" text COLLATE "pg_catalog"."default",
  "m_loading_id" text COLLATE "pg_catalog"."default",
  "nama_file" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for template_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."template_email";
CREATE TABLE "public"."template_email" (
  "template_email_id" int4 NOT NULL DEFAULT nextval('template_email_template_email_id_seq'::regclass),
  "nama_template" varchar(20) COLLATE "pg_catalog"."default" NOT NULL,
  "subject_email" varchar(200) COLLATE "pg_catalog"."default",
  "isi_email" text COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "reply_to_email" varchar(100) COLLATE "pg_catalog"."default",
  "mail_server_id" int4,
  "from_name" varchar(50) COLLATE "pg_catalog"."default",
  "status" bool
)
;
COMMENT ON TABLE "public"."template_email" IS 'berisi master template untuk email';

-- ----------------------------
-- Table structure for tmp_cek_embos
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_cek_embos";
CREATE TABLE "public"."tmp_cek_embos" (
  "cardno" varchar(255) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Table structure for tmp_cek_embos_copy1
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_cek_embos_copy1";
CREATE TABLE "public"."tmp_cek_embos_copy1" (
  "cardno" varchar(255) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Table structure for tmp_excel_bri_corp1
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_excel_bri_corp1";
CREATE TABLE "public"."tmp_excel_bri_corp1" (
  "id_tmp_excel_bri_corp" int4 NOT NULL DEFAULT nextval('tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq'::regclass),
  "nama" text COLLATE "pg_catalog"."default",
  "tagihan_sebelumnya" text COLLATE "pg_catalog"."default",
  "pembayaran" text COLLATE "pg_catalog"."default",
  "pembelanjaan" text COLLATE "pg_catalog"."default",
  "pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "tagihan_baru" text COLLATE "pg_catalog"."default",
  "sisa_kredit" text COLLATE "pg_catalog"."default",
  "bunga_pembelanjaan" text COLLATE "pg_catalog"."default",
  "bunga_pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "tanggal_cetak" text COLLATE "pg_catalog"."default",
  "jatuh_tempo_pembayaran" text COLLATE "pg_catalog"."default",
  "batas_kredit" text COLLATE "pg_catalog"."default",
  "batas_pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "nama_file" text COLLATE "pg_catalog"."default",
  "account_corp_number" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_excel_bri_corp2
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_excel_bri_corp2";
CREATE TABLE "public"."tmp_excel_bri_corp2" (
  "id_tmp_excel_bri_corp" int4 NOT NULL DEFAULT nextval('tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq'::regclass),
  "cardholder_number" text COLLATE "pg_catalog"."default",
  "cardholder_name" text COLLATE "pg_catalog"."default",
  "tagihan_sebelumnya" text COLLATE "pg_catalog"."default",
  "pembayaran_kredit" text COLLATE "pg_catalog"."default",
  "pembelanjaan_debit" text COLLATE "pg_catalog"."default",
  "pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "finance_charge" text COLLATE "pg_catalog"."default",
  "tagihan_baru_idr" text COLLATE "pg_catalog"."default",
  "id_tmp_excel_bri_corp1" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_excel_bri_corp3
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_excel_bri_corp3";
CREATE TABLE "public"."tmp_excel_bri_corp3" (
  "id_tmp_excel_bri_corp" int4 NOT NULL DEFAULT nextval('tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq'::regclass),
  "nama" text COLLATE "pg_catalog"."default",
  "pembelanjaan" text COLLATE "pg_catalog"."default",
  "pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "tagihan_baru" text COLLATE "pg_catalog"."default",
  "sisa_kredit" text COLLATE "pg_catalog"."default",
  "nomor_kartu" text COLLATE "pg_catalog"."default",
  "tanggal_cetak" text COLLATE "pg_catalog"."default",
  "jatuh_tempo_pembayaran" text COLLATE "pg_catalog"."default",
  "batas_kredit" text COLLATE "pg_catalog"."default",
  "batas_pengambilan_tunai" text COLLATE "pg_catalog"."default",
  "tanggal_transaksi" text COLLATE "pg_catalog"."default",
  "tanggal_pembukuan" text COLLATE "pg_catalog"."default",
  "keterangan" text COLLATE "pg_catalog"."default",
  "transaksi_valas" text COLLATE "pg_catalog"."default",
  "nilai_tukar" text COLLATE "pg_catalog"."default",
  "jumlah_idr" text COLLATE "pg_catalog"."default",
  "id_tmp_excel_bri_corp1" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_excel_hana_bank
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_excel_hana_bank";
CREATE TABLE "public"."tmp_excel_hana_bank" (
  "id_tmp_excel_hana_bank" int4 NOT NULL DEFAULT nextval('tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq'::regclass),
  "credit_card_no" text COLLATE "pg_catalog"."default",
  "billing_cycle" text COLLATE "pg_catalog"."default",
  "min_payment" text COLLATE "pg_catalog"."default",
  "full_payment" text COLLATE "pg_catalog"."default",
  "due_date" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_ku
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_ku";
CREATE TABLE "public"."tmp_ku" (
  "cardno" varchar(50) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_split
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_split";
CREATE TABLE "public"."tmp_split" (
  "detail_id" int4 NOT NULL DEFAULT nextval('tmp_split_detail_id_seq'::regclass),
  "m_loading_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "nama" varchar(60) COLLATE "pg_catalog"."default",
  "alamat1" varchar(200) COLLATE "pg_catalog"."default",
  "alamat2" varchar(200) COLLATE "pg_catalog"."default",
  "alamat3" varchar(200) COLLATE "pg_catalog"."default",
  "alamat4" varchar(200) COLLATE "pg_catalog"."default",
  "alamat5" varchar(200) COLLATE "pg_catalog"."default",
  "city" varchar(40) COLLATE "pg_catalog"."default",
  "zipcode" varchar(9) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "pdf_name" varchar(100) COLLATE "pg_catalog"."default",
  "password_pdf" varchar(25) COLLATE "pg_catalog"."default",
  "jml_hlm" int4,
  "flag_attach" varchar(3) COLLATE "pg_catalog"."default",
  "tipe_kartu" varchar(40) COLLATE "pg_catalog"."default",
  "no_rek_asli" varchar(20) COLLATE "pg_catalog"."default",
  "ket_produk" varchar(60) COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "n_email" int4 DEFAULT 1,
  "size_pdf" numeric(6,2),
  "barcode" text COLLATE "pg_catalog"."default",
  "nama_produk" text COLLATE "pg_catalog"."default",
  "tanggal" text COLLATE "pg_catalog"."default",
  "total_produk" text COLLATE "pg_catalog"."default",
  "kode_cab" varchar(10) COLLATE "pg_catalog"."default",
  "cabang" varchar(50) COLLATE "pg_catalog"."default",
  "kurir" varchar(15) COLLATE "pg_catalog"."default",
  "start_pdf" varchar(15) COLLATE "pg_catalog"."default",
  "end_pdf" varchar(15) COLLATE "pg_catalog"."default"
)
WITH (OIDS=TRUE)
;

-- ----------------------------
-- Table structure for tmp_tgl_dashboard_cashin_
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_tgl_dashboard_cashin_";
CREATE TABLE "public"."tmp_tgl_dashboard_cashin_" (
  "urutan" int4,
  "tgl" float8,
  "jam" float8,
  "jumlah" int8,
  "tanggal" timestamp(6),
  "date_email_callback" timestamp(6),
  "cabang" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_tgl_dashboard_cashin_appdev
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_tgl_dashboard_cashin_appdev";
CREATE TABLE "public"."tmp_tgl_dashboard_cashin_appdev" (
  "urutan" int4,
  "tgl" float8,
  "jam" float8,
  "jumlah" int8,
  "tanggal" timestamp(6),
  "date_email_callback" timestamp(6),
  "cabang" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tmp_tgl_dashboard_failed_appdev
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_tgl_dashboard_failed_appdev";
CREATE TABLE "public"."tmp_tgl_dashboard_failed_appdev" (
  "urutan" int4,
  "tgl" float8,
  "jumlah" numeric
)
;

-- ----------------------------
-- Table structure for tmp_tgl_dashboard_success_appdev
-- ----------------------------
DROP TABLE IF EXISTS "public"."tmp_tgl_dashboard_success_appdev";
CREATE TABLE "public"."tmp_tgl_dashboard_success_appdev" (
  "urutan" int4,
  "tgl" float8,
  "jumlah" numeric
)
;

-- ----------------------------
-- Table structure for tr_email
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email";
CREATE TABLE "public"."tr_email" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_tr_email_id_seq1'::regclass),
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" varchar(150) COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;
COMMENT ON TABLE "public"."tr_email" IS 'tabel berisi status pengiriman email';

-- ----------------------------
-- Table structure for tr_email_032025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_032025_bc";
CREATE TABLE "public"."tr_email_032025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_032025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_032025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_032025_co";
CREATE TABLE "public"."tr_email_032025_co" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_032025_co_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_032025_mt
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_032025_mt";
CREATE TABLE "public"."tr_email_032025_mt" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_032025_mt_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_052025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_052025_bc";
CREATE TABLE "public"."tr_email_052025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_052025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_052025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_052025_co";
CREATE TABLE "public"."tr_email_052025_co" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_052025_co_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_072025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_072025_bc";
CREATE TABLE "public"."tr_email_072025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_072025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_082025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_082025_bc";
CREATE TABLE "public"."tr_email_082025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_082025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_102025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_102025_bc";
CREATE TABLE "public"."tr_email_102025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_102025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_102025_co
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_102025_co";
CREATE TABLE "public"."tr_email_102025_co" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_102025_co_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_102025_mt
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_102025_mt";
CREATE TABLE "public"."tr_email_102025_mt" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_102025_mt_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_112025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_112025_bc";
CREATE TABLE "public"."tr_email_112025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_112025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_122025_bc
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_122025_bc";
CREATE TABLE "public"."tr_email_122025_bc" (
  "tr_email_id" int4 NOT NULL DEFAULT nextval('tr_email_122025_bc_tr_email_id_seq'::regclass),
  "nomor_customer" varchar(500) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "read_method" varchar(12) COLLATE "pg_catalog"."default",
  "delay_info" text COLLATE "pg_catalog"."default",
  "info_device" text COLLATE "pg_catalog"."default",
  "tipe_gagal" text COLLATE "pg_catalog"."default",
  "respon" text COLLATE "pg_catalog"."default",
  "tgl_read2" text COLLATE "pg_catalog"."default",
  "kode_kirim" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_473
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_473";
CREATE TABLE "public"."tr_email_473" (
  "tr_email_id" int4,
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" text COLLATE "pg_catalog"."default",
  "email" text COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "nama_customer" text COLLATE "pg_catalog"."default",
  "tgl_read" timestamp(6),
  "status_sample" bool,
  "body_email_read" text COLLATE "pg_catalog"."default",
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for tr_email_history
-- ----------------------------
DROP TABLE IF EXISTS "public"."tr_email_history";
CREATE TABLE "public"."tr_email_history" (
  "tr_email_history_id" int4 NOT NULL DEFAULT nextval('tr_email_history_tr_email_history_id_seq'::regclass),
  "nomor_customer" varchar(20) COLLATE "pg_catalog"."default",
  "nomor_rekening" varchar(20) COLLATE "pg_catalog"."default",
  "blth" varchar(6) COLLATE "pg_catalog"."default",
  "flagtrans" varchar(2) COLLATE "pg_catalog"."default",
  "nama_file" varchar(50) COLLATE "pg_catalog"."default",
  "email" varchar(100) COLLATE "pg_catalog"."default",
  "email_sukses" bool,
  "date_email_send" timestamp(6),
  "date_email_callback" timestamp(6),
  "ket_error" varchar(700) COLLATE "pg_catalog"."default",
  "email_callback" bool,
  "count_sent" int4,
  "loading_id" int4,
  "user_modify" varchar(20) COLLATE "pg_catalog"."default",
  "tgl_modify" timestamp(6),
  "tr_email_id" int4,
  "antrian_id" int4,
  "template_id" int4,
  "k_nama_body_email" text COLLATE "pg_catalog"."default",
  "k_lampiran_email" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for user1
-- ----------------------------
DROP TABLE IF EXISTS "public"."user1";
CREATE TABLE "public"."user1" (
  "userid" varchar(12) COLLATE "pg_catalog"."default" NOT NULL,
  "nama" varchar(30) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for usermenu1
-- ----------------------------
DROP TABLE IF EXISTS "public"."usermenu1";
CREATE TABLE "public"."usermenu1" (
  "userid" varchar(12) COLLATE "pg_catalog"."default",
  "menuid" int4,
  "akses" varchar(4) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."_log_baca__read_email_id_seq"
OWNED BY "public"."_log_baca_"."read_email_id";
SELECT setval('"public"."_log_baca__read_email_id_seq"', 19, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."_suppression_list_aws_id_seq"
OWNED BY "public"."_suppression_list_aws"."id";
SELECT setval('"public"."_suppression_list_aws_id_seq"', 339148, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."_tr_email_log_bulan_lalu_id_seq"
OWNED BY "public"."_tr_email_log_bulan_lalu"."id";
SELECT setval('"public"."_tr_email_log_bulan_lalu_id_seq"', 48067421, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."_urutan_no_pdf_ematerai_id_seq"
OWNED BY "public"."_urutan_no_pdf_ematerai"."id";
SELECT setval('"public"."_urutan_no_pdf_ematerai_id_seq"', 1, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."access1_accessid_seq"
OWNED BY "public"."access1"."accessid";
SELECT setval('"public"."access1_accessid_seq"', 11, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."antrian_email_antrian_id_seq"
OWNED BY "public"."antrian_email"."antrian_id";
SELECT setval('"public"."antrian_email_antrian_id_seq"', 34128362, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."antrian_email_attach_file_antrian_email_attach_file_id_seq"
OWNED BY "public"."antrian_email_attach_file"."antrian_email_attach_file_id";
SELECT setval('"public"."antrian_email_attach_file_antrian_email_attach_file_id_seq"', 24388543, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq"
OWNED BY "public"."antrian_email_attach_pdf"."antrian_email_attach_pdf_id";
SELECT setval('"public"."antrian_email_attach_pdf_antrian_email_attach_pdf_id_seq"', 22601, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."antrian_email_history_antrian_history_id_seq"
OWNED BY "public"."antrian_email_history"."antrian_history_id";
SELECT setval('"public"."antrian_email_history_antrian_history_id_seq"', 33022270, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."attach_selektif_attach_selektif_id_seq"
OWNED BY "public"."attach_selektif"."attach_selektif_id";
SELECT setval('"public"."attach_selektif_attach_selektif_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."autofeedback_history_autofeedback_id_seq"
OWNED BY "public"."autofeedback_history"."autofeedback_id";
SELECT setval('"public"."autofeedback_history_autofeedback_id_seq"', 2065596, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."bounce_inbox_bounce_inbox_id_seq"
OWNED BY "public"."bounce_inbox"."bounce_inbox_id";
SELECT setval('"public"."bounce_inbox_bounce_inbox_id_seq"', 1215133, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."code_email_code_email_id_seq"
OWNED BY "public"."code_email"."code_email_id";
SELECT setval('"public"."code_email_code_email_id_seq"', 62, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_032025_bc_detail_id_seq"
OWNED BY "public"."detail_032025_bc"."detail_id";
SELECT setval('"public"."detail_032025_bc_detail_id_seq"', 312434, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_032025_co_detail_id_seq"
OWNED BY "public"."detail_032025_co"."detail_id";
SELECT setval('"public"."detail_032025_co_detail_id_seq"', 1773, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_032025_mt_detail_id_seq"
OWNED BY "public"."detail_032025_mt"."detail_id";
SELECT setval('"public"."detail_032025_mt_detail_id_seq"', 100481, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_052025_bc_detail_id_seq"
OWNED BY "public"."detail_052025_bc"."detail_id";
SELECT setval('"public"."detail_052025_bc_detail_id_seq"', 23, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_052025_co_detail_id_seq"
OWNED BY "public"."detail_052025_co"."detail_id";
SELECT setval('"public"."detail_052025_co_detail_id_seq"', 11, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_072025_bc_detail_id_seq"
OWNED BY "public"."detail_072025_bc"."detail_id";
SELECT setval('"public"."detail_072025_bc_detail_id_seq"', 30, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_082025_bc_detail_id_seq"
OWNED BY "public"."detail_082025_bc"."detail_id";
SELECT setval('"public"."detail_082025_bc_detail_id_seq"', 2, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_102025_bc_detail_id_seq"
OWNED BY "public"."detail_102025_bc"."detail_id";
SELECT setval('"public"."detail_102025_bc_detail_id_seq"', 40880, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_102025_co_detail_id_seq"
OWNED BY "public"."detail_102025_co"."detail_id";
SELECT setval('"public"."detail_102025_co_detail_id_seq"', 78, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_102025_mt_detail_id_seq"
OWNED BY "public"."detail_102025_mt"."detail_id";
SELECT setval('"public"."detail_102025_mt_detail_id_seq"', 523, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_112025_bc_detail_id_seq"
OWNED BY "public"."detail_112025_bc"."detail_id";
SELECT setval('"public"."detail_112025_bc_detail_id_seq"', 7, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_122025_bc_detail_id_seq"
OWNED BY "public"."detail_122025_bc"."detail_id";
SELECT setval('"public"."detail_122025_bc_detail_id_seq"', 330109, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_cetak_detail_id_seq"
OWNED BY "public"."detail_cetak"."detail_id";
SELECT setval('"public"."detail_cetak_detail_id_seq"', 252424, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."detail_detail_id_seq"
OWNED BY "public"."detail"."detail_id";
SELECT setval('"public"."detail_detail_id_seq"', 1215870, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."jobs_loading_detail_non_ematerai_id_seq"
OWNED BY "public"."jobs_loading_detail_non_ematerai"."id";
SELECT setval('"public"."jobs_loading_detail_non_ematerai_id_seq"', 3, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."log_approval_log_approval_id_seq"
OWNED BY "public"."log_approval"."log_approval_id";
SELECT setval('"public"."log_approval_log_approval_id_seq"', 13170, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."log_customer_ku_log_customer_ku_id_seq"
OWNED BY "public"."log_customer_ku"."log_customer_ku_id";
SELECT setval('"public"."log_customer_ku_log_customer_ku_id_seq"', 29, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."log_customer_log_customer_id_seq"
OWNED BY "public"."log_customer"."log_customer_id";
SELECT setval('"public"."log_customer_log_customer_id_seq"', 4425, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."log_error_kirim_log_error_kirim_id_seq"
OWNED BY "public"."log_error_kirim"."log_error_kirim_id";
SELECT setval('"public"."log_error_kirim_log_error_kirim_id_seq"', 3077029, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."log_temp_excel_pk_id_seq"
OWNED BY "public"."log_temp_excel_pk"."id";
SELECT setval('"public"."log_temp_excel_pk_id_seq"', 359112, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_attach_file_m_attach_file_id_seq"
OWNED BY "public"."m_attach_file"."m_attach_file_id";
SELECT setval('"public"."m_attach_file_m_attach_file_id_seq"', 80, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_customer_history_m_customer_id_seq"
OWNED BY "public"."m_customer_history"."m_customer_id";
SELECT setval('"public"."m_customer_history_m_customer_id_seq"', 13691, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_customer_ku_m_customer_ku_id_seq"
OWNED BY "public"."m_customer_ku"."m_customer_ku_id";
SELECT setval('"public"."m_customer_ku_m_customer_ku_id_seq"', 240, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_customer_m_customer_id_seq"
OWNED BY "public"."m_customer"."m_customer_id";
SELECT setval('"public"."m_customer_m_customer_id_seq"', 18453297, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_customer_x_m_customer_id_seq"
OWNED BY "public"."m_customer_x"."m_customer_id";
SELECT setval('"public"."m_customer_x_m_customer_id_seq"', 9, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_jadwal_jadwal_id_seq"
OWNED BY "public"."m_jadwal"."jadwal_id";
SELECT setval('"public"."m_jadwal_jadwal_id_seq"', 356234, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."m_loading_loading_id_seq"
OWNED BY "public"."m_loading"."m_loading_id";
SELECT setval('"public"."m_loading_loading_id_seq"', 7989, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."mail_server_mail_server_id_seq"
OWNED BY "public"."mail_server"."mail_server_id";
SELECT setval('"public"."mail_server_mail_server_id_seq"', 8, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_hana_bank_id_master_hana_bank_seq"
OWNED BY "public"."master_hana_bank"."id_master_hana_bank";
SELECT setval('"public"."master_hana_bank_id_master_hana_bank_seq"', 13, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_kurir_id_master_kurir_seq1"
OWNED BY "public"."master_kurir"."id_master_kurir";
SELECT setval('"public"."master_kurir_id_master_kurir_seq1"', 1290, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_bri_id_seq"
OWNED BY "public"."master_proses_after_ematerai_file_ce_from_bri"."id";
SELECT setval('"public"."master_proses_after_ematerai_file_ce_from_bri_id_seq"', 410501, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_id_seq"
OWNED BY "public"."master_proses_after_ematerai_file_ce_from_produksi"."id";
SELECT setval('"public"."master_proses_after_ematerai_file_ce_from_produksi_id_seq"', 3238, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq"
OWNED BY "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr"."id";
SELECT setval('"public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_id_seq"', 3033, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq"
OWNED BY "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_summary"."id";
SELECT setval('"public"."master_proses_after_ematerai_file_ce_from_produksi_ocr_s_id_seq"', 182341, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_file_id_seq"
OWNED BY "public"."master_proses_after_ematerai_file"."id";
SELECT setval('"public"."master_proses_after_ematerai_file_id_seq"', 6439909, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_id_seq"
OWNED BY "public"."master_proses_after_ematerai"."id";
SELECT setval('"public"."master_proses_after_ematerai_id_seq"', 1, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."master_proses_after_ematerai_produksi_id_seq"
OWNED BY "public"."master_proses_after_ematerai_produksi"."id";
SELECT setval('"public"."master_proses_after_ematerai_produksi_id_seq"', 4, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."menu1_menuid_seq"
OWNED BY "public"."menu1"."menuid";
SELECT setval('"public"."menu1_menuid_seq"', 74, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."menugroup1_menugroupid_seq"
OWNED BY "public"."menugroup1_"."menugroupid";
SELECT setval('"public"."menugroup1_menugroupid_seq"', 3, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."menugroup1_menugroupid_seq1"
OWNED BY "public"."menugroup1"."menugroupid";
SELECT setval('"public"."menugroup1_menugroupid_seq1"', 3, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."mkurir_id_mkurir_seq"
OWNED BY "public"."mkurir"."id_mkurir";
SELECT setval('"public"."mkurir_id_mkurir_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."pdf_jobs_id_seq"
OWNED BY "public"."pdf_jobs"."id";
SELECT setval('"public"."pdf_jobs_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."pdf_queue_id_seq"
OWNED BY "public"."pdf_queue"."id";
SELECT setval('"public"."pdf_queue_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."read_email_read_email_id_seq"
OWNED BY "public"."read_email"."read_email_id";
SELECT setval('"public"."read_email_read_email_id_seq"', 8409079, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tampung_tampung_id_seq"
OWNED BY "public"."tampung"."tampung_id";
SELECT setval('"public"."tampung_tampung_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."temp_excel_all_saved_id_seq"
OWNED BY "public"."temp_excel_all_saved"."id";
SELECT setval('"public"."temp_excel_all_saved_id_seq"', 471, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."temp_excel_pk_id_seq"
OWNED BY "public"."temp_excel_pk"."id";
SELECT setval('"public"."temp_excel_pk_id_seq"', 471, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."temp_excel_sl_id_seq"
OWNED BY "public"."temp_excel_sl"."id";
SELECT setval('"public"."temp_excel_sl_id_seq"', 2, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."template_email_template_email_id_seq"
OWNED BY "public"."template_email"."template_email_id";
SELECT setval('"public"."template_email_template_email_id_seq"', 34, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq"
OWNED BY "public"."tmp_excel_bri_corp1"."id_tmp_excel_bri_corp";
SELECT setval('"public"."tmp_excel_bri_corp1_id_tmp_excel_bri_corp_seq"', 46904, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq"
OWNED BY "public"."tmp_excel_bri_corp2"."id_tmp_excel_bri_corp";
SELECT setval('"public"."tmp_excel_bri_corp2_id_tmp_excel_bri_corp_seq"', 189323, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq"
OWNED BY "public"."tmp_excel_bri_corp3"."id_tmp_excel_bri_corp";
SELECT setval('"public"."tmp_excel_bri_corp3_id_tmp_excel_bri_corp_seq"', 1488632, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq"
OWNED BY "public"."tmp_excel_hana_bank"."id_tmp_excel_hana_bank";
SELECT setval('"public"."tmp_excel_hana_bank_id_tmp_excel_hana_bank_seq"', 3663, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tmp_split_detail_id_seq"
OWNED BY "public"."tmp_split"."detail_id";
SELECT setval('"public"."tmp_split_detail_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_032025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_032025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_032025_bc_tr_email_id_seq"', 102566, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_032025_co_tr_email_id_seq"
OWNED BY "public"."tr_email_032025_co"."tr_email_id";
SELECT setval('"public"."tr_email_032025_co_tr_email_id_seq"', 8780, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_032025_mt_tr_email_id_seq"
OWNED BY "public"."tr_email_032025_mt"."tr_email_id";
SELECT setval('"public"."tr_email_032025_mt_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_052025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_052025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_052025_bc_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_052025_co_tr_email_id_seq"
OWNED BY "public"."tr_email_052025_co"."tr_email_id";
SELECT setval('"public"."tr_email_052025_co_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_072025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_072025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_072025_bc_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_082025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_082025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_082025_bc_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_102025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_102025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_102025_bc_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_102025_co_tr_email_id_seq"
OWNED BY "public"."tr_email_102025_co"."tr_email_id";
SELECT setval('"public"."tr_email_102025_co_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_102025_mt_tr_email_id_seq"
OWNED BY "public"."tr_email_102025_mt"."tr_email_id";
SELECT setval('"public"."tr_email_102025_mt_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_112025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_112025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_112025_bc_tr_email_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_122025_bc_tr_email_id_seq"
OWNED BY "public"."tr_email_122025_bc"."tr_email_id";
SELECT setval('"public"."tr_email_122025_bc_tr_email_id_seq"', 1, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_history_tr_email_history_id_seq"
OWNED BY "public"."tr_email_history"."tr_email_history_id";
SELECT setval('"public"."tr_email_history_tr_email_history_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."tr_email_tr_email_id_seq1"
OWNED BY "public"."tr_email"."tr_email_id";
SELECT setval('"public"."tr_email_tr_email_id_seq1"', 341270, true);

-- ----------------------------
-- Indexes structure for table _log_baca_
-- ----------------------------
CREATE INDEX "_log_baca__index" ON "public"."_log_baca_" USING btree (
  "read_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "tr_email_id2" "pg_catalog"."int8_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table _log_baca_
-- ----------------------------
ALTER TABLE "public"."_log_baca_" ADD CONSTRAINT "_log_baca__p_k" PRIMARY KEY ("read_email_id");

-- ----------------------------
-- Primary Key structure for table _suppression_list_aws
-- ----------------------------
ALTER TABLE "public"."_suppression_list_aws" ADD CONSTRAINT "_suppression_list_aws_p_k" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table _tr_email_log_bulan_lalu
-- ----------------------------
CREATE INDEX "idx_email__tr_email_log_bulan_lalu" ON "public"."_tr_email_log_bulan_lalu" USING btree (
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table _tr_email_log_bulan_lalu
-- ----------------------------
ALTER TABLE "public"."_tr_email_log_bulan_lalu" ADD CONSTRAINT "_tr_email_log_bulan_lalu_p_k" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table _urutan_no_pdf_ematerai
-- ----------------------------
ALTER TABLE "public"."_urutan_no_pdf_ematerai" ADD CONSTRAINT "pk__urutan_no_pdf_ematerai" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table access1
-- ----------------------------
ALTER TABLE "public"."access1" ADD CONSTRAINT "pk_access1" PRIMARY KEY ("accessid");

-- ----------------------------
-- Indexes structure for table antrian_email
-- ----------------------------
CREATE INDEX "idx_antrian_email_template_email" ON "public"."antrian_email" USING btree (
  "template_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table antrian_email
-- ----------------------------
ALTER TABLE "public"."antrian_email" ADD CONSTRAINT "antrian_p_k" PRIMARY KEY ("antrian_id");

-- ----------------------------
-- Primary Key structure for table antrian_email_attach_file
-- ----------------------------
ALTER TABLE "public"."antrian_email_attach_file" ADD CONSTRAINT "pk_antrian_email_attach_file" PRIMARY KEY ("antrian_email_attach_file_id");

-- ----------------------------
-- Primary Key structure for table antrian_email_attach_pdf
-- ----------------------------
ALTER TABLE "public"."antrian_email_attach_pdf" ADD CONSTRAINT "pk_antrian_email_attach_pdf" PRIMARY KEY ("antrian_email_attach_pdf_id");

-- ----------------------------
-- Primary Key structure for table antrian_email_history
-- ----------------------------
ALTER TABLE "public"."antrian_email_history" ADD CONSTRAINT "antrian_history_p_k" PRIMARY KEY ("antrian_history_id");

-- ----------------------------
-- Primary Key structure for table attach_selektif
-- ----------------------------
ALTER TABLE "public"."attach_selektif" ADD CONSTRAINT "pk_attach_selektif" PRIMARY KEY ("attach_selektif_id");

-- ----------------------------
-- Primary Key structure for table autofeedback_history
-- ----------------------------
ALTER TABLE "public"."autofeedback_history" ADD CONSTRAINT "autofeedback_history_pk" PRIMARY KEY ("autofeedback_id");

-- ----------------------------
-- Primary Key structure for table bounce_inbox
-- ----------------------------
ALTER TABLE "public"."bounce_inbox" ADD CONSTRAINT "bounce_inbox_p_k" PRIMARY KEY ("bounce_inbox_id");

-- ----------------------------
-- Uniques structure for table code_email
-- ----------------------------
ALTER TABLE "public"."code_email" ADD CONSTRAINT "code_email_unq" UNIQUE ("code_email", "flagtrans");

-- ----------------------------
-- Primary Key structure for table code_email
-- ----------------------------
ALTER TABLE "public"."code_email" ADD CONSTRAINT "code_email_p_k" PRIMARY KEY ("code_email_id");

-- ----------------------------
-- Indexes structure for table detail
-- ----------------------------
CREATE INDEX "detail_index" ON "public"."detail" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "flagtrans" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nama_file" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);
CREATE INDEX "idx_detail" ON "public"."detail" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "flagtrans" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);
CREATE INDEX "idx_detail_loading_id" ON "public"."detail" USING btree (
  "m_loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
CREATE INDEX "norek_index" ON "public"."detail" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table detail
-- ----------------------------
ALTER TABLE "public"."detail" ADD CONSTRAINT "detail_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_032025_bc
-- ----------------------------
ALTER TABLE "public"."detail_032025_bc" ADD CONSTRAINT "detail_032025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_032025_bc
-- ----------------------------
ALTER TABLE "public"."detail_032025_bc" ADD CONSTRAINT "detail_032025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_032025_co
-- ----------------------------
ALTER TABLE "public"."detail_032025_co" ADD CONSTRAINT "detail_032025_co_unique" UNIQUE ("nomor_rekening", "nama_file", "blth");

-- ----------------------------
-- Primary Key structure for table detail_032025_co
-- ----------------------------
ALTER TABLE "public"."detail_032025_co" ADD CONSTRAINT "detail_032025_co_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_032025_mt
-- ----------------------------
ALTER TABLE "public"."detail_032025_mt" ADD CONSTRAINT "detail_032025_mt_unique" UNIQUE ("nomor_rekening", "pdf_name");

-- ----------------------------
-- Primary Key structure for table detail_032025_mt
-- ----------------------------
ALTER TABLE "public"."detail_032025_mt" ADD CONSTRAINT "detail_032025_mt_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_052025_bc
-- ----------------------------
ALTER TABLE "public"."detail_052025_bc" ADD CONSTRAINT "detail_052025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_052025_bc
-- ----------------------------
ALTER TABLE "public"."detail_052025_bc" ADD CONSTRAINT "detail_052025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_052025_co
-- ----------------------------
ALTER TABLE "public"."detail_052025_co" ADD CONSTRAINT "detail_052025_co_unique" UNIQUE ("nomor_rekening", "nama_file", "blth");

-- ----------------------------
-- Primary Key structure for table detail_052025_co
-- ----------------------------
ALTER TABLE "public"."detail_052025_co" ADD CONSTRAINT "detail_052025_co_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_072025_bc
-- ----------------------------
ALTER TABLE "public"."detail_072025_bc" ADD CONSTRAINT "detail_072025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_072025_bc
-- ----------------------------
ALTER TABLE "public"."detail_072025_bc" ADD CONSTRAINT "detail_072025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_082025_bc
-- ----------------------------
ALTER TABLE "public"."detail_082025_bc" ADD CONSTRAINT "detail_082025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_082025_bc
-- ----------------------------
ALTER TABLE "public"."detail_082025_bc" ADD CONSTRAINT "detail_082025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_102025_bc
-- ----------------------------
ALTER TABLE "public"."detail_102025_bc" ADD CONSTRAINT "detail_102025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_102025_bc
-- ----------------------------
ALTER TABLE "public"."detail_102025_bc" ADD CONSTRAINT "detail_102025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_102025_co
-- ----------------------------
ALTER TABLE "public"."detail_102025_co" ADD CONSTRAINT "detail_102025_co_unique" UNIQUE ("nomor_rekening", "nama_file", "blth");

-- ----------------------------
-- Primary Key structure for table detail_102025_co
-- ----------------------------
ALTER TABLE "public"."detail_102025_co" ADD CONSTRAINT "detail_102025_co_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_102025_mt
-- ----------------------------
ALTER TABLE "public"."detail_102025_mt" ADD CONSTRAINT "detail_102025_mt_unique" UNIQUE ("nomor_rekening", "pdf_name");

-- ----------------------------
-- Primary Key structure for table detail_102025_mt
-- ----------------------------
ALTER TABLE "public"."detail_102025_mt" ADD CONSTRAINT "detail_102025_mt_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_112025_bc
-- ----------------------------
ALTER TABLE "public"."detail_112025_bc" ADD CONSTRAINT "detail_112025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_112025_bc
-- ----------------------------
ALTER TABLE "public"."detail_112025_bc" ADD CONSTRAINT "detail_112025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table detail_122025_bc
-- ----------------------------
ALTER TABLE "public"."detail_122025_bc" ADD CONSTRAINT "detail_122025_bc_unique" UNIQUE ("nomor_rekening", "m_loading_id");

-- ----------------------------
-- Primary Key structure for table detail_122025_bc
-- ----------------------------
ALTER TABLE "public"."detail_122025_bc" ADD CONSTRAINT "detail_122025_bc_p_k" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Indexes structure for table detail_cetak
-- ----------------------------
CREATE INDEX "detail_cetak_index" ON "public"."detail_cetak" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "flagtrans" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nama_file" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table detail_cetak
-- ----------------------------
ALTER TABLE "public"."detail_cetak" ADD CONSTRAINT "detail_cetak_pk" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Uniques structure for table jobs_loading_detail_non_ematerai
-- ----------------------------
ALTER TABLE "public"."jobs_loading_detail_non_ematerai" ADD CONSTRAINT "jobs_loading_detail_non_ematerai_job_id_key" UNIQUE ("job_id");

-- ----------------------------
-- Primary Key structure for table jobs_loading_detail_non_ematerai
-- ----------------------------
ALTER TABLE "public"."jobs_loading_detail_non_ematerai" ADD CONSTRAINT "jobs_loading_detail_non_ematerai_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table kalender
-- ----------------------------
CREATE INDEX "idx_kalender_tgl" ON "public"."kalender" USING btree (
  "tanggal" "pg_catalog"."timestamp_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table kalender
-- ----------------------------
ALTER TABLE "public"."kalender" ADD CONSTRAINT "pk_kalender" PRIMARY KEY ("tanggal");

-- ----------------------------
-- Primary Key structure for table log_approval
-- ----------------------------
ALTER TABLE "public"."log_approval" ADD CONSTRAINT "log_approval_id_pk" PRIMARY KEY ("log_approval_id");

-- ----------------------------
-- Primary Key structure for table log_customer
-- ----------------------------
ALTER TABLE "public"."log_customer" ADD CONSTRAINT "log_customer_p_k" PRIMARY KEY ("log_customer_id");

-- ----------------------------
-- Primary Key structure for table log_customer_copy1
-- ----------------------------
ALTER TABLE "public"."log_customer_copy1" ADD CONSTRAINT "log_customer_copy1_pkey" PRIMARY KEY ("log_customer_id");

-- ----------------------------
-- Primary Key structure for table log_customer_copy2
-- ----------------------------
ALTER TABLE "public"."log_customer_copy2" ADD CONSTRAINT "log_customer_copy2_pkey" PRIMARY KEY ("log_customer_id");

-- ----------------------------
-- Primary Key structure for table log_customer_ku
-- ----------------------------
ALTER TABLE "public"."log_customer_ku" ADD CONSTRAINT "log_customer_ku_p_k" PRIMARY KEY ("log_customer_ku_id");

-- ----------------------------
-- Indexes structure for table log_hapusdata
-- ----------------------------
CREATE INDEX "idx_periode_produk" ON "public"."log_hapusdata" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "produk_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table m_attach_file
-- ----------------------------
ALTER TABLE "public"."m_attach_file" ADD CONSTRAINT "pk_m_attach_file" PRIMARY KEY ("m_attach_file_id");

-- ----------------------------
-- Indexes structure for table m_customer
-- ----------------------------
CREATE INDEX "m_customer_index" ON "public"."m_customer" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "flagtrans" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table m_customer
-- ----------------------------
ALTER TABLE "public"."m_customer" ADD CONSTRAINT "m_customer_p_key" PRIMARY KEY ("m_customer_id");

-- ----------------------------
-- Primary Key structure for table m_customer_ku
-- ----------------------------
ALTER TABLE "public"."m_customer_ku" ADD CONSTRAINT "m_customer_ku_p_key" PRIMARY KEY ("m_customer_ku_id");

-- ----------------------------
-- Indexes structure for table m_customer_x
-- ----------------------------
CREATE INDEX "m_customer_x_index" ON "public"."m_customer_x" USING btree (
  "blth" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "flagtrans" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table m_customer_x
-- ----------------------------
ALTER TABLE "public"."m_customer_x" ADD CONSTRAINT "m_customer_x_p_key" PRIMARY KEY ("m_customer_id");

-- ----------------------------
-- Uniques structure for table m_jadwal
-- ----------------------------
ALTER TABLE "public"."m_jadwal" ADD CONSTRAINT "m_jadwal_unq" UNIQUE ("tgl_jadwal", "flagtrans");

-- ----------------------------
-- Primary Key structure for table m_jadwal
-- ----------------------------
ALTER TABLE "public"."m_jadwal" ADD CONSTRAINT "m_jadwal_p_k" PRIMARY KEY ("jadwal_id");

-- ----------------------------
-- Primary Key structure for table m_loading
-- ----------------------------
ALTER TABLE "public"."m_loading" ADD CONSTRAINT "m_loading_p_k" PRIMARY KEY ("m_loading_id");

-- ----------------------------
-- Primary Key structure for table mail_server
-- ----------------------------
ALTER TABLE "public"."mail_server" ADD CONSTRAINT "mail_server_p_k" PRIMARY KEY ("mail_server_id");

-- ----------------------------
-- Primary Key structure for table master_hana_bank
-- ----------------------------
ALTER TABLE "public"."master_hana_bank" ADD CONSTRAINT "master_hana_bank_pkey" PRIMARY KEY ("id_master_hana_bank");

-- ----------------------------
-- Primary Key structure for table master_kurir
-- ----------------------------
ALTER TABLE "public"."master_kurir" ADD CONSTRAINT "master_kurir_pkey" PRIMARY KEY ("id_master_kurir");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai" ADD CONSTRAINT "pk_master_proses_after_ematerai" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_file
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_file" ADD CONSTRAINT "pk_master_proses_after_ematerai_file" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_file_ce_from_bri
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_file_ce_from_bri" ADD CONSTRAINT "pk_master_proses_after_ematerai_file_ce_from_bri" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_file_ce_from_produksi
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_file_ce_from_produksi" ADD CONSTRAINT "pk_master_proses_after_ematerai_file_ce_from_produksi" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_file_ce_from_produksi_ocr
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_file_ce_from_produksi_ocr" ADD CONSTRAINT "pk_master_proses_after_ematerai_file_ce_from_produksi_ocr" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_produksi
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_produksi" ADD CONSTRAINT "pk_master_proses_after_ematerai_produksi" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table master_proses_after_ematerai_produksi_copy1
-- ----------------------------
ALTER TABLE "public"."master_proses_after_ematerai_produksi_copy1" ADD CONSTRAINT "master_proses_after_ematerai_produksi_copy1_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table menu1
-- ----------------------------
ALTER TABLE "public"."menu1" ADD CONSTRAINT "menu1_pkey" PRIMARY KEY ("menu", "flagtrans");

-- ----------------------------
-- Primary Key structure for table menugroup1
-- ----------------------------
ALTER TABLE "public"."menugroup1" ADD CONSTRAINT "menugroup1_pkey" PRIMARY KEY ("menugroupid");

-- ----------------------------
-- Primary Key structure for table mproduk
-- ----------------------------
ALTER TABLE "public"."mproduk" ADD CONSTRAINT "p_product" PRIMARY KEY ("flagtrans");

-- ----------------------------
-- Uniques structure for table pdf_jobs
-- ----------------------------
ALTER TABLE "public"."pdf_jobs" ADD CONSTRAINT "pdf_jobs_job_id_key" UNIQUE ("job_id");

-- ----------------------------
-- Primary Key structure for table pdf_jobs
-- ----------------------------
ALTER TABLE "public"."pdf_jobs" ADD CONSTRAINT "pdf_jobs_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table pdf_queue
-- ----------------------------
CREATE INDEX "idx_pdf_queue_job_status" ON "public"."pdf_queue" USING btree (
  "job_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "status" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table pdf_queue
-- ----------------------------
ALTER TABLE "public"."pdf_queue" ADD CONSTRAINT "pdf_queue_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table read_email
-- ----------------------------
CREATE INDEX "read_id" ON "public"."read_email" USING btree (
  "tr_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table read_email
-- ----------------------------
ALTER TABLE "public"."read_email" ADD CONSTRAINT "read_email_pk" PRIMARY KEY ("read_email_id");

-- ----------------------------
-- Primary Key structure for table sample_email
-- ----------------------------
ALTER TABLE "public"."sample_email" ADD CONSTRAINT "sample_email_p_k" PRIMARY KEY ("email_sample");

-- ----------------------------
-- Primary Key structure for table tampung
-- ----------------------------
ALTER TABLE "public"."tampung" ADD CONSTRAINT "tampung_pk" PRIMARY KEY ("tampung_id");

-- ----------------------------
-- Indexes structure for table template_email
-- ----------------------------
CREATE UNIQUE INDEX "idx_template_email" ON "public"."template_email" USING btree (
  "template_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Uniques structure for table template_email
-- ----------------------------
ALTER TABLE "public"."template_email" ADD CONSTRAINT "template_email_unq" UNIQUE ("nama_template", "flagtrans");

-- ----------------------------
-- Primary Key structure for table template_email
-- ----------------------------
ALTER TABLE "public"."template_email" ADD CONSTRAINT "template_email_p_k" PRIMARY KEY ("template_email_id");

-- ----------------------------
-- Indexes structure for table template_email_copy1
-- ----------------------------
CREATE UNIQUE INDEX "idx_template_email_copy1" ON "public"."template_email_copy1" USING btree (
  "template_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Uniques structure for table template_email_copy1
-- ----------------------------
ALTER TABLE "public"."template_email_copy1" ADD CONSTRAINT "template_email_copy1_nama_template_flagtrans_key" UNIQUE ("nama_template", "flagtrans");

-- ----------------------------
-- Primary Key structure for table template_email_copy1
-- ----------------------------
ALTER TABLE "public"."template_email_copy1" ADD CONSTRAINT "template_email_copy1_pkey" PRIMARY KEY ("template_email_id");

-- ----------------------------
-- Indexes structure for table template_email_copy2
-- ----------------------------
CREATE UNIQUE INDEX "idx_template_email_copy2" ON "public"."template_email_copy2" USING btree (
  "template_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Uniques structure for table template_email_copy2
-- ----------------------------
ALTER TABLE "public"."template_email_copy2" ADD CONSTRAINT "template_email_copy2_nama_template_flagtrans_key" UNIQUE ("nama_template", "flagtrans");

-- ----------------------------
-- Primary Key structure for table template_email_copy2
-- ----------------------------
ALTER TABLE "public"."template_email_copy2" ADD CONSTRAINT "template_email_copy2_pkey" PRIMARY KEY ("template_email_id");

-- ----------------------------
-- Indexes structure for table template_email_copy3
-- ----------------------------
CREATE UNIQUE INDEX "idx_template_email_copy3" ON "public"."template_email_copy3" USING btree (
  "template_email_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);

-- ----------------------------
-- Uniques structure for table template_email_copy3
-- ----------------------------
ALTER TABLE "public"."template_email_copy3" ADD CONSTRAINT "template_email_copy3_nama_template_flagtrans_key" UNIQUE ("nama_template", "flagtrans");

-- ----------------------------
-- Primary Key structure for table template_email_copy3
-- ----------------------------
ALTER TABLE "public"."template_email_copy3" ADD CONSTRAINT "template_email_copy3_pkey" PRIMARY KEY ("template_email_id");

-- ----------------------------
-- Primary Key structure for table tmp_cek_embos
-- ----------------------------
ALTER TABLE "public"."tmp_cek_embos" ADD CONSTRAINT "tmp_cek_embos_pkey" PRIMARY KEY ("cardno");

-- ----------------------------
-- Primary Key structure for table tmp_cek_embos_copy1
-- ----------------------------
ALTER TABLE "public"."tmp_cek_embos_copy1" ADD CONSTRAINT "tmp_cek_embos_copy1_pkey" PRIMARY KEY ("cardno");

-- ----------------------------
-- Primary Key structure for table tmp_excel_bri_corp1
-- ----------------------------
ALTER TABLE "public"."tmp_excel_bri_corp1" ADD CONSTRAINT "tmp_excel_bri_corp1_pkey" PRIMARY KEY ("id_tmp_excel_bri_corp");

-- ----------------------------
-- Primary Key structure for table tmp_excel_bri_corp2
-- ----------------------------
ALTER TABLE "public"."tmp_excel_bri_corp2" ADD CONSTRAINT "tmp_excel_bri_corp2_pkey" PRIMARY KEY ("id_tmp_excel_bri_corp");

-- ----------------------------
-- Primary Key structure for table tmp_excel_bri_corp3
-- ----------------------------
ALTER TABLE "public"."tmp_excel_bri_corp3" ADD CONSTRAINT "tmp_excel_bri_corp3_pkey" PRIMARY KEY ("id_tmp_excel_bri_corp");

-- ----------------------------
-- Primary Key structure for table tmp_excel_hana_bank
-- ----------------------------
ALTER TABLE "public"."tmp_excel_hana_bank" ADD CONSTRAINT "tmp_excel_hana_bank_pkey" PRIMARY KEY ("id_tmp_excel_hana_bank");

-- ----------------------------
-- Indexes structure for table tmp_split
-- ----------------------------
CREATE INDEX "idx_tmp_split" ON "public"."tmp_split" USING btree (
  "nomor_customer" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tmp_split
-- ----------------------------
ALTER TABLE "public"."tmp_split" ADD CONSTRAINT "pk_tmp_split" PRIMARY KEY ("detail_id");

-- ----------------------------
-- Indexes structure for table tr_email
-- ----------------------------
CREATE INDEX "idx_tr_email2_loading_id" ON "public"."tr_email" USING btree (
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
CREATE INDEX "idx_tremail2" ON "public"."tr_email" USING btree (
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email
-- ----------------------------
ALTER TABLE "public"."tr_email" ADD CONSTRAINT "tr_email2_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_032025_bc
-- ----------------------------
CREATE INDEX "tr_email_032025_bc_index" ON "public"."tr_email_032025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_032025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_032025_bc" ADD CONSTRAINT "tr_email_032025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_032025_co
-- ----------------------------
CREATE INDEX "tr_email_032025_co_index" ON "public"."tr_email_032025_co" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_032025_co
-- ----------------------------
ALTER TABLE "public"."tr_email_032025_co" ADD CONSTRAINT "tr_email_032025_co_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_032025_mt
-- ----------------------------
CREATE INDEX "tr_email_032025_mt_index" ON "public"."tr_email_032025_mt" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_032025_mt
-- ----------------------------
ALTER TABLE "public"."tr_email_032025_mt" ADD CONSTRAINT "tr_email_032025_mt_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_052025_bc
-- ----------------------------
CREATE INDEX "tr_email_052025_bc_index" ON "public"."tr_email_052025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_052025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_052025_bc" ADD CONSTRAINT "tr_email_052025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_052025_co
-- ----------------------------
CREATE INDEX "tr_email_052025_co_index" ON "public"."tr_email_052025_co" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_052025_co
-- ----------------------------
ALTER TABLE "public"."tr_email_052025_co" ADD CONSTRAINT "tr_email_052025_co_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_072025_bc
-- ----------------------------
CREATE INDEX "tr_email_072025_bc_index" ON "public"."tr_email_072025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_072025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_072025_bc" ADD CONSTRAINT "tr_email_072025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_082025_bc
-- ----------------------------
CREATE INDEX "tr_email_082025_bc_index" ON "public"."tr_email_082025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_082025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_082025_bc" ADD CONSTRAINT "tr_email_082025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_102025_bc
-- ----------------------------
CREATE INDEX "tr_email_102025_bc_index" ON "public"."tr_email_102025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_102025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_102025_bc" ADD CONSTRAINT "tr_email_102025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_102025_co
-- ----------------------------
CREATE INDEX "tr_email_102025_co_index" ON "public"."tr_email_102025_co" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_102025_co
-- ----------------------------
ALTER TABLE "public"."tr_email_102025_co" ADD CONSTRAINT "tr_email_102025_co_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_102025_mt
-- ----------------------------
CREATE INDEX "tr_email_102025_mt_index" ON "public"."tr_email_102025_mt" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_102025_mt
-- ----------------------------
ALTER TABLE "public"."tr_email_102025_mt" ADD CONSTRAINT "tr_email_102025_mt_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_112025_bc
-- ----------------------------
CREATE INDEX "tr_email_112025_bc_index" ON "public"."tr_email_112025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_112025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_112025_bc" ADD CONSTRAINT "tr_email_112025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Indexes structure for table tr_email_122025_bc
-- ----------------------------
CREATE INDEX "tr_email_122025_bc_index" ON "public"."tr_email_122025_bc" USING btree (
  "nomor_rekening" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "loading_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table tr_email_122025_bc
-- ----------------------------
ALTER TABLE "public"."tr_email_122025_bc" ADD CONSTRAINT "tr_email_122025_bc_p_k" PRIMARY KEY ("tr_email_id");

-- ----------------------------
-- Primary Key structure for table tr_email_history
-- ----------------------------
ALTER TABLE "public"."tr_email_history" ADD CONSTRAINT "tr_email_history_p_k" PRIMARY KEY ("tr_email_history_id");

-- ----------------------------
-- Primary Key structure for table user1
-- ----------------------------
ALTER TABLE "public"."user1" ADD CONSTRAINT "user1_pkey" PRIMARY KEY ("userid");

-- ----------------------------
-- Uniques structure for table usermenu1
-- ----------------------------
ALTER TABLE "public"."usermenu1" ADD CONSTRAINT "usermenu1_userid_key" UNIQUE ("userid", "menuid");

-- ----------------------------
-- View structure for mst_produk
-- ----------------------------
DROP VIEW IF EXISTS "public"."mst_produk";
CREATE VIEW "public"."mst_produk" AS  SELECT 'e-BRI'::text AS kodeaplikasi, 'B-BRI-EST'::text AS kodeproduk, mproduk.flagtrans, mproduk.produk, mproduk.produk AS mdescription, '21'::text AS aplikasi_id, '4'::text AS group_aplikasi_id
   FROM mproduk;

-- ----------------------------
-- View structure for tablesize
-- ----------------------------
DROP VIEW IF EXISTS "public"."tablesize";
CREATE VIEW "public"."tablesize" AS  SELECT (tables.table_schema::text || '.'::text) || tables.table_name::text AS table_full_name, pg_size_pretty(pg_total_relation_size((((('"'::text || tables.table_schema::text) || '"."'::text) || tables.table_name::text) || '"'::text)::regclass)) AS size
   FROM information_schema.tables
  ORDER BY pg_total_relation_size((((('"'::text || tables.table_schema::text) || '"."'::text) || tables.table_name::text) || '"'::text)::regclass) DESC;

-- ----------------------------
-- View structure for vw_030325010824
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_030325010824";
CREATE VIEW "public"."vw_030325010824" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email_032025_co a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'PTSTMTC_020325.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_110620010933
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_110620010933";
CREATE VIEW "public"."vw_110620010933" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'DUMMY_BROSUR_BRI_CARD_202005013.dat'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_110620011028
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_110620011028";
CREATE VIEW "public"."vw_110620011028" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'DUMMY_BROSUR_BRI_CARD_202005013.dat'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_130520011323
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_130520011323";
CREATE VIEW "public"."vw_130520011323" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC100520.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_130520011505
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_130520011505";
CREATE VIEW "public"."vw_130520011505" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC100520.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_130520012029
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_130520012029";
CREATE VIEW "public"."vw_130520012029" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC100520.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_130520012406
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_130520012406";
CREATE VIEW "public"."vw_130520012406" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC100520.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_130520012532
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_130520012532";
CREATE VIEW "public"."vw_130520012532" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC100520.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_270420022856
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_270420022856";
CREATE VIEW "public"."vw_270420022856" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC250420.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_270420034534
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_270420034534";
CREATE VIEW "public"."vw_270420034534" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC250420.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_270420035339
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_270420035339";
CREATE VIEW "public"."vw_270420035339" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC250420.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_270420035443
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_270420035443";
CREATE VIEW "public"."vw_270420035443" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC250420.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_270420042540
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_270420042540";
CREATE VIEW "public"."vw_270420042540" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC250420.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_300620123911
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_300620123911";
CREATE VIEW "public"."vw_300620123911" AS  SELECT a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
   FROM tr_email a
   LEFT JOIN m_loading b ON b.m_loading_id = a.loading_id
  WHERE b.loading_file::text = 'EST_BRIC030620.DAT'::text AND a.status_sample = false;

-- ----------------------------
-- View structure for vw_email
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_email";
CREATE VIEW "public"."vw_email" AS         (         SELECT a.nomor_rekening, a.email1 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.flagtrans::text = b.flagtrans::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email1 IS NOT NULL
        UNION 
                 SELECT a.nomor_rekening, a.email2 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.flagtrans::text = b.flagtrans::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email2 IS NOT NULL)
UNION 
         SELECT a.nomor_rekening, a.email3 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans
           FROM m_customer a
      JOIN detail b ON a.blth::text = b.blth::text AND a.flagtrans::text = b.flagtrans::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email3 IS NOT NULL;

-- ----------------------------
-- View structure for vw_email_bayar
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_email_bayar";
CREATE VIEW "public"."vw_email_bayar" AS         (         SELECT a.nomor_rekening, a.email1 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email1 IS NOT NULL AND b.jml_hlm > 4
        UNION 
                 SELECT a.nomor_rekening, a.email2 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email2 IS NOT NULL AND b.jml_hlm > 4)
UNION 
         SELECT a.nomor_rekening, a.email3 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
           FROM m_customer a
      JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email3 IS NOT NULL AND b.jml_hlm > 4;

-- ----------------------------
-- View structure for vw_email_gratis
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_email_gratis";
CREATE VIEW "public"."vw_email_gratis" AS         (         SELECT a.nomor_rekening, a.email1 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email1 IS NOT NULL AND b.jml_hlm <= 4
        UNION 
                 SELECT a.nomor_rekening, a.email2 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
                   FROM m_customer a
              JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email2 IS NOT NULL AND b.jml_hlm <= 4)
UNION 
         SELECT a.nomor_rekening, a.email3 AS email, b.nama, b.m_loading_id, b.nama_file, b.blth, b.flagtrans, b.jml_hlm, b.pdf_name
           FROM m_customer a
      JOIN detail b ON a.blth::text = b.blth::text AND a.nomor_rekening::text = b.nomor_rekening::text AND a.email3 IS NOT NULL AND b.jml_hlm <= 4;

-- ----------------------------
-- View structure for vw_read_email_first
-- ----------------------------
DROP VIEW IF EXISTS "public"."vw_read_email_first";
CREATE VIEW "public"."vw_read_email_first" AS  SELECT read_email.tr_email_id, min(read_email.read_email_id) AS read_email_id_min, count(*) AS jum_read
   FROM read_email
  GROUP BY read_email.tr_email_id
  ORDER BY read_email.tr_email_id;
