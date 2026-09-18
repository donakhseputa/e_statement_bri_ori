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
