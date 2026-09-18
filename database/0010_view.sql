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
