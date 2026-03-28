<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// ─── Daftar pengguna (username => [password, role, nama_lengkap]) ───
$USERS = [
    'UPT RSBG TUBAN'   => ['password' => 'admin123',   'role' => 'admin',   'nama' => 'Administrator'],
    'pegawai' => ['password' => 'pegawai123', 'role' => 'pegawai', 'nama' => 'Shelshi'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (isset($USERS[$username]) && $USERS[$username]['password'] === $password) {
        $_SESSION['logged_in']  = true;
        $_SESSION['username']   = $username;
        $_SESSION['role']       = $USERS[$username]['role'];
        $_SESSION['nama']       = $USERS[$username]['nama'];
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SIPUA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --navy:#1a2d6b;--navy2:#1e3275;--accent:#2563eb;
            --font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;
        }
        body{
            font-family:var(--font);
            background:linear-gradient(135deg,#0f172a 0%,var(--navy) 45%,#243d90 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            position:relative;overflow:hidden;
        }
        /* Grid pattern background */
        body::before{
            content:'';position:absolute;inset:0;
            background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
            background-size:40px 40px;pointer-events:none;
        }
        /* Glow blobs */
        body::after{
            content:'';position:absolute;width:500px;height:500px;
            background:radial-gradient(circle,rgba(37,99,235,.25),transparent 70%);
            top:-100px;right:-100px;pointer-events:none;
        }
        .login-wrap{
            position:relative;z-index:1;
            background:rgba(255,255,255,.97);
            border-radius:20px;padding:44px 40px 36px;
            width:100%;max-width:420px;
            box-shadow:0 32px 80px rgba(0,0,0,.35),0 0 0 1px rgba(255,255,255,.1);
        }
        .logo-area{text-align:center;margin-bottom:28px;}
        .logo-area img{width:72px;height:72px;object-fit:contain;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;}
        .logo-area .app-name{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-.5px;}
        .logo-area .app-sub{font-size:12px;color:#64748b;margin-top:3px;line-height:1.4;}
        .divider{height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin:0 0 24px;}
        .role-tabs{display:flex;gap:6px;margin-bottom:22px;background:#f1f5f9;border-radius:10px;padding:4px;}
        .role-tab{
            flex:1;padding:8px 0;border:none;border-radius:7px;
            font-family:var(--font);font-size:12px;font-weight:600;cursor:pointer;
            background:none;color:#64748b;transition:all .2s;
        }
        .role-tab.active{background:#fff;color:var(--navy);box-shadow:0 1px 4px rgba(0,0,0,.1);}
        .form-group{margin-bottom:16px;}
        label{display:block;margin-bottom:5px;font-size:12px;font-weight:700;color:#374151;letter-spacing:.3px;text-transform:uppercase;}
        .input-wrap{position:relative;}
        .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;}
        input[type="text"],input[type="password"]{
            width:100%;padding:11px 13px 11px 38px;
            border:2px solid #e2e8f0;border-radius:9px;
            font-family:var(--font);font-size:13.5px;outline:none;
            background:#f8fafc;color:#0f172a;transition:all .18s;
        }
        input[type="text"]:focus,input[type="password"]:focus{
            border-color:var(--accent);background:#fff;
            box-shadow:0 0 0 3px rgba(37,99,235,.1);
        }
        .pw-toggle{
            position:absolute;right:12px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;font-size:14px;
            color:#94a3b8;padding:4px;
        }
        .btn-login{
            width:100%;padding:13px;margin-top:6px;
            background:linear-gradient(135deg,var(--navy),var(--accent));
            color:#fff;border:none;border-radius:10px;
            font-family:var(--font);font-size:14px;font-weight:700;
            cursor:pointer;letter-spacing:.3px;
            transition:transform .12s,box-shadow .2s;
            box-shadow:0 4px 16px rgba(37,99,235,.35);
        }
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(37,99,235,.45);}
        .btn-login:active{transform:translateY(0);}
        .error-msg{
            background:#fff5f5;color:#991b1b;border:1.5px solid #fca5a5;
            border-radius:9px;padding:11px 14px;margin-bottom:18px;
            font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:8px;
        }
        .hint-box{
            background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:9px;
            padding:10px 14px;margin-bottom:18px;font-size:11.5px;color:#1e40af;
        }
        .hint-box b{font-family:var(--mono);}
        .footer-text{text-align:center;margin-top:20px;font-size:11px;color:#94a3b8;}
        .badge-role{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;margin-bottom:14px;}
        .badge-admin{background:#dbeafe;color:#1e40af;}
        .badge-pegawai{background:#dcfce7;color:#166534;}
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="logo-area">
        <img src="img/LOGO PEMPROV.png" onerror="this.style.display='none'">
        <div class="app-name">SIPUA</div>
        <div class="app-sub">Sistem Usulan Anggaran<br>UPT Rehabilitasi Sosial Bina Grahita Tuban</div>
    </div>
    <div class="divider"></div>

    <!-- Tab Pilih Role (visual saja, login otomatis sesuai akun) -->
    <div class="role-tabs" id="roleTabs">
        <button class="role-tab active" onclick="switchTab('admin',this)">🔐 Admin</button>
        <button class="role-tab" onclick="switchTab('pegawai',this)">👤 Pegawai</button>
    </div>

    <div id="hintAdmin" class="hint-box">
        Akses penuh termasuk <b>Import RKA</b>, kelola data master &amp; laporan.
    </div>
    <div id="hintPegawai" class="hint-box" style="display:none;">
        Akses pengajuan usulan bulanan &amp; laporan. Import RKA tidak tersedia.
    </div>

    <?php if ($error): ?>
        <div class="error-msg">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label>Username</label>
            <div class="input-wrap">
                <span class="input-icon">👤</span>
                <input type="text" name="username" id="usernameInput" placeholder="Masukkan username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autocomplete="username" required>
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
                <span class="input-icon">🔑</span>
                <input type="password" name="password" id="pwInput" placeholder="Masukkan password" autocomplete="current-password" required>
                <button type="button" class="pw-toggle" onclick="togglePw()">👁️</button>
            </div>
        </div>
        <button type="submit" class="btn-login">🔐 Masuk ke SIPUA</button>
    </form>
    <div class="footer-text">SIPUA v1 © 2026 · TA 2026</div>
</div>

<script>
function switchTab(role, btn) {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('hintAdmin').style.display  = role === 'admin'   ? '' : 'none';
    document.getElementById('hintPegawai').style.display = role === 'pegawai' ? '' : 'none';
    // Pre-fill username sesuai tab sebagai bantuan
    const inp = document.getElementById('usernameInput');
    if (!inp.value) inp.value = role === 'admin' ? 'admin' : '';
    inp.focus();
}
function togglePw() {
    const inp = document.getElementById('pwInput');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>