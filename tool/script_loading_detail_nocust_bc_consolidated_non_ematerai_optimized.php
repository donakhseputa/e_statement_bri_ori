<?php
// script_loading_detail_nocust_bc_consolidated_non_ematerai_optimized.php

require_once 'tcpdf_include.php';   // pastikan include sesuai di sistem kamu
require_once 'koneksi.php';         // koneksi PG

// ===============================================
// ENTRY POINT (dipanggil via ajax)
// ===============================================

$flagtrans = $_POST['flagtrans'] ?? $_POST['pr'] ?? '';
$menu_id   = $_POST['menu_id']   ?? '';
$blth      = $_POST['blth']      ?? '';
$file      = $_POST['file']      ?? '';

$waktu_awal = microtime(true);

// Cek apakah file sudah pernah di-load
$sql_cek_dat = "
    SELECT 1 
    FROM m_loading 
    WHERE flagtrans = $1 
      AND blth      = $2 
      AND loading_file = $3
    LIMIT 1
";

$qry_cek_dat = pg_query_params($conn, $sql_cek_dat, [$flagtrans, $blth, $file]);

if (!$qry_cek_dat) {
    die('ERROR cek m_loading: ' . pg_last_error($conn));
}

$jml = pg_num_rows($qry_cek_dat);

if ($jml > 0) {
    $msg   = 'ERROR: file sudah pernah di-upload';
    $waktu = microtime(true) - $waktu_awal;
    $waktu = (int) $waktu;
    echo $msg . '|' . $waktu . '|' . $flagtrans;
    exit;
}

// Kalau sampai sini, file belum pernah diproses → jalankan proses PDF
list($pesan, $jml_record, $rekening_tanpa_email_str) = dat_to_pdf_optimized(
    $conn,
    $flagtrans,
    $menu_id,
    $blth,
    $file
);

$waktu = microtime(true) - $waktu_awal;
$waktu = (int) $waktu;

// Format output: pesan|jumlah_record|rekening_tanpa_email|waktu|flagtrans
echo $pesan . '|' . $jml_record . '|' . $rekening_tanpa_email_str . '|' . $waktu . '|' . $flagtrans;
exit;


// =====================================================
// FUNGSI UTAMA: dat_to_pdf_optimized
//   - Stream 1 query besar
//   - 1 PDF per rekening
//   - Hemat memori, lebih cepat
// =====================================================

/**
 * @return array [$pesan, $jml_record, $rekening_tanpa_email_str]
 */
function dat_to_pdf_optimized($conn, $flagtrans, $menu_id, $blth, $file)
{
    $total_record            = 0;
    $rekening_tanpa_email    = [];
    $current_account_no      = null;
    $current_account_email   = null;
    $current_account_rows    = [];
    $pdf                     = null;

    // Lokasi output PDF
    $output_base_dir = __DIR__ . "/pdf_output/$blth";
    if (!is_dir($output_base_dir)) {
        mkdir($output_base_dir, 0775, true);
    }

    // =======================================
    // 1 query besar: ambil semua transaksi
    // =======================================
    // GANTI nama_tabel_transaksi dan kolom sesuai sistem kamu:
    $sql = "
        SELECT 
            norekening,
            email,
            tgl_transaksi,
            keterangan,
            mutasi_debet,
            mutasi_kredit,
            saldo_akhir,
            -- kolom lain yang dibutuhkan layout
            *
        FROM nama_tabel_transaksi
        WHERE flagtrans = $1
          AND blth      = $2
          AND loading_file = $3
        ORDER BY norekening, tgl_transaksi, id
    ";

    $res = pg_query_params($conn, $sql, [$flagtrans, $blth, $file]);
    if (!$res) {
        throw new RuntimeException('ERROR query transaksi: ' . pg_last_error($conn));
    }

    // =======================================
    // LOOP STREAMING PER BARIS
    // =======================================
    while ($row = pg_fetch_assoc($res)) {
        $total_record++;

        $acc_no   = trim($row['norekening']);
        $acc_email= trim($row['email']);

        // Jika rekening berubah → tutup PDF sebelumnya, simpan ke disk
        if ($current_account_no !== null && $acc_no !== $current_account_no) {
            save_pdf_for_account(
                $pdf,
                $current_account_no,
                $current_account_email,
                $current_account_rows,
                $output_base_dir,
                $blth
            );

            // tracking rekening tanpa email
            if (empty($current_account_email)) {
                $rekening_tanpa_email[] = $current_account_no;
            }

            // reset buffer rekening sebelumnya
            $current_account_rows  = [];
            $pdf                   = null;
        }

        // Jika rekening baru dimulai
        if ($current_account_no === null || $acc_no !== $current_account_no) {
            $current_account_no    = $acc_no;
            $current_account_email = $acc_email;

            // Buat instance TCPDF baru untuk rekening ini
            $pdf = create_tcpdf_instance_for_statement();
        }

        // Tambahkan baris transaksi ke buffer rekening (bisa juga langsung tulis ke PDF)
        $current_account_rows[] = $row;
    }

    // =======================================
    // TANGANI REKENING TERAKHIR
    // =======================================
    if ($current_account_no !== null && !empty($current_account_rows)) {
        save_pdf_for_account(
            $pdf,
            $current_account_no,
            $current_account_email,
            $current_account_rows,
            $output_base_dir,
            $blth
        );

        if (empty($current_account_email)) {
            $rekening_tanpa_email[] = $current_account_no;
        }
    }

    // Format pesan dan list rekening tanpa email
    $pesan = "Proses impor dan generate PDF selesai";
    $rekening_tanpa_email_str = empty($rekening_tanpa_email)
        ? '-'
        : implode(', ', array_unique($rekening_tanpa_email));

    return [$pesan, $total_record, $rekening_tanpa_email_str];
}


// =====================================================
// Helper: Membuat TCPDF dengan setting standar e-statement
// Copy dari setting lama kamu
// =====================================================
function create_tcpdf_instance_for_statement()
{
    // TODO: sesuaikan dengan setting TCPDF lama di sistem kamu
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('E-Statement BRI');
    $pdf->SetAuthor('BRI');
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    return $pdf;
}

// =====================================================
// Helper: Simpan PDF per rekening
// Di sini kamu tempelkan layout TCPDF lama (header, body, footer)
// =====================================================
function save_pdf_for_account(
    ?TCPDF $pdf,
    string $account_no,
    ?string $email,
    array $rows,
    string $output_base_dir,
    string $blth
) {
    if ($pdf === null) {
        return;
    }

    // Tambah halaman
    $pdf->AddPage();

    // TODO: PANGGIL KODE LAYOUT LAMA DI SINI
    // - header rekening (nama nasabah, alamat, periode, dll)
    // - tabel transaksi
    // - footer
    //
    // Misalnya:
    // draw_statement_header($pdf, $account_no, $rows[0], $blth);
    // draw_statement_transactions($pdf, $rows);
    // draw_statement_footer($pdf);

    // =======================================
    // Penentuan nama file PDF
    // =======================================
    // Format nama file yang diinginkan bank:
    //   <norek>_<blth>.pdf  atau sesuai format aslinya
    // Misal blth = 202501 (Jan 2025)
    $filename = sprintf('%s_%s.pdf', $account_no, $blth);
    $filepath = $output_base_dir . '/' . $filename;

    // Simpan ke disk (arsip)
    $pdf->Output($filepath, 'F');

    // Bersihkan memory PDF instance
    $pdf->reset(); // jika ada
    // atau unset($pdf);  // tapi karena referensi pakai pointer, cukup biarkan caller yang override
}
