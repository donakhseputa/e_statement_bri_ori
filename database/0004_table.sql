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
