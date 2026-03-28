<?php
// ============================================================
//  api.php — Semua Endpoint API SIPUA
//  Cara akses:
//    GET  api.php?action=get_pagu      → ambil data pagu
//    GET  api.php?action=get_usulan    → ambil semua usulan
//    POST api.php?action=post_usulan   → simpan usulan baru
//    POST api.php?action=update_usulan → update status usulan
// ============================================================

require_once 'config.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    // ----------------------------------------------------------
    // GET: Ambil data pagu + sisa per program
    // ----------------------------------------------------------
    case 'get_pagu':
        $sql = "
            SELECT
                p.id,
                p.nama,
                p.pagu,
                COALESCE(SUM(
                    CASE WHEN u.status != 'ditolak' THEN u.total ELSE 0 END
                ), 0) AS terpakai
            FROM tbl_pagu p
            LEFT JOIN tbl_usulan u ON p.id = u.program_id
            WHERE p.tahun = 2025
            GROUP BY p.id, p.nama, p.pagu
            ORDER BY p.id
        ";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['pagu']     = (int)$row['pagu'];
            $row['terpakai'] = (int)$row['terpakai'];
            $row['sisa']     = $row['pagu'] - $row['terpakai'];
            $row['persen']   = $row['pagu'] > 0
                ? round(($row['terpakai'] / $row['pagu']) * 100, 1)
                : 0;
        }

        echo json_encode(['success' => true, 'data' => $rows]);
        break;

    // ----------------------------------------------------------
    // GET: Ambil semua usulan + nama program
    // ----------------------------------------------------------
    case 'get_usulan':
        $sql = "
            SELECT
                u.*,
                p.nama AS program_nama
            FROM tbl_usulan u
            JOIN tbl_pagu p ON u.program_id = p.id
            ORDER BY u.tgl_input DESC
        ";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        break;

    // ----------------------------------------------------------
    // POST: Simpan usulan baru + validasi pagu otomatis
    // ----------------------------------------------------------
    case 'post_usulan':
        $input     = json_decode(file_get_contents('php://input'), true);
        $nama      = trim($input['nama']       ?? '');
        $programId = trim($input['program_id'] ?? '');
        $volume    = (float)($input['volume']  ?? 0);
        $satuan    = trim($input['satuan']     ?? 'Unit');
        $harga     = (int)($input['harga']     ?? 0);
        $ket       = trim($input['keterangan'] ?? '');

        // Validasi field wajib
        if (!$nama || !$programId || $volume <= 0 || $harga <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $total = (int)($volume * $harga);

        // Hitung sisa pagu program yang dipilih
        $stmtSisa = $pdo->prepare("
            SELECT
                p.pagu,
                COALESCE(SUM(
                    CASE WHEN u.status != 'ditolak' THEN u.total ELSE 0 END
                ), 0) AS terpakai
            FROM tbl_pagu p
            LEFT JOIN tbl_usulan u ON p.id = u.program_id
            WHERE p.id = ?
            GROUP BY p.pagu
        ");
        $stmtSisa->execute([$programId]);
        $paguRow = $stmtSisa->fetch(PDO::FETCH_ASSOC);

        if (!$paguRow) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Program tidak ditemukan']);
            exit;
        }

        $sisa   = (int)$paguRow['pagu'] - (int)$paguRow['terpakai'];
        // Otomatis ditolak jika melebihi sisa pagu
        $status = ($total > $sisa) ? 'ditolak' : 'pending';

        // Simpan ke database
        $stmt = $pdo->prepare("
            INSERT INTO tbl_usulan
                (nama, program_id, volume, satuan, harga, total, keterangan, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nama, $programId, $volume, $satuan, $harga, $total, $ket, $status]);

        echo json_encode([
            'success'      => true,
            'status'       => $status,
            'sisa_setelah' => $status === 'pending' ? $sisa - $total : $sisa,
            'message'      => $status === 'ditolak'
                ? 'Usulan DITOLAK: melebihi sisa pagu Rp ' . number_format($sisa, 0, ',', '.')
                : 'Usulan berhasil diajukan'
        ]);
        break;

    // ----------------------------------------------------------
    // POST: Update status usulan (approve / tolak)
    // ----------------------------------------------------------
    case 'update_usulan':
        $input  = json_decode(file_get_contents('php://input'), true);
        $id     = (int)($input['id']     ?? 0);
        $status = $input['status'] ?? '';

        if (!$id || !in_array($status, ['pending', 'disetujui', 'ditolak'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE tbl_usulan SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        break;

    // ----------------------------------------------------------
    // Default: action tidak dikenal
    // ----------------------------------------------------------
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action tidak dikenal. Gunakan: get_pagu, get_usulan, post_usulan, update_usulan']);
        break;
}