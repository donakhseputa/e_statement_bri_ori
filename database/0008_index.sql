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
