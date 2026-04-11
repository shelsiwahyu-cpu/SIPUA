<?php
session_start();

// ─── Auth check ───────────────────────────────────────
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$role       = $_SESSION['role']     ?? 'pegawai';
$namaUser   = $_SESSION['nama']     ?? 'Pengguna';
$username   = $_SESSION['username'] ?? '';
$isAdmin    = ($role === 'admin');

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIPUA — UPT RSBG Tuban 2026</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
:root{
  --navy:#1a2d6b;--navy2:#1e3275;--navy3:#243d90;
  --accent:#2563eb;--accent2:#1d4ed8;--accent-light:#eff6ff;
  --orange:#f59e0b;--green:#059669;--teal:#0d9488;--red:#dc2626;
  --bg:#eef2f9;--surface:#fff;--surface2:#f5f8fc;--border:#dde6f0;
  --text:#0f172a;--muted:#64748b;--muted2:#94a3b8;
  --font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;
  --radius:12px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.05);
  --shadow-md:0 4px 20px rgba(0,0,0,.09);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:var(--font);min-height:100vh;font-size:14px;}
.sidebar{position:fixed;left:0;top:0;bottom:0;width:216px;background:linear-gradient(175deg,var(--navy),var(--navy2),var(--navy3));display:flex;flex-direction:column;z-index:200;box-shadow:4px 0 24px rgba(0,0,0,.2);}
.sb-logo{padding:18px 16px 14px;border-bottom:1px solid rgba(255,255,255,.08);}
.sb-logo .mark{width:34px;height:34px;background:linear-gradient(135deg,var(--accent),#60a5fa);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;margin-bottom:8px;}
.sb-logo h1{font-size:13.5px;font-weight:800;color:#fff;}
.sb-logo p{font-size:9px;color:rgba(255,255,255,.35);margin-top:1px;text-transform:uppercase;letter-spacing:.7px;}
.sb-logo .inst{font-size:9.5px;color:rgba(255,255,255,.55);margin-top:4px;font-weight:600;line-height:1.3;}
.nav-sec{padding:12px 12px 3px;font-size:9px;color:rgba(255,255,255,.28);font-weight:700;letter-spacing:1.3px;text-transform:uppercase;}
.nav-item{display:flex;align-items:center;gap:8px;padding:8px 13px;margin:1px 7px;border-radius:7px;font-size:12px;font-weight:500;color:rgba(255,255,255,.55);cursor:pointer;border:none;background:none;width:calc(100% - 14px);text-align:left;font-family:var(--font);transition:all .13s;}
.nav-item:hover{background:rgba(255,255,255,.08);color:#fff;}
.nav-item.active{background:rgba(37,99,235,.82);color:#fff;font-weight:700;}
.nav-icon{font-size:12px;width:15px;text-align:center;flex-shrink:0;}
.nav-badge-rka{margin-left:auto;background:#ef4444;color:#fff;font-size:9px;font-weight:800;padding:1px 5px;border-radius:10px;}
.sb-role{margin:8px 12px 0;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:700;display:flex;align-items:center;gap:5px;border:1px solid;}
.sb-role.admin{background:rgba(37,99,235,.18);border-color:rgba(37,99,235,.4);color:#93c5fd;}
.sb-role.pegawai{background:rgba(5,150,105,.18);border-color:rgba(5,150,105,.4);color:#6ee7b7;}
.sb-foot{margin-top:auto;padding:10px 14px;border-top:1px solid rgba(255,255,255,.08);}
.btn-logout{width:100%;padding:7px 10px;background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.35);border-radius:7px;color:#fca5a5;font-family:var(--font);font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;transition:all .15s;}
.btn-logout:hover{background:rgba(220,38,38,.3);color:#fff;}
.sb-copy{font-size:9px;color:rgba(255,255,255,.2);text-align:center;margin-top:7px;}
.main{margin-left:216px;min-height:100vh;display:flex;flex-direction:column;}
.topbar{background:var(--navy);padding:0 24px;height:52px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(0,0,0,.15);}
.tb-logo{font-size:15px;font-weight:800;color:#fff;display:flex;align-items:center;gap:7px;}
.tb-logo .mk{width:28px;height:28px;background:linear-gradient(135deg,var(--accent),#60a5fa);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;}
.tb-right{display:flex;align-items:center;gap:10px;}
.tb-user{font-size:11.5px;color:rgba(255,255,255,.75);display:flex;align-items:center;gap:6px;}
.tb-user b{color:#fff;}
.tb-role-pill{font-size:9.5px;font-weight:800;padding:2px 8px;border-radius:10px;border:1px solid;}
.tb-role-pill.admin{background:rgba(37,99,235,.3);border-color:rgba(96,165,250,.5);color:#bfdbfe;}
.tb-role-pill.pegawai{background:rgba(5,150,105,.3);border-color:rgba(52,211,153,.5);color:#a7f3d0;}
.month-badge{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 12px;}
.month-badge select{background:none;border:none;color:#fff;font-family:var(--mono);font-size:11px;font-weight:700;outline:none;cursor:pointer;}
.month-badge select option{background:var(--navy);color:#fff;}
.breadbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 24px;height:36px;display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);}
.breadbar .bca{color:var(--text);font-weight:700;}
.content{padding:20px 24px;flex:1;}
.page{display:none;animation:fadeIn .15s ease;}
.page.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}
.ph{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;}
.ph h2{font-size:21px;font-weight:800;}
.ph p{font-size:11.5px;color:var(--muted);margin-top:2px;}
.sg{display:grid;gap:12px;margin-bottom:16px;}
.sg4{grid-template-columns:repeat(4,1fr);}
.sg3{grid-template-columns:repeat(3,1fr);}
.sg2{grid-template-columns:repeat(2,1fr);}
.sc{border-radius:var(--radius);padding:14px 16px;box-shadow:var(--shadow-md);border:none;position:relative;overflow:hidden;}
.sc-blue{background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;}
.sc-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24);color:#fff;}
.sc-green{background:linear-gradient(135deg,#059669,#10b981);color:#fff;}
.sc-teal{background:linear-gradient(135deg,#0d9488,#14b8a6);color:#fff;}
.sc-purple{background:linear-gradient(135deg,#7c3aed,#a78bfa);color:#fff;}
.sc-red{background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;}
.sc .lbl{font-size:9.5px;font-weight:600;opacity:.82;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;}
.sc .val{font-size:16px;font-weight:800;font-family:var(--mono);line-height:1.1;}
.sc .sub{font-size:10px;opacity:.75;font-weight:500;margin-top:2px;}
.sc .ico{position:absolute;top:10px;right:12px;font-size:20px;opacity:.22;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:16px;box-shadow:var(--shadow);}
.card-head{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;}
.card-head h3{font-size:12.5px;font-weight:700;}
.card-head .sub{font-size:10.5px;color:var(--muted);}
.card-body{padding:16px;}
.tw{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th{text-align:left;padding:9px 12px;font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;background:var(--surface2);border-bottom:2px solid var(--border);white-space:nowrap;}
td{padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#f8fbff;}
.mono{font-family:var(--mono);font-size:11.5px;font-weight:600;}
.muted{color:var(--muted);font-size:10.5px;}
.th-right{text-align:right!important;}
.col-harga{font-family:var(--mono);font-size:11px;font-weight:600;text-align:right;white-space:nowrap;}
.col-total{font-family:var(--mono);font-size:11px;font-weight:700;text-align:right;white-space:nowrap;color:var(--green);}
.badge{padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:3px;}
.b-info{background:#dbeafe;color:#1e40af;}
.b-green{background:#d1fae5;color:#065f46;}
.b-orange{background:#fef3c7;color:#92400e;}
.b-red{background:#fee2e2;color:#991b1b;}
.b-purple{background:#ede9fe;color:#5b21b6;}
.tag-pihak{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;background:#ede9fe;color:#5b21b6;white-space:nowrap;}
.tag-seksi{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;background:#dcfce7;color:#166534;white-space:nowrap;border:1px solid #86efac;}
.tag-surat{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;background:#e0f2fe;color:#0369a1;white-space:nowrap;font-family:var(--mono);}
.tag-nip{display:inline-block;padding:2px 7px;border-radius:10px;font-size:9.5px;font-weight:600;background:#f1f5f9;color:var(--muted);white-space:nowrap;font-family:var(--mono);}
.tag-jabatan{display:inline-block;padding:2px 7px;border-radius:10px;font-size:9.5px;font-weight:500;background:#fef3c7;color:#92400e;white-space:nowrap;}
.tag-group{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;background:#f0fdf4;color:#166534;font-family:var(--mono);white-space:nowrap;}
.tag-month{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;background:linear-gradient(90deg,#eff6ff,#dbeafe);color:var(--accent2);border:1px solid #bfdbfe;font-family:var(--mono);}
.filter-bar{display:flex;gap:7px;align-items:center;flex-wrap:wrap;padding:10px 14px 0;}
.sw{position:relative;flex:1;min-width:140px;}
.sw .si{position:absolute;left:9px;top:50%;transform:translateY(-50%);font-size:11px;pointer-events:none;}
.si-inp{width:100%;padding:6px 9px 6px 27px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-family:var(--font);outline:none;background:var(--surface2);}
.si-inp:focus{border-color:var(--accent);background:#fff;}
.sel{padding:6px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:11.5px;font-family:var(--font);background:var(--surface2);outline:none;cursor:pointer;}
.sel:focus{border-color:var(--accent);}
.btn{padding:7px 14px;border-radius:7px;font-family:var(--font);font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .13s;display:inline-flex;align-items:center;gap:5px;}
.btn-sm{padding:5px 11px;font-size:11.5px;}
.btn-xs{padding:3px 8px;font-size:10.5px;}
.btn-accent{background:var(--accent);color:#fff;}
.btn-accent:hover{background:var(--accent2);}
.btn-orange{background:var(--orange);color:#fff;}
.btn-orange:hover{background:#d97706;}
.btn-green{background:var(--green);color:#fff;}
.btn-green:hover{background:#047857;}
.btn-red{background:#fef2f2;border:1px solid #fca5a5;color:var(--red);}
.btn-red:hover{background:#fee2e2;}
.btn-ghost{background:var(--surface2);border:1.5px solid var(--border);color:var(--muted);}
.btn-ghost:hover{background:var(--border);color:var(--text);}
.btn-purple{background:#7c3aed;color:#fff;}
.btn-purple:hover{background:#6d28d9;}
.pb{height:5px;background:#e2e8f0;border-radius:4px;overflow:hidden;min-width:60px;}
.pf{height:100%;border-radius:4px;}
.mov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:500;display:none;align-items:center;justify-content:center;}
.mov.open{display:flex;}
.modal{background:#fff;border-radius:13px;width:760px;max-width:96vw;max-height:94vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.modal.xl{width:820px;}
.modal.lg{width:680px;}
.modal.sm{width:480px;}
.modal.full{width:1000px;}
.mh{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:10;}
.mh h3{font-size:13.5px;font-weight:700;}
.mc{background:none;border:none;font-size:17px;cursor:pointer;color:var(--muted);}
.mb{padding:18px;}
.mf{padding:12px 18px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:7px;position:sticky;bottom:0;background:#fff;z-index:10;}
.fg{display:flex;flex-direction:column;gap:4px;margin-bottom:11px;}
.fl{font-size:11px;font-weight:600;color:#374151;}
.fc{padding:8px 11px;border:1.5px solid var(--border);border-radius:7px;font-family:var(--font);font-size:12.5px;outline:none;background:var(--surface2);color:var(--text);transition:border-color .13s;width:100%;}
.fc:focus{border-color:var(--accent);background:#fff;}
.fc[readonly]{background:#f8fafc;color:var(--muted);cursor:not-allowed;}
.fr2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.fr3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}
.fr4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px;}
.mdivider{border:none;border-top:1.5px dashed var(--border);margin:14px 0 12px;}
.pagi{display:flex;align-items:center;gap:4px;padding:10px 14px;border-top:1px solid var(--border);}
.pagi-info{font-size:11px;color:var(--muted);flex:1;}
.pb-btn{width:26px;height:26px;border-radius:5px;border:1.5px solid var(--border);background:var(--surface2);font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .13s;}
.pb-btn:hover{border-color:var(--accent);color:var(--accent);}
.pb-btn.active{background:var(--accent);border-color:var(--accent);color:#fff;}
.month-tabs{display:flex;gap:4px;flex-wrap:wrap;margin-bottom:14px;}
.mt-btn{padding:4px 12px;border-radius:20px;border:1.5px solid var(--border);font-family:var(--mono);font-size:11px;font-weight:700;cursor:pointer;background:var(--surface2);color:var(--muted);transition:all .13s;}
.mt-btn:hover{border-color:var(--accent);color:var(--accent);}
.mt-btn.active{background:var(--accent);border-color:var(--accent);color:#fff;}
.alert{padding:10px 12px;border-radius:8px;font-size:11.5px;margin-bottom:12px;display:flex;align-items:flex-start;gap:8px;}
.alert-ok{background:#f0fdf4;border:1.5px solid #86efac;color:#166534;}
.alert-warn{background:#fffbeb;border:1.5px solid #fcd34d;color:#92400e;}
.alert-err{background:#fff5f5;border:1.5px solid #fca5a5;color:#991b1b;}
.alert-info{background:#eff6ff;border:1.5px solid #93c5fd;color:#1e3a8a;}
.empty{text-align:center;padding:32px 16px;color:var(--muted);}
.empty .ei{font-size:26px;margin-bottom:6px;}
.empty p{font-size:12.5px;}
.month-strip{background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:9px 14px;margin-bottom:12px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
.ms-item{font-size:11.5px;color:var(--muted);}
.ms-item b{color:var(--text);font-family:var(--mono);}
.upload-zone{border:2.5px dashed var(--border);border-radius:12px;padding:28px 20px;text-align:center;background:var(--surface2);transition:all .2s;cursor:pointer;position:relative;}
.upload-zone:hover,.upload-zone.drag{border-color:var(--accent);background:var(--accent-light);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-zone .uz-icon{font-size:32px;margin-bottom:8px;}
.upload-zone .uz-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:3px;}
.upload-zone .uz-sub{font-size:11px;color:var(--muted);}
.tmpl-hint{background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:10px 14px;font-size:11.5px;color:#166534;display:flex;align-items:center;gap:8px;margin-bottom:12px;}
.col-map-grid{display:grid;grid-template-columns:1fr 24px 1fr;align-items:center;gap:6px 8px;font-size:12px;}
.col-map-grid .lh{font-weight:600;color:var(--text);}
.col-map-grid .arr{color:var(--muted);text-align:center;}
.col-map-grid select{padding:5px 8px;border:1.5px solid var(--border);border-radius:6px;font-family:var(--font);font-size:11.5px;background:var(--surface2);outline:none;}
.col-map-grid select:focus{border-color:var(--accent);}
.prev-wrap{max-height:220px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;}
.prev-wrap table th{position:sticky;top:0;z-index:1;}
.rka-month-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:16px;}
.rka-month-card{background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px;position:relative;cursor:pointer;transition:all .15s;}
.rka-month-card:hover{border-color:var(--accent);box-shadow:var(--shadow-md);}
.rka-month-card.has-data{border-color:#86efac;background:#f0fdf4;}
.rka-month-card .rmc-month{font-size:13px;font-weight:800;margin-bottom:4px;}
.rka-month-card .rmc-items{font-size:10.5px;color:var(--muted);}
.rka-month-card .rmc-badge{position:absolute;top:10px;right:10px;font-size:9px;font-weight:700;padding:2px 7px;border-radius:10px;}
.rka-month-card .rmc-actions{display:flex;gap:5px;margin-top:10px;}
.import-wizard{background:var(--surface);border:1.5px solid var(--accent);border-radius:12px;padding:16px;margin-bottom:16px;}
.import-wizard .iw-head{font-size:12px;font-weight:700;color:var(--accent2);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:8px;}
.step-bar{display:flex;align-items:center;gap:0;margin-bottom:16px;}
.step-item{flex:1;text-align:center;position:relative;}
.step-item::after{content:'';position:absolute;top:14px;left:50%;width:100%;height:2px;background:var(--border);z-index:0;}
.step-item:last-child::after{display:none;}
.step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;margin:0 auto 4px;position:relative;z-index:1;transition:all .2s;}
.step-dot.done{background:var(--green);color:#fff;}
.step-dot.active{background:var(--accent);color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.2);}
.step-dot.idle{background:var(--surface2);border:2px solid var(--border);color:var(--muted2);}
.step-label{font-size:9.5px;font-weight:600;color:var(--muted);}
.step-label.active{color:var(--accent);}
.sender-block{background:var(--accent-light);border:1.5px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-bottom:16px;}
.sender-block .sb-title{font-size:11px;font-weight:700;color:var(--accent2);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;}
.items-list{display:flex;flex-direction:column;gap:8px;margin-bottom:12px;}
.add-item-form{background:var(--surface2);border:1.5px dashed var(--border);border-radius:10px;padding:14px 16px;}
.add-item-form .ait{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;}
.items-total-bar{background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:8px 14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
.items-total-bar .itb-label{font-size:11.5px;font-weight:600;color:#166534;}
.items-total-bar .itb-val{font-size:14px;font-weight:800;font-family:var(--mono);color:#065f46;}
.group-row-header td{background:#f0f7ff!important;border-top:2px solid #bfdbfe;}
.group-row-item td{background:var(--surface);}
.group-row-item:hover td{background:#f8fbff;}
.group-row-total td{background:#f0fdf4!important;border-bottom:2px solid #86efac;}
.btn-nota{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:10px;font-weight:700;cursor:pointer;border:1.5px solid #93c5fd;background:#eff6ff;color:#1e40af;transition:all .13s;white-space:nowrap;font-family:var(--font);}
.btn-nota:hover{background:#2563eb;color:#fff;border-color:#2563eb;}
.access-denied{text-align:center;padding:60px 24px;}
.access-denied .ad-icon{font-size:56px;margin-bottom:12px;}
.access-denied h2{font-size:22px;font-weight:800;color:var(--red);margin-bottom:6px;}
.access-denied p{font-size:13px;color:var(--muted);margin-bottom:16px;}
.sk-search-box{position:relative;margin-bottom:20px;}
.sk-search-box input{width:100%;padding:13px 16px 13px 44px;border:2px solid var(--border);border-radius:10px;font-family:var(--font);font-size:14px;outline:none;background:#fff;transition:border-color .15s,box-shadow .15s;}
.sk-search-box input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.12);}
.sk-search-box .sk-si{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:16px;pointer-events:none;}
.sk-search-box .sk-clear{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:var(--border);border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;display:none;align-items:center;justify-content:center;color:var(--muted);}
.sk-search-box .sk-clear.show{display:flex;}
.sk-result-wrap{background:var(--surface);border:1.5px solid var(--accent);border-radius:10px;overflow:hidden;margin-bottom:16px;}
.sk-result-header{background:linear-gradient(90deg,#eff6ff,#e0f2fe);padding:10px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.sk-result-header .skrh-title{font-size:12px;font-weight:700;color:var(--accent2);}
.sk-result-item{display:flex;align-items:center;gap:10px;padding:9px 16px;border-bottom:1px solid var(--border);transition:background .1s;}
.sk-result-item:last-child{border-bottom:none;}
.sk-result-item:hover{background:#f8fbff;}
.sk-result-item .sri-no{font-size:10px;font-family:var(--mono);color:var(--muted2);width:22px;text-align:center;flex-shrink:0;}
.sk-result-item .sri-name{flex:1;font-size:12.5px;font-weight:600;}
.sk-result-item .sri-name mark{background:#fef08a;color:#713f12;border-radius:3px;padding:0 2px;}
.sk-result-item .sri-sub{display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;background:#ede9fe;color:#5b21b6;white-space:nowrap;flex-shrink:0;}
/* harga-otomatis badge */
.harga-auto-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:10px;font-size:9.5px;font-weight:700;background:#d1fae5;color:#065f46;border:1px solid #86efac;}
@media(max-width:1000px){.sidebar{width:190px;}.main{margin-left:190px;}.sg4{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>

<?php
$roleJs = $isAdmin ? 'admin' : 'pegawai';
$namaJs = htmlspecialchars($namaUser, ENT_QUOTES);
$usernameJs = htmlspecialchars($username, ENT_QUOTES);
?>

<aside class="sidebar">
  <div class="sb-logo">
    <div class="mark">📋</div>
    <h1>SIPUA</h1>
    <p>Sistem Usulan Anggaran</p>
    <div class="inst">UPT RSBG Tuban</div>
  </div>
  <div class="sb-role <?= $roleJs ?>">
    <?= $isAdmin ? '🔐 Admin' : '👤 Pegawai' ?>
    <span style="font-size:9px;opacity:.7;margin-left:2px;"><?= $namaJs ?></span>
  </div>
  <div class="nav-sec">Menu Utama</div>
  <button class="nav-item" onclick="goPage('dashboard')"><span class="nav-icon">🏠</span> Dashboard</button>
  <?php if ($isAdmin): ?>
  <button class="nav-item" onclick="goPage('import-rka')">
    <span class="nav-icon">📤</span> Import DPA
    <span class="nav-badge-rka" id="sbBadgeRka" style="display:none">!</span>
  </button>
  <?php endif; ?>
  <button class="nav-item" onclick="goPage('rka')"><span class="nav-icon">📑</span> Data DPA</button>
  <button class="nav-item" onclick="goPage('subkegiatan')"><span class="nav-icon">🏛️</span> Sub Kegiatan</button>
  <button class="nav-item" onclick="goPage('usulan')"><span class="nav-icon">✅</span> Usulan Bulanan</button>
  <button class="nav-item" onclick="goPage('riwayat')"><span class="nav-icon">🗂️</span> Riwayat per Bulan</button>
  <button class="nav-item" onclick="goPage('laporan')"><span class="nav-icon">📊</span> Laporan</button>
  <div class="sb-foot">
    <a href="?logout=1" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">🚪 Keluar</a>
    <div class="sb-copy">SIPUA v1 © 2026 · TA 2026</div>
  </div>
</aside>

<main class="main">
<div class="topbar">
  <div class="tb-logo"><div class="mk">📋</div><span id="tbTitle">Dashboard</span></div>
  <div class="tb-right">
    <div class="month-badge">
      <span style="color:rgba(255,255,255,.6);font-size:10px;">Bulan Aktif:</span>
      <select id="globalMonth" onchange="onMonthChange()">
        <option value="1">Januari 2026</option><option value="2">Februari 2026</option>
        <option value="3">Maret 2026</option><option value="4" selected>April 2026</option>
        <option value="5">Mei 2026</option><option value="6">Juni 2026</option>
        <option value="7">Juli 2026</option><option value="8">Agustus 2026</option>
        <option value="9">September 2026</option><option value="10">Oktober 2026</option>
        <option value="11">November 2026</option><option value="12">Desember 2026</option>
      </select>
    </div>
    <span class="tb-user">
      <span class="tb-role-pill <?= $roleJs ?>"><?= $isAdmin ? '🔐 Admin' : '👤 Pegawai' ?></span>
      <b><?= $namaJs ?></b>
    </span>
    <a href="?logout=1" style="color:rgba(255,255,255,.5);font-size:11px;text-decoration:none;padding:4px 9px;border:1px solid rgba(255,255,255,.15);border-radius:5px;" onclick="return confirm('Yakin ingin keluar?')">🚪 Keluar</a>
  </div>
</div>
<div class="breadbar">
  <span>🏠</span><span style="color:var(--muted2);">/</span>
  <span class="bca" id="breadActive">Dashboard</span>
</div>

<div class="content">

<!-- ════ IMPORT RKA ════ -->
<div class="page" id="page-import-rka">
  <?php if ($isAdmin): ?>
  <div class="ph">
    <div><h2>Import Data DPA</h2><p>Upload file Excel DPA per bulan — data tiap bulan tersimpan secara terpisah</p></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <button class="btn btn-accent btn-sm" onclick="openImportWizard(null)">+ Import DPA Baru</button>
    </div>
  </div>
  <div class="sg sg4" id="importRkaStats"></div>
  <div class="card">
    <div class="card-head"><h3>📅 Data DPA Tersimpan per Bulan</h3><span class="sub" id="rkaStorageSub">—</span></div>
    <div class="card-body" id="rkaMonthGrid"><div class="empty"><div class="ei">📭</div><p>Belum ada data DPA.</p></div></div>
  </div>
  <div class="import-wizard" id="importWizard" style="display:none;">
    <div class="iw-head">
      <span id="iwTitle">📤 Import DPA untuk Bulan</span>
      <button class="btn btn-ghost btn-xs" onclick="closeImportWizard()">✕ Tutup</button>
    </div>
    <div class="step-bar" id="stepBar">
      <div class="step-item"><div class="step-dot active" id="sd1">1</div><div class="step-label active" id="sl1">Pilih Bulan</div></div>
      <div class="step-item"><div class="step-dot idle" id="sd2">2</div><div class="step-label" id="sl2">Upload File</div></div>
      <div class="step-item"><div class="step-dot idle" id="sd3">3</div><div class="step-label" id="sl3">Pemetaan Kolom</div></div>
      <div class="step-item"><div class="step-dot idle" id="sd4">4</div><div class="step-label" id="sl4">Preview &amp; Simpan</div></div>
    </div>
    <div id="iwStep1">
      <div class="fg">
        <label class="fl">Pilih Bulan yang akan di-Import *</label>
        <select class="fc" id="iwBulan" style="max-width:260px;">
          <option value="1">Januari 2026</option><option value="2">Februari 2026</option>
          <option value="3">Maret 2026</option><option value="4">April 2026</option>
          <option value="5">Mei 2026</option><option value="6">Juni 2026</option>
          <option value="7">Juli 2026</option><option value="8">Agustus 2026</option>
          <option value="9">September 2026</option><option value="10">Oktober 2026</option>
          <option value="11">November 2026</option><option value="12">Desember 2026</option>
        </select>
      </div>
      <div id="iwExistingWarn" style="display:none;" class="alert alert-warn"></div>
      <div class="tmpl-hint">💡 <span>Data DPA per bulan bisa berbeda-beda. Setiap bulan menyimpan data DPA secara terpisah.</span></div>
      <div style="display:flex;justify-content:flex-end;margin-top:8px;">
        <button class="btn btn-accent" onclick="iwNext1()">Lanjut: Upload File →</button>
      </div>
    </div>
    <div id="iwStep2" style="display:none;">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
          <div class="upload-zone" id="uploadZone">
            <input type="file" id="rkaFileInput" accept=".xlsx,.xls,.csv" onchange="handleRkaFile(event)">
            <div class="uz-icon">📊</div>
            <div class="uz-title">Drag &amp; Drop atau Klik untuk Upload</div>
            <div class="uz-sub">Format: .xlsx · .xls · .csv</div>
          </div>
          <div id="filePreviewInfo" style="display:none;margin-top:10px;font-size:12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;">
            <b id="fpFileName">—</b><br><span style="color:var(--muted);" id="fpDetail">—</span>
          </div>
        </div>
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">📋 Format Kolom yang Diperlukan</div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:11.5px;line-height:1.8;">
            <div>✅ <b>Sub Kegiatan</b> — nama sub kegiatan</div>
            <div>✅ <b>Uraian</b> — nama barang / jasa</div>
            <div>✅ <b>Jumlah</b> — volume / kuantitas</div>
            <div>☑️ <b>Satuan</b> — satuan ukur (opsional)</div>
            <div>☑️ <b>Harga Satuan</b> — harga per unit (opsional)</div>
          </div>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:12px;">
        <button class="btn btn-ghost btn-sm" onclick="iwBack(1)">← Kembali</button>
        <button class="btn btn-accent" id="iwNext2Btn" onclick="iwNext2()" disabled style="opacity:.5;cursor:not-allowed;">Lanjut: Pemetaan Kolom →</button>
      </div>
    </div>
    <div id="iwStep3" style="display:none;">
      <p style="font-size:11.5px;color:var(--muted);margin-bottom:12px;">Pastikan setiap kolom wajib sudah dipetakan dengan benar.</p>
      <div class="col-map-grid" id="iwColMapGrid"></div>
      <div style="display:flex;justify-content:space-between;margin-top:14px;">
        <button class="btn btn-ghost btn-sm" onclick="iwBack(2)">← Kembali</button>
        <button class="btn btn-accent" onclick="iwNext3()">Lanjut: Preview →</button>
      </div>
    </div>
    <div id="iwStep4" style="display:none;">
      <div id="iwPreviewAlert"></div>
      <div class="prev-wrap" style="margin-bottom:12px;"><div id="iwPreviewTable"></div></div>
      <div style="display:flex;justify-content:space-between;">
        <button class="btn btn-ghost btn-sm" onclick="iwBack(3)">← Kembali</button>
        <div style="display:flex;gap:7px;">
          <span class="badge b-info" id="iwPreviewCount">—</span>
          <button class="btn btn-green" onclick="doSaveRkaBulan()">✅ Simpan ke Sistem</button>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="access-denied">
    <div class="ad-icon">🔒</div>
    <h2>Akses Terbatas</h2>
    <p>Halaman <b>Import DPA</b> hanya dapat diakses oleh <b>Admin</b>
    <button class="btn btn-accent" onclick="goPage('dashboard')">← Kembali ke Dashboard</button>
  </div>
  <?php endif; ?>
</div>

<!-- ════ DASHBOARD ════ -->
<div class="page" id="page-dashboard">
  <div class="ph">
    <div><h2>Dashboard</h2><p id="dashSub">Ringkasan Usulan TA 2026</p></div>
    <button class="btn btn-ghost btn-sm" onclick="renderDashboard()">↻ Refresh</button>
  </div>
  <div id="dashNoRka" style="display:none;"></div>
  <div class="sg sg4" id="dashStats"></div>
  <div style="display:grid;grid-template-columns:3fr 2fr;gap:16px;margin-bottom:16px;">
    <div class="card" style="margin-bottom:0;">
      <div class="card-head"><h3>📈 Tren Usulan per Bulan (2026)</h3></div>
      <div class="card-body"><canvas id="dashBar" height="200"></canvas></div>
    </div>
    <div class="card" style="margin-bottom:0;">
      <div class="card-head"><h3>📋 Ringkasan Bulan Ini</h3><span class="sub" id="dashBulanNow">—</span></div>
      <div class="card-body" style="padding:12px;"><div id="dashMonthSummary"></div></div>
    </div>
  </div>
  <div class="card">
    <div class="card-head"><h3>🏛️ Status Item DPA (Bulan Aktif)</h3><span class="sub" id="dashRkaBulanSub">—</span></div>
    <div class="tw"><table>
      <thead><tr><th>Sub Kegiatan</th><th>Total Item</th><th>Sudah Diusulkan</th><th>Belum Diusulkan</th><th>Progress</th></tr></thead>
      <tbody id="dashSubTbody"></tbody>
    </table></div>
  </div>
</div>

<!-- ════ DATA RKA ════ -->
<div class="page" id="page-rka">
  <div class="ph">
    <div><h2>Data DPA</h2><p id="rkaSubTitle">Master item DPA TA 2026</p></div>
    <div style="display:flex;gap:8px;">
      <?php if ($isAdmin): ?>
      <button class="btn btn-accent btn-sm" onclick="goPage('import-rka')">📤 Import DPA</button>
      <?php endif; ?>
      <button class="btn btn-green btn-sm" onclick="exportRkaExcel()">⬇️ Rekapitulasi DPA</button>
    </div>
  </div>
  <div id="rkaNoBanner" style="display:none;"></div>
  <div class="card" style="margin-bottom:12px;">
    <div class="card-head"><h3>📅 Filter Bulan DPA</h3></div>
    <div class="card-body" style="padding:10px 14px;">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <label style="font-size:12px;font-weight:600;">Tampilkan DPA bulan:</label>
        <select class="sel" id="rkaBulanFilter" onchange="renderRkaTable()">
          <option value="all">Semua Bulan</option>
          <option value="1">Januari 2026</option><option value="2">Februari 2026</option>
          <option value="3">Maret 2026</option><option value="4">April 2026</option>
          <option value="5">Mei 2026</option><option value="6">Juni 2026</option>
          <option value="7">Juli 2026</option><option value="8">Agustus 2026</option>
          <option value="9">September 2026</option><option value="10">Oktober 2026</option>
          <option value="11">November 2026</option><option value="12">Desember 2026</option>
        </select>
        <span id="rkaFilterInfo" class="tag-month">—</span>
      </div>
    </div>
  </div>
  <div class="sg sg3">
    <div class="sc sc-blue"><span class="ico">📋</span><div class="lbl">Total Item</div><div class="val" id="rkaCountStat">0</div><div class="sub">item kegiatan</div></div>
    <div class="sc sc-orange"><span class="ico">🏛️</span><div class="lbl">Sub Kegiatan</div><div class="val" id="rkaSubCount">0</div><div class="sub">sub kegiatan</div></div>
    <div class="sc sc-green"><span class="ico">✅</span><div class="lbl">Item Diusulkan</div><div class="val" id="rkaUsulCount">—</div><div class="sub">dari total item</div></div>
  </div>
  <div class="card">
    <div class="card-head"><h3>📑 Daftar Item DPA</h3><span class="sub" id="rkaCardSub">TA 2026</span></div>
    <div class="filter-bar">
      <div class="sw"><span class="si">🔍</span><input type="text" class="si-inp" id="filterRka" placeholder="Cari sub kegiatan atau uraian…" oninput="renderRkaTable()"></div>
      <select class="sel" id="filterRkaSub" onchange="renderRkaTable()"><option value="">Semua Sub Kegiatan</option></select>
      <select class="sel" id="filterRkaPP" onchange="renderRkaTable()">
        <option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="200">Semua</option>
      </select>
    </div>
    <div class="tw"><table>
      <!-- Harga Satuan TIDAK ditampilkan di tabel Data RKA -->
      <thead><tr><th style="width:42px;">No</th><th>Bulan</th><th>Sub Kegiatan</th><th>Uraian</th><th style="text-align:right;">Jumlah</th><th>Satuan</th><th style="text-align:right;">Diusulkan</th><th style="text-align:right;">Sisa</th></tr></thead>
      <tbody id="rkaTbody"></tbody>
    </table></div>
    <div class="pagi"><span class="pagi-info" id="rkaInfo">—</span><span id="rkaPagiBtn"></span></div>
  </div>
</div>

<!-- ════ SUB KEGIATAN ════ -->
<div class="page" id="page-subkegiatan">
  <div class="ph">
    <div><h2>Sub Kegiatan</h2><p id="skPageSub">Daftar sub kegiatan beserta barang dari data DPA</p></div>
    <button class="btn btn-ghost btn-sm" onclick="renderSubKegiatanPage()">↻ Refresh</button>
  </div>
  <div id="skNoRka" style="display:none;">
    <div class="alert alert-warn">⚠️ <span>Data DPA belum diimport. <?= $isAdmin ? '<b onclick="goPage(\'import-rka\')" style="cursor:pointer;text-decoration:underline;">Klik di sini untuk Import DPA</b>' : 'Hubungi Admin untuk import data DPA.' ?> terlebih dahulu.</span></div>
  </div>
  <div class="sk-search-box">
    <span class="sk-si">🔍</span>
    <input type="text" id="skSearch" placeholder="Masukkan nama barang, kemudian sistem akan menampilkan subkegiatan yang sesuai." oninput="onSkSearch()">
    <button class="sk-clear" id="skClearBtn" onclick="clearSkSearch()">✕</button>
  </div>
  <div id="skSearchResult">
    <div class="empty" style="padding:32px 16px;"><div class="ei">🔍</div><p style="font-size:13px;">Masukkan nama barang di kolom pencarian di atas</p></div>
  </div>
</div>

<!-- ════ USULAN BULANAN ════ -->
<div class="page" id="page-usulan">
  <div class="ph">
    <div><h2>Usulan Bulanan</h2><p id="usulanSubtitle">Input usulan permintaan barang per bulan</p></div>
    <button class="btn btn-accent" onclick="openModalUsulan()">+ Buat Pengajuan Baru</button>
  </div>
  <div id="usulanNoRka" style="display:none;"></div>
  <div class="month-strip" id="usulanMonthStrip"></div>
  <div class="sg sg4" id="usulanStats"></div>
  <div class="card">
    <div class="card-head">
      <h3>📋 Daftar Pengajuan <span id="usulanBulanLabel">Bulan Ini</span></h3>
      <span style="font-size:10.5px;color:var(--muted);">💡 Klik <b>⬇️ Usulan Permintaan</b> di tiap pengajuan untuk unduh</span>
    </div>
    <div class="filter-bar">
      <div class="sw"><span class="si">🔍</span><input type="text" class="si-inp" id="filterUsulan" placeholder="Cari uraian, pihak, seksi, NIP, nomor surat…" oninput="renderUsulanTable()"></div>
      <select class="sel" id="filterUsulanSub" onchange="renderUsulanTable()"><option value="">Semua Sub</option></select>
    </div>
    <div class="tw">
      <table>
        <thead><tr>
          <th style="width:36px;">No</th><th>Sub Kegiatan</th><th>Uraian Barang</th>
          <th style="text-align:right;width:50px;">Jumlah</th><th style="width:55px;">Sat</th>
          <th class="th-right" style="width:115px;">Harga Satuan</th>
          <th class="th-right" style="width:115px;">Jumlah Harga</th>
          <th>Keterangan</th><th style="width:130px;">Aksi</th>
        </tr></thead>
        <tbody id="usulanTbody"></tbody>
      </table>
    </div>
    <div class="pagi"><span class="pagi-info" id="usulanInfo">—</span></div>
  </div>
</div>

<!-- ════ RIWAYAT ════ -->
<div class="page" id="page-riwayat">
  <div class="ph">
    <div><h2>Riwayat per Bulan</h2><p>Data pengajuan tersimpan per bulan</p></div>
    <button class="btn btn-ghost btn-sm" onclick="exportAllExcel()">⬇️ Export All</button>
  </div>
  <div class="month-tabs" id="riwayatMonthTabs"></div>
  <div id="riwayatContent"><div class="empty"><div class="ei">📅</div><p>Pilih bulan untuk melihat riwayat.</p></div></div>
</div>

<!-- ════ LAPORAN ════ -->
<div class="page" id="page-laporan">
  <div class="ph">
    <div><h2>Laporan Rekapitulasi</h2><p>Rekap usulan seluruh bulan TA 2026</p></div>
    <div style="display:flex;gap:7px;">
      <button class="btn btn-orange" onclick="window.print()">🖨️ Cetak Laporan </button>
      <button class="btn btn-green btn-sm" onclick="exportLaporan()">⬇️ Rekapitulasi Usulan Barang</button>
    </div>
  </div>
  <div class="sg sg3" id="lapStats"></div>
  <div class="card">
    <div class="card-head"><h3>📊 Grafik Pengajuan per Sub Kegiatan</h3></div>
    <div class="card-body"><canvas id="lapBar" height="180"></canvas></div>
  </div>
  <div class="card">
    <div class="card-head"><h3>📋 Rekap per Item DPA</h3></div>
    <div class="tw"><table>
      <thead><tr><th>No</th><th>Bulan DPA</th><th>Sub Kegiatan</th><th>Uraian</th><th style="text-align:right;">Jumlah DPA</th><th>Satuan</th><th style="text-align:right;">Total Diusulkan</th><th style="text-align:right;">Sisa</th><th>%</th></tr></thead>
      <tbody id="lapTbody"></tbody>
    </table></div>
  </div>
</div>

</div><!-- /content -->
</main>

<!-- ════ MODAL EDIT PENGAJUAN ════ -->
<div class="mov" id="modalEditPengajuan">
  <div class="modal xl">
    <div class="mh">
      <h3>✏️ Edit Pengajuan</h3>
      <button class="mc" onclick="closeModal('modalEditPengajuan')">✕</button>
    </div>
    <div class="mb">
      <!-- Identitas -->
      <div class="sender-block">
        <div class="sb-title">📋 Identitas Pengajuan</div>
        <div class="fr2">
          <div class="fg">
            <label class="fl">Bulan *</label>
            <select class="fc" id="eBulan">
              <option value="1">Januari 2026</option><option value="2">Februari 2026</option>
              <option value="3">Maret 2026</option><option value="4">April 2026</option>
              <option value="5">Mei 2026</option><option value="6">Juni 2026</option>
              <option value="7">Juli 2026</option><option value="8">Agustus 2026</option>
              <option value="9">September 2026</option><option value="10">Oktober 2026</option>
              <option value="11">November 2026</option><option value="12">Desember 2026</option>
            </select>
          </div>
          <div class="fg"><label class="fl">Nomor Surat *</label><input type="text" class="fc" id="eNomorSurat" placeholder="000.1.2.3/001/107.6/2026"></div>
        </div>
        <div class="fr2" style="margin-bottom:8px;">
          <div class="fg" style="margin-bottom:0;"><label class="fl">Pegawai yang Mengajukan *</label><input type="text" class="fc" id="ePihak" placeholder="Nama lengkap"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Dari Seksi *</label>
            <select class="fc" id="eSeksi">
              <option value="">— Pilih Seksi —</option>
              <option value="Seksi Tata Usaha">Seksi Tata Usaha</option>
              <option value="Seksi Rehabilitasi Sosial">Seksi Rehabilitasi Sosial</option>
              <option value="Seksi Pelayanan">Seksi Pelayanan</option>
            </select>
          </div>
        </div>
        <div class="fr2">
          <div class="fg" style="margin-bottom:0;"><label class="fl">NIP</label><input type="text" class="fc" id="eNIP" placeholder="199712312020011001"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Jabatan</label><input type="text" class="fc" id="eJabatan" placeholder="Penata Laksana Barang"></div>
        </div>
      </div>

      <!-- Daftar Item -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
        <div style="font-size:12px;font-weight:700;">📦 Daftar Barang</div>
        <span id="eItemCountBadge" style="font-size:10px;font-family:var(--mono);font-weight:700;color:var(--accent);background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:3px 11px;">0 item</span>
      </div>
      <div id="eItemList" style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;"></div>

      <!-- Total -->
      <div id="eItemsTotal" class="items-total-bar" style="display:none;margin-bottom:12px;">
        <span class="itb-label">💰 Total Nilai Pengajuan</span>
        <span class="itb-val" id="eTotalNilai">—</span>
      </div>

      <!-- Form Tambah Item Baru -->
      <div class="add-item-form">
        <div class="ait">+ Tambah / Ganti Barang</div>
        <div class="fr2">
          <div class="fg"><label class="fl">Sub Kegiatan</label><select class="fc" id="eSubKeg" onchange="ePopulateItemSel()"><option value="">— Pilih —</option></select></div>
          <div class="fg"><label class="fl">Nama Barang</label><select class="fc" id="eItemSel" onchange="eOnItemSelect()"><option value="">— Pilih Sub dulu —</option></select></div>
        </div>
        <div class="fr4">
          <div class="fg" style="margin-bottom:0;"><label class="fl">Volume *</label><input type="number" class="fc" id="eVol" placeholder="10" min="1" oninput="eOnVolInput()"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Satuan</label><input type="text" class="fc" id="eSat" placeholder="Buah / Rim"></div>
          <div class="fg" style="margin-bottom:0;">
            <label class="fl" style="display:flex;align-items:center;gap:6px;">
              Harga Satuan
              <span id="eHargaAutoBadge" class="harga-auto-badge" style="display:none;">✨ Otomatis</span>
            </label>
            <input type="number" class="fc" id="eHarga" placeholder="10000" min="0" oninput="eOnVolInput()">
          </div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Total</label><input type="text" class="fc" id="eTotal" readonly placeholder="—"></div>
        </div>
        <div class="fg" style="margin-top:10px;margin-bottom:0;"><label class="fl">Keterangan</label><input type="text" class="fc" id="eKet" placeholder="Spesifikasi, tujuan penggunaan, dll."></div>
        <div style="margin-top:10px;display:flex;justify-content:flex-end;"><button class="btn btn-accent btn-sm" onclick="eAddItem()">+ Tambahkan ke Daftar</button></div>
      </div>

      <div id="eErrMsg" style="display:none;" class="alert alert-err" style="margin-top:10px;"></div>
    </div>
    <div class="mf" style="justify-content:space-between;align-items:center;">
      <span style="font-size:10.5px;color:var(--muted);">💡 Perubahan akan langsung menggantikan data lama</span>
      <div style="display:flex;gap:7px;">
        <button class="btn btn-ghost btn-sm" onclick="closeModal('modalEditPengajuan')">✕ Batal</button>
        <button class="btn btn-orange" onclick="saveEditPengajuan()">💾 Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<!-- ════ MODAL HAPUS RKA ════ -->
<div class="mov" id="modalDelRka">
  <div class="modal sm">
    <div class="mh"><h3>🗑️ Hapus Data DPA</h3><button class="mc" onclick="closeModal('modalDelRka')">✕</button></div>
    <div class="mb"><div class="alert alert-err" id="delRkaMsg"></div><p style="font-size:12.5px;">Apakah Anda yakin ingin menghapus data DPA ini? Tindakan ini tidak dapat dibatalkan.</p></div>
    <div class="mf"><button class="btn btn-ghost btn-sm" onclick="closeModal('modalDelRka')">Batal</button><button class="btn btn-red" id="delRkaConfirmBtn">🗑️ Ya, Hapus</button></div>
  </div>
</div>

<!-- ════ MODAL PENGAJUAN ════ -->
<div class="mov" id="modalUsulan">
  <div class="modal">
    <div class="mh">
      <h3 id="mUsulanTitle">➕ Buat Pengajuan Baru</h3>
      <button class="mc" onclick="closeModal('modalUsulan')">✕</button>
    </div>
    <div class="mb">
      <div class="sender-block">
        <div class="sb-title">📋 Identitas Pengajuan</div>
        <div class="fr2">
          <div class="fg"><label class="fl">Bulan *</label>
            <select class="fc" id="mBulan" onchange="onMBulanChange()">
              <option value="1">Januari 2026</option><option value="2">Februari 2026</option>
              <option value="3">Maret 2026</option><option value="4">April 2026</option>
              <option value="5">Mei 2026</option><option value="6">Juni 2026</option>
              <option value="7">Juli 2026</option><option value="8">Agustus 2026</option>
              <option value="9">September 2026</option><option value="10">Oktober 2026</option>
              <option value="11">November 2026</option><option value="12">Desember 2026</option>
            </select>
          </div>
          <div class="fg"><label class="fl">Nomor Surat *</label><input type="text" class="fc" id="mNomorSurat" placeholder="000.1.2.3/001/107.6/2026"></div>
        </div>
        <div id="mNoRkaWarn" style="display:none;" class="alert alert-warn"></div>
        <div class="fr2" style="margin-bottom:8px;">
          <div class="fg" style="margin-bottom:0;"><label class="fl">Pegawai yang Mengajukan *</label><input type="text" class="fc" id="mPihak" placeholder="Contoh: Shelshi"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Dari Seksi *</label>
            <select class="fc" id="mSeksi">
              <option value="">— Pilih Seksi —</option>
              <option value="Seksi Tata Usaha">Seksi Tata Usaha</option>
              <option value="Seksi Rehabilitasi Sosial">Seksi Rehabilitasi Sosial</option>
              <option value="Seksi Pelayanan">Seksi Pelayanan</option>
            </select>
          </div>
        </div>
        <div class="fr2">
          <div class="fg" style="margin-bottom:0;"><label class="fl">NIP</label><input type="text" class="fc" id="mNIP" placeholder="199712312020011001"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Jabatan</label><input type="text" class="fc" id="mJabatan" placeholder="Penata Laksana Barang"></div>
        </div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <div style="font-size:12px;font-weight:700;color:var(--text);">📦 Daftar Barang yang Diajukan</div>
        <button id="mItemCountBadge" onclick="toggleItemList()" style="font-size:10px;font-family:var(--mono);font-weight:700;color:var(--accent);background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:3px 11px;cursor:pointer;display:flex;align-items:center;gap:5px;">
          <span id="mItemCountText">0 item</span><span id="mItemToggleIcon" style="font-size:9px;color:var(--muted);">▼</span>
        </button>
      </div>
      <div id="mItemSummary" style="display:none;margin-bottom:8px;"></div>
      <div id="mItemList" class="items-list">
        <div id="mEmptyItems" style="text-align:center;padding:16px;color:var(--muted2);font-size:12px;border:1.5px dashed var(--border);border-radius:8px;">Belum ada barang. Tambahkan di bawah.</div>
      </div>
      <div id="mItemsTotal" class="items-total-bar" style="display:none;">
        <span class="itb-label">💰 Total Nilai Pengajuan</span>
        <span class="itb-val" id="mTotalNilai">—</span>
      </div>
      <div class="add-item-form">
        <div class="ait">+ Tambah Barang</div>
        <div class="fr2">
          <div class="fg"><label class="fl">Sub Kegiatan *</label><select class="fc" id="mSubKeg" onchange="populateItemSel()"><option value="">— Pilih —</option></select></div>
          <div class="fg"><label class="fl">Nama Barang *</label><select class="fc" id="mItemSel" onchange="onItemSelect()"><option value="">— Pilih Sub dulu —</option></select></div>
        </div>
        <div id="mItemInfo" style="display:none;margin-bottom:10px;">
          <div style="background:var(--surface);border:1.5px solid var(--border);border-radius:9px;overflow:hidden;">
            <div style="background:linear-gradient(90deg,#eff6ff,#f0fdf4);padding:8px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
              <span style="font-size:11px;font-weight:700;color:var(--accent);">📊 Data dari DPA</span>
              <div id="miStockPill" style="font-size:10px;font-weight:700;padding:2px 10px;border-radius:20px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0;border-bottom:1px solid var(--border);">
              <div style="padding:9px 14px;border-right:1px solid var(--border);">
                <div style="font-size:9.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Total yang dapat diusulkan</div>
                <div id="miVolRka" style="font-family:var(--mono);font-size:14px;font-weight:800;color:var(--accent);">—</div>
              </div>
              <div style="padding:9px 14px;border-right:1px solid var(--border);">
                <div style="font-size:9.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Sudah Diusulkan</div>
                <div id="miSudah" style="font-family:var(--mono);font-size:14px;font-weight:800;color:var(--orange);">—</div>
              </div>
              <div style="padding:9px 14px;">
                <div style="font-size:9.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Sisa yang dapat diusulkan</div>
                <div id="miSisa" style="font-family:var(--mono);font-size:14px;font-weight:800;">—</div>
              </div>
            </div>
            <div style="padding:6px 14px;" id="miValidMsg"></div>
          </div>
        </div>
        <!-- Harga Satuan otomatis dari RKA + badge -->
        <div class="fr4">
          <div class="fg" style="margin-bottom:0;"><label class="fl">Jumlah *</label><input type="number" class="fc" id="mVol" placeholder="10" min="1" oninput="onVolInput()"></div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Satuan</label><input type="text" class="fc" id="mSat" placeholder="Pcs / Rim"></div>
          <div class="fg" style="margin-bottom:0;">
            <label class="fl" style="display:flex;align-items:center;gap:6px;">
              Harga Satuan *
              <span id="mHargaAutoBadge" class="harga-auto-badge" style="display:none;">✨ Otomatis dari DPA</span>
            </label>
            <input type="number" class="fc" id="mHarga" placeholder="10000" min="0" oninput="onVolInput()">
          </div>
          <div class="fg" style="margin-bottom:0;"><label class="fl">Total</label><input type="text" class="fc" id="mTotal" readonly placeholder="—"></div>
        </div>
        <div class="fg" style="margin-top:10px;margin-bottom:0;"><label class="fl">Keterangan / Peruntukkan</label><input type="text" class="fc" id="mKet" placeholder="Spesifikasi, tujuan penggunaan, dll."></div>
        <div style="margin-top:10px;display:flex;justify-content:flex-end;"><button class="btn btn-accent btn-sm" onclick="addItemToList()">+ Tambahkan ke Daftar</button></div>
      </div>
    </div>
    <div class="mf" style="justify-content:space-between;align-items:center;">
      <span style="font-size:10.5px;color:var(--muted);">💡 Setelah simpan, form reset otomatis untuk pengajuan baru</span>
      <div style="display:flex;gap:7px;">
        <button class="btn btn-ghost btn-sm" onclick="closeModal('modalUsulan')">✕ Tutup</button>
        <button class="btn btn-orange" onclick="submitPengajuan()">✅ Simpan Pengajuan</button>
      </div>
    </div>
  </div>
</div>

<!-- ════ MODAL EXPORT USULAN PERMINTAAN ════ -->
<div class="mov" id="modalExportWord">
  <div class="modal lg" style="display:flex;flex-direction:column;max-height:90vh;overflow:hidden;">
    <div class="mh" style="position:relative;top:unset;">
      <h3>📄 Download Usulan Permintaan</h3>
      <button class="mc" onclick="closeModal('modalExportWord')">✕</button>
    </div>
    <div class="mb" style="overflow-y:auto;flex:1;">
      <div class="alert alert-info" style="margin-bottom:14px;">ℹ️ <span>Membuat file Usulan Permintaan <b>(.doc)</b> yang dapat dibuka di Microsoft Word / LibreOffice.</span></div>

      <!-- Kepada Yth — statis, tidak perlu diinput -->
      <div style="background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:9px;padding:11px 14px;margin-bottom:14px;">
        <div style="font-size:10px;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;">📌 Kepada Yth.</div>
        <div style="font-size:13px;font-weight:800;color:#0f172a;">Mirza Arintha Praharasty, S.Sos.</div>
        <div style="font-size:11.5px;color:#475569;margin-top:3px;">NIP. 19901008 201903 2 014</div>
        <div style="font-size:11.5px;color:#475569;margin-top:2px;">Kepala Sub Bagian Tata Usaha</div>
      </div>

      <!-- Hidden fields kepada — nilai hardcoded -->
      <input type="hidden" id="wKepada" value="Mirza Arintha Praharasty, S.Sos.">
      <input type="hidden" id="wNIPKepada" value="19901008 201903 2 014">
      <input type="hidden" id="wJabatanKepada" value="Kepala Sub Bagian Tata Usaha">

      <!-- Hanya input pihak yang mengetahui -->
      <p style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Pihak Yang Mengetahui</p>
      <div class="fg"><label class="fl">Nama Pihak Yang Mengetahui *</label><input type="text" class="fc" id="wNamaMengetahui" placeholder="Nama lengkap + gelar"></div>
      <div class="fr2">
        <div class="fg"><label class="fl">NIP Pihak Mengetahui</label><input type="text" class="fc" id="wNIPMengetahui" placeholder="Contoh: 19850101 201001 1 001"></div>
        <div class="fg"><label class="fl">Jabatan yang Mengetahui</label><input type="text" class="fc" id="wJabatanMengetahui" placeholder="Contoh: Kepala Seksi Pelayanan"></div>
      </div>

      <!-- Hidden fields dari pengajuan -->
      <input type="hidden" id="wBulan" value="">
      <input type="hidden" id="wDari" value="">
      <input type="hidden" id="wNIPDari" value="">
      <input type="hidden" id="wJabatanDari" value="">
      <input type="hidden" id="wSeksiDari" value="">
      <input type="hidden" id="wNomorND" value="">
      <input type="hidden" id="wFilterSub" value="">
      <input type="hidden" id="wSinglePengajuanId" value="">
    </div>
    <!-- Footer tombol selalu di bawah modal -->
    <div style="padding:12px 18px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:7px;background:#fff;flex-shrink:0;">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modalExportWord')">Batal</button>
      <button class="btn btn-green" onclick="doExportWord()">📄 Download Usulan Permintaan (.doc)</button>
    </div>
  </div>
</div>

<!-- ════ MODAL IMPORT RESULT ════ -->
<div class="mov" id="modalImportResult">
  <div class="modal xl">
    <div class="mh"><h3 id="mIRTitle">✅ Import Berhasil</h3><button class="mc" onclick="closeModal('modalImportResult')">✕</button></div>
    <div class="mb" id="mIRBody"></div>
    <div class="mf">
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modalImportResult')">Tutup</button>
      <button class="btn btn-accent" onclick="closeModal('modalImportResult');goPage('rka')">📑 Lihat Data DPA</button>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
const USER_ROLE = '<?= $roleJs ?>';
const USER_NAMA = '<?= $namaJs ?>';
const IS_ADMIN  = (USER_ROLE === 'admin');

const BLN=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const BLN_S=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

let state={pengajuan:[],rka_per_bulan:{}};
let dashBarInst=null,lapBarInst=null;
let riwayatSelectedMonth=null;
let modalItems=[];
let itemListExpanded=true;
let iwCurrentStep=1,iwSelectedBulan=null;
let importWorkbook=null,importSheetData=[],importHeaders=[];

// ─── FIELD_DEFS: tambah harga_satuan (opsional) ───
const FIELD_DEFS=[
  {key:'nama_sub',    label:'Sub Kegiatan', required:true},
  {key:'uraian',      label:'Uraian',       required:true},
  {key:'jumlah',      label:'Jumlah',       required:true},
  {key:'satuan',      label:'Satuan',       required:false},
  {key:'harga_satuan',label:'Harga Satuan', required:false},
];
let colMapping={};

// ─────────────────────────────────────────
//  PERSIST
// ─────────────────────────────────────────
function save(){try{localStorage.setItem('sipua_rsbg_v61',JSON.stringify(state));}catch(e){alert('Storage penuh.');}}
function load(){
  try{
    const r=localStorage.getItem('sipua_rsbg_v61');
    if(r){state={...state,...JSON.parse(r)};return;}
    const r6=localStorage.getItem('sipua_rsbg_v60');
    if(r6){const old=JSON.parse(r6);state={pengajuan:old.pengajuan||[],rka_per_bulan:old.rka_per_bulan||{}};save();return;}
    const r52=localStorage.getItem('sipua_rsbg_v52');
    if(r52){const old=JSON.parse(r52);if(old.pengajuan)state.pengajuan=old.pengajuan;if(old.rka_master&&old.rka_master.length)state.rka_per_bulan[0]=old.rka_master;save();}
  }catch(e){console.warn('Load error:',e);}
}
function uid(){return Math.random().toString(36).slice(2,9);}
function getMonth(){return parseInt(document.getElementById('globalMonth').value)||4;}
function formatRp(n){if(!n&&n!==0)return'—';return'Rp '+Number(n).toLocaleString('id-ID');}
function esc(s){if(!s&&s!==0)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

// ─────────────────────────────────────────
//  RKA HELPERS
// ─────────────────────────────────────────
function getRkaBulanList(){return Object.keys(state.rka_per_bulan||{}).map(Number).filter(b=>((state.rka_per_bulan[b]||[]).length>0)).sort((a,b)=>a-b);}
function getRkaForBulan(bulan){
  if(bulan===null||bulan==='all'||bulan===undefined){
    const all=[];Object.entries(state.rka_per_bulan||{}).forEach(([b,items])=>{(items||[]).forEach(it=>all.push({...it,_bulan:Number(b)}));});return all;
  }
  return((state.rka_per_bulan||{})[bulan]||[]).map(it=>({...it,_bulan:Number(bulan)}));
}
function getRkaForUsulan(bulan){
  const specific=getRkaForBulan(bulan);if(specific.length>0)return specific;return getRkaForBulan('all');
}
function getRkaSubList(items){
  const subs={};(items||[]).forEach(r=>{const k=r.kode_sub||r.nama_sub||'';if(!subs[k])subs[k]=r.nama_sub||k;});
  return Object.entries(subs).map(([kode,nama])=>({kode,nama}));
}
function getSudahUsul(item_key){
  let tot=0;(state.pengajuan||[]).forEach(p=>(p.items||[]).forEach(it=>{if(it.item_key===item_key)tot+=it.volume||0;}));return tot;
}
function getPengajuanBulan(bulan){return(state.pengajuan||[]).filter(p=>p.bulan==bulan);}
function hasBulanRka(bulan){return(state.rka_per_bulan[bulan]||[]).length>0;}

// ─────────────────────────────────────────
//  NAVIGATION
// ─────────────────────────────────────────
const PAGE_NAMES={dashboard:'Dashboard','import-rka':'Import RKA',rka:'Data RKA',subkegiatan:'Sub Kegiatan',usulan:'Usulan Bulanan',riwayat:'Riwayat per Bulan',laporan:'Laporan'};

function goPage(name){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  const pageEl=document.getElementById('page-'+name);
  if(pageEl)pageEl.classList.add('active');
  document.querySelectorAll('.nav-item').forEach(btn=>{if(btn.getAttribute('onclick')&&btn.getAttribute('onclick').includes("'"+name+"'"))btn.classList.add('active');});
  document.getElementById('tbTitle').textContent=PAGE_NAMES[name]||name;
  document.getElementById('breadActive').textContent=PAGE_NAMES[name]||name;
  if(name==='dashboard')renderDashboard();
  if(name==='import-rka'&&IS_ADMIN)renderImportPage();
  if(name==='rka')renderRkaTable();
  if(name==='subkegiatan')renderSubKegiatanPage();
  if(name==='usulan')renderUsulanPage();
  if(name==='riwayat')renderRiwayat();
  if(name==='laporan')renderLaporan();
}
function onMonthChange(){
  const pg=document.querySelector('.page.active');if(!pg)return;
  const id=pg.id.replace('page-','');
  if(id==='usulan')renderUsulanPage();
  if(id==='dashboard')renderDashboard();
  if(id==='subkegiatan')renderSubKegiatanPage();
}
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
document.querySelectorAll('.mov').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');}));

// ─────────────────────────────────────────
//  IMPORT RKA PAGE
// ─────────────────────────────────────────
function renderImportPage(){
  if(!IS_ADMIN)return;
  const rkaBulans=getRkaBulanList();
  const totalItems=Object.values(state.rka_per_bulan||{}).reduce((s,a)=>s+(a||[]).length,0);
  document.getElementById('importRkaStats').innerHTML=`
    <div class="sc sc-blue"><span class="ico">📅</span><div class="lbl">Bulan Tersimpan</div><div class="val">${rkaBulans.length}</div><div class="sub">dari 12 bulan</div></div>
    <div class="sc sc-green"><span class="ico">📋</span><div class="lbl">Total Item RKA</div><div class="val">${totalItems}</div><div class="sub">seluruh bulan</div></div>
    <div class="sc sc-orange"><span class="ico">🏛️</span><div class="lbl">Sub Kegiatan</div><div class="val">${getRkaSubList(getRkaForBulan('all')).length}</div><div class="sub">unik (semua bulan)</div></div>
    <div class="sc sc-teal"><span class="ico">✅</span><div class="lbl">Bulan Belum Import</div><div class="val">${12-rkaBulans.filter(b=>b>0).length}</div><div class="sub">bulan kosong</div></div>`;
  document.getElementById('rkaStorageSub').textContent=rkaBulans.length+' bulan tersimpan · '+totalItems+' total item';
  const gridEl=document.getElementById('rkaMonthGrid');
  if(!totalItems){gridEl.innerHTML='<div class="empty"><div class="ei">📭</div><p>Belum ada data DPA Klik <b>"+ Import DPA Baru"</b> untuk memulai.</p></div>';return;}
  let html='<div class="rka-month-grid">';
  for(let b=1;b<=12;b++){
    const items=(state.rka_per_bulan[b]||[]);const hasData=items.length>0;const subs=getRkaSubList(items);
    const hasHarga=items.some(r=>r.harga_satuan>0);
    html+=`<div class="rka-month-card${hasData?' has-data':''}">
      <div class="rmc-badge ${hasData?'b-green':'b-orange'}">${hasData?'✅ Tersimpan':'—'}</div>
      <div class="rmc-month">${BLN[b]}</div>
      <div class="rmc-items">${hasData?`<b>${items.length} item</b> · ${subs.length} sub kegiatan${hasHarga?' · <span style="color:var(--green);">💰 Ada harga</span>':''}` :'<span style="color:var(--muted2);">Belum ada data</span>'}</div>
      <div class="rmc-actions">
        ${hasData?`<button class="btn btn-ghost btn-xs" onclick="viewRkaBulan(${b})">👁️ Lihat</button><button class="btn btn-accent btn-xs" onclick="openImportWizard(${b})">🔄 Ganti</button><button class="btn btn-red btn-xs" onclick="confirmDelRka(${b})">🗑️</button>`
        :`<button class="btn btn-accent btn-xs" onclick="openImportWizard(${b})">+ Import</button>`}
      </div></div>`;
  }
  if((state.rka_per_bulan[0]||[]).length>0){
    const items=state.rka_per_bulan[0];
    html+=`<div class="rka-month-card has-data" style="border-color:#fcd34d;background:#fffbeb;">
      <div class="rmc-badge b-orange">⚠️ Legacy</div>
      <div class="rmc-month" style="color:#92400e;">Data Lama</div>
      <div class="rmc-items"><b>${items.length} item</b> — migrasi</div>
      <div class="rmc-actions"><button class="btn btn-ghost btn-xs" onclick="viewRkaBulan(0)">👁️ Lihat</button><button class="btn btn-red btn-xs" onclick="confirmDelRka(0)">🗑️ Hapus</button></div>
    </div>`;
  }
  html+='</div>';gridEl.innerHTML=html;
}
function viewRkaBulan(bulan){document.getElementById('rkaBulanFilter').value=bulan===0?'all':bulan;goPage('rka');}
function confirmDelRka(bulan){
  if(!IS_ADMIN)return;
  const items=(state.rka_per_bulan[bulan]||[]);const label=bulan===0?'Data Lama':(BLN[bulan]+' 2026');
  document.getElementById('delRkaMsg').innerHTML=`⚠️ Menghapus data RKA <b>${label}</b> (${items.length} item).`;
  document.getElementById('delRkaConfirmBtn').onclick=()=>doDelRka(bulan);openModal('modalDelRka');
}
function doDelRka(bulan){
  if(!IS_ADMIN)return;
  delete state.rka_per_bulan[bulan];save();closeModal('modalDelRka');renderImportPage();updateSubSelects();updateBadgeRka();
}
function openImportWizard(bulanTarget){
  if(!IS_ADMIN)return;
  iwCurrentStep=1;iwSelectedBulan=bulanTarget;importWorkbook=null;importSheetData=[];importHeaders=[];colMapping={};
  document.getElementById('rkaFileInput').value='';document.getElementById('filePreviewInfo').style.display='none';
  ['iwStep1','iwStep2','iwStep3','iwStep4'].forEach((id,i)=>document.getElementById(id).style.display=i===0?'':'none');
  if(bulanTarget!==null){document.getElementById('iwBulan').value=bulanTarget||4;document.getElementById('iwTitle').textContent='📤 Import DPA untuk Bulan '+BLN[bulanTarget||4];}
  else{document.getElementById('iwBulan').value=getMonth();document.getElementById('iwTitle').textContent='📤 Import DPA Baru';}
  checkIwExisting();updateStepBar(1);
  document.getElementById('importWizard').style.display='';
  document.getElementById('importWizard').scrollIntoView({behavior:'smooth',block:'start'});
}
function closeImportWizard(){document.getElementById('importWizard').style.display='none';}
function checkIwExisting(){
  const b=parseInt(document.getElementById('iwBulan').value);const el=document.getElementById('iwExistingWarn');
  if((state.rka_per_bulan[b]||[]).length>0){el.style.display='flex';el.innerHTML=`⚠️ <span>Bulan <b>${BLN[b]} 2026</b> sudah memiliki <b>${state.rka_per_bulan[b].length} item</b> DPA. Data lama akan <b>diganti</b>.</span>`;}
  else{el.style.display='none';}
}
document.getElementById('iwBulan')&&document.getElementById('iwBulan').addEventListener('change',checkIwExisting);
function updateStepBar(step){
  for(let i=1;i<=4;i++){
    const dot=document.getElementById('sd'+i);const lbl=document.getElementById('sl'+i);if(!dot)continue;
    if(i<step){dot.className='step-dot done';dot.textContent='✓';}
    else if(i===step){dot.className='step-dot active';dot.textContent=i;lbl.className='step-label active';}
    else{dot.className='step-dot idle';dot.textContent=i;lbl.className='step-label';}
  }
}
function iwNext1(){iwSelectedBulan=parseInt(document.getElementById('iwBulan').value);document.getElementById('iwTitle').textContent='📤 Import DPA — '+BLN[iwSelectedBulan]+' 2026';iwShowStep(2);}
function iwNext2(){if(!importSheetData.length){alert('Upload file terlebih dahulu.');return;}renderIwColMap();iwShowStep(3);}
function iwNext3(){for(const fd of FIELD_DEFS.filter(f=>f.required)){if(colMapping[fd.key]===''||colMapping[fd.key]===undefined){alert(`Field "${fd.label}" wajib dipetakan.`);return;}}renderIwPreview();iwShowStep(4);}
function iwBack(toStep){iwShowStep(toStep);}
function iwShowStep(s){
  iwCurrentStep=s;
  ['iwStep1','iwStep2','iwStep3','iwStep4'].forEach((id,i)=>{const el=document.getElementById(id);if(el)el.style.display=(i===s-1)?'':'none';});
  updateStepBar(s);
}
const uz=document.getElementById('uploadZone');
if(uz){
  uz.addEventListener('dragover',e=>{e.preventDefault();uz.classList.add('drag');});
  uz.addEventListener('dragleave',()=>uz.classList.remove('drag'));
  uz.addEventListener('drop',e=>{e.preventDefault();uz.classList.remove('drag');const f=e.dataTransfer.files[0];if(f)processRkaFile(f);});
}
function handleRkaFile(e){const f=e.target.files[0];if(f)processRkaFile(f);}
function processRkaFile(file){
  document.getElementById('filePreviewInfo').style.display='block';
  document.getElementById('fpFileName').textContent=file.name;
  document.getElementById('fpDetail').textContent='Memproses…';
  const reader=new FileReader();
  reader.onload=function(ev){
    try{
      const data=new Uint8Array(ev.target.result);
      importWorkbook=XLSX.read(data,{type:'array'});
      const sheetName=importWorkbook.SheetNames[0];const ws=importWorkbook.Sheets[sheetName];
      const raw=XLSX.utils.sheet_to_json(ws,{header:1,defval:''});
      let headerRowIdx=0;
      for(let i=0;i<Math.min(10,raw.length);i++){if(raw[i].filter(c=>String(c).trim()!=='').length>=3){headerRowIdx=i;break;}}
      importHeaders=raw[headerRowIdx].map(h=>String(h).trim());
      importSheetData=raw.slice(headerRowIdx+1).filter(r=>r.some(c=>String(c).trim()!==''));
      document.getElementById('fpDetail').textContent=`Sheet: "${sheetName}" · ${importSheetData.length} baris · ${importHeaders.length} kolom`;
      colMapping={};
      importHeaders.forEach((h,i)=>{
        const hl=h.toLowerCase();
        if(colMapping.nama_sub===undefined&&(hl.includes('sub kegiatan')||hl.includes('kegiatan')))colMapping.nama_sub=i;
        if(colMapping.uraian===undefined&&(hl.includes('uraian')||hl.includes('nama barang')||hl.includes('barang')||hl.includes('item')))colMapping.uraian=i;
        if(colMapping.jumlah===undefined&&(hl.includes('jumlah')||hl.includes('volume')||hl.includes('qty')))colMapping.jumlah=i;
        if(colMapping.satuan===undefined&&(hl.includes('satuan')||hl.includes('unit')))colMapping.satuan=i;
        if(colMapping.harga_satuan===undefined&&(hl.includes('harga')||hl.includes('price')||hl.includes('harga satuan')))colMapping.harga_satuan=i;
      });
      FIELD_DEFS.forEach(fd=>{if(colMapping[fd.key]===undefined)colMapping[fd.key]='';});
      const nextBtn=document.getElementById('iwNext2Btn');if(nextBtn){nextBtn.disabled=false;nextBtn.style.opacity='1';nextBtn.style.cursor='pointer';}
    }catch(err){document.getElementById('fpDetail').textContent='Error: '+err.message;}
  };
  reader.readAsArrayBuffer(file);
}
function renderIwColMap(){
  const optHtml='<option value="">— Tidak dipakai —</option>'+importHeaders.map((h,i)=>`<option value="${i}">${h}</option>`).join('');
  document.getElementById('iwColMapGrid').innerHTML=FIELD_DEFS.map(fd=>`
    <div class="lh">${fd.label}${fd.required?' <span style="color:var(--red);">*</span>':''}</div>
    <div class="arr">→</div>
    <select id="iwcm_${fd.key}" onchange="colMapping['${fd.key}']=this.value===''?'':parseInt(this.value);">${optHtml}</select>`).join('');
  FIELD_DEFS.forEach(fd=>{const sel=document.getElementById('iwcm_'+fd.key);if(sel)sel.value=(colMapping[fd.key]!==''&&colMapping[fd.key]!==undefined)?String(colMapping[fd.key]):'';});
}
function mapRow(row){const obj={};FIELD_DEFS.forEach(fd=>{const idx=colMapping[fd.key];obj[fd.key]=(idx!==''&&idx!==undefined)?String(row[idx]||'').trim():'';});return obj;}
function renderIwPreview(){
  const mapped=importSheetData.slice(0,8).map(row=>mapRow(row));const total=importSheetData.length;
  document.getElementById('iwPreviewCount').textContent=total+' baris data';
  document.getElementById('iwPreviewAlert').innerHTML=`<div class="alert alert-ok">✅ <span>Siap import <b>${total} baris</b> ke RKA bulan <b>${BLN[iwSelectedBulan]} 2026</b>.</span></div>`;
  document.getElementById('iwPreviewTable').innerHTML=`<table><thead><tr>${FIELD_DEFS.map(fd=>`<th>${fd.label}</th>`).join('')}</tr></thead><tbody>${mapped.map(r=>`<tr>${FIELD_DEFS.map(fd=>`<td style="font-size:11px;">${r[fd.key]||'<span style="color:var(--muted2);">—</span>'}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
}

// ─── doSaveRkaBulan: simpan harga_satuan ───
function doSaveRkaBulan(){
  if(!IS_ADMIN)return;
  if(!importSheetData.length){alert('Tidak ada data.');return;}
  for(const fd of FIELD_DEFS.filter(f=>f.required)){if(colMapping[fd.key]===''||colMapping[fd.key]===undefined){alert(`Field "${fd.label}" wajib dipetakan.`);return;}}
  const items=importSheetData.map(row=>{
    const obj=mapRow(row);
    obj.jumlah=parseFloat(String(obj.jumlah).replace(/[^0-9.]/g,''))||0;
    // Simpan harga_satuan dari Excel jika ada
    obj.harga_satuan=parseFloat(String(obj.harga_satuan||'').replace(/[^0-9.]/g,''))||0;
    obj.kode_sub=obj.nama_sub;obj.item_key=uid();return obj;
  }).filter(r=>r.uraian&&r.jumlah>0);
  if(!items.length){alert('Tidak ada data valid.');return;}
  const b=iwSelectedBulan;const prevCount=(state.rka_per_bulan[b]||[]).length;
  state.rka_per_bulan[b]=items;save();closeImportWizard();
  document.getElementById('mIRTitle').textContent='✅ Import Berhasil — '+BLN[b]+' 2026';
  const subs=getRkaSubList(items);
  const hasHarga=items.some(r=>r.harga_satuan>0);
  document.getElementById('mIRBody').innerHTML=`
    <div class="alert alert-ok" style="margin-bottom:14px;">✅ <b>${items.length} item RKA</b> berhasil disimpan untuk bulan <b>${BLN[b]} 2026</b>${prevCount>0?' (mengganti '+prevCount+' data lama)':''}.${hasHarga?' <b style="color:var(--green);">💰 Data harga satuan berhasil diimport.</b>':' <span style="color:var(--orange);">⚠️ Tidak ada data harga satuan.</span>'}</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">${subs.map(s=>{const it=items.filter(r=>(r.kode_sub||r.nama_sub)===s.kode);return`<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;"><div style="font-weight:700;font-size:12px;">${s.nama}</div><div style="font-size:11px;color:var(--muted);"><b style="color:var(--accent);">${it.length} item</b></div></div>`;}).join('')}</div>`;
  openModal('modalImportResult');renderImportPage();updateSubSelects();updateBadgeRka();
}

// ─────────────────────────────────────────
//  SUB SELECTS
// ─────────────────────────────────────────
function updateSubSelects(){
  const allSubs=getRkaSubList(getRkaForBulan('all'));
  const opts=allSubs.map(s=>`<option value="${s.kode}">${s.nama}</option>`).join('');
  ['filterRkaSub','filterUsulanSub','wFilterSub'].forEach(id=>{const el=document.getElementById(id);if(el)el.innerHTML='<option value="">Semua Sub Kegiatan</option>'+opts;});
  populateMSubKeg();
}
function populateMSubKeg(){
  const bln=parseInt(document.getElementById('mBulan').value)||getMonth();
  const rka=getRkaForUsulan(bln);const subs=getRkaSubList(rka);
  const el=document.getElementById('mSubKeg');
  if(el)el.innerHTML='<option value="">— Pilih —</option>'+subs.map(s=>`<option value="${s.kode}">${s.nama}</option>`).join('');
}
function updateBadgeRka(){
  const el=document.getElementById('sbBadgeRka');
  if(el)el.style.display=IS_ADMIN&&getRkaBulanList().length===0?'':'none';
}

// ─────────────────────────────────────────
//  SUB KEGIATAN PAGE
// ─────────────────────────────────────────
function renderSubKegiatanPage(){
  const rkaAll=getRkaForBulan('all');const subs=getRkaSubList(rkaAll);const hasRka=rkaAll.length>0;
  document.getElementById('skNoRka').style.display=hasRka?'none':'';
  document.getElementById('skPageSub').textContent=hasRka?`Daftar ${subs.length} sub kegiatan · ${rkaAll.length} total item RKA`:'Belum ada data RKA';
  document.getElementById('skSearch').value='';
  document.getElementById('skClearBtn').classList.remove('show');
  document.getElementById('skSearchResult').style.display='none';
}
function highlightMatch(text,q){
  if(!q)return esc(text);const escaped=esc(text);
  try{const safe=q.split('').map(function(c){return c.replace(/[-[\]{}()*+?.,\\^$|#]/,'\\$&');}).join('');const re=new RegExp('('+safe+')','gi');return escaped.replace(re,'<mark>$1</mark>');}catch(e){return escaped;}
}
function onSkSearch(){
  const q=(document.getElementById('skSearch').value||'').trim();
  const clearBtn=document.getElementById('skClearBtn');const resultWrap=document.getElementById('skSearchResult');
  if(!q){clearBtn.classList.remove('show');resultWrap.style.display='none';return;}
  clearBtn.classList.add('show');resultWrap.style.display='';
  const ql=q.toLowerCase();const rkaAll=getRkaForBulan('all');
  const matched=rkaAll.filter(r=>(r.uraian||'').toLowerCase().includes(ql));
  if(!matched.length){resultWrap.innerHTML=`<div class="sk-result-wrap"><div class="sk-result-header"><span class="skrh-title">🔍 Hasil: "<b>${esc(q)}</b>"</span><span class="badge b-orange">0 hasil</span></div><div class="empty" style="padding:24px;"><div class="ei">🔎</div><p>Tidak ada barang yang cocok.</p></div></div>`;return;}
  const rows=matched.map((r,i)=>`<div class="sk-result-item"><span class="sri-no">${i+1}</span><span class="sri-name">${highlightMatch(r.uraian||'—',q)}</span><span class="sri-sub">🏛️ ${esc(r.nama_sub||'—')}</span></div>`).join('');
  resultWrap.innerHTML=`<div class="sk-result-wrap"><div class="sk-result-header"><span class="skrh-title">🔍 Hasil: "<b>${esc(q)}</b>"</span><span class="badge b-info">${matched.length} barang ditemukan</span></div>${rows}</div>`;
}
function clearSkSearch(){document.getElementById('skSearch').value='';onSkSearch();document.getElementById('skSearch').focus();}

// ─────────────────────────────────────────
//  DASHBOARD
// ─────────────────────────────────────────
function renderDashboard(){
  const bln=getMonth();const hasRka=getRkaBulanList().length>0;
  const noRkaEl=document.getElementById('dashNoRka');
  if(!hasRka){
    noRkaEl.style.display='';
    noRkaEl.innerHTML=IS_ADMIN
      ?`<div class="alert alert-warn">⚠️ <span>Data RKA belum diimport. <b onclick="goPage('import-rka')" style="cursor:pointer;text-decoration:underline;">Klik di sini untuk Import RKA</b>.</span></div>`
      :`<div class="alert alert-warn">⚠️ <span>Data RKA belum tersedia. Hubungi <b>Administrator</b>.</span></div>`;
  } else {noRkaEl.style.display='none';}
  document.getElementById('dashSub').textContent='Ringkasan Usulan TA 2026 — Bulan Aktif: '+BLN[bln];
  document.getElementById('dashBulanNow').textContent=BLN[bln]+' 2026';
  document.getElementById('dashRkaBulanSub').textContent='RKA Bulan '+BLN[bln]+' 2026';
  const allP=state.pengajuan||[];const bP=allP.filter(p=>p.bulan==bln);
  const allItems=allP.flatMap(p=>p.items||[]);const bItems=bP.flatMap(p=>p.items||[]);
  const totalNilai=bItems.reduce((s,it)=>s+(it.total||0),0);
  const rkaAll=getRkaForBulan('all');
  document.getElementById('dashStats').innerHTML=`
    <div class="sc sc-blue"><span class="ico">📦</span><div class="lbl">Total Item RKA</div><div class="val">${rkaAll.length}</div><div class="sub">${getRkaBulanList().length} bulan diimport</div></div>
    <div class="sc sc-orange"><span class="ico">📋</span><div class="lbl">Total Pengajuan TA</div><div class="val">${allP.length}</div><div class="sub">${allItems.length} item barang</div></div>
    <div class="sc sc-green"><span class="ico">✅</span><div class="lbl">Pengajuan Bulan Ini</div><div class="val">${bP.length}</div><div class="sub">${bItems.length} item · ${BLN_S[bln]}</div></div>
    <div class="sc sc-teal"><span class="ico">💰</span><div class="lbl">Nilai Bulan Ini</div><div class="val" style="font-size:11px;">${totalNilai>0?formatRp(totalNilai):'—'}</div><div class="sub">${BLN_S[bln]} 2026</div></div>`;
  document.getElementById('dashMonthSummary').innerHTML=bP.length===0
    ?'<div class="empty" style="padding:20px;"><div class="ei">📭</div><p>Belum ada pengajuan bulan ini.</p></div>'
    :`<div style="display:flex;flex-direction:column;gap:8px;font-size:12.5px;"><div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);"><span>Jumlah Pengajuan</span><b class="mono">${bP.length}</b></div><div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);"><span>Jumlah Item Barang</span><b class="mono">${bItems.length}</b></div><div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:var(--green);">💰 Total Nilai</span><b class="mono" style="color:var(--green);">${formatRp(totalNilai)}</b></div></div>`;
  const labels=BLN.slice(1);const counts=Array.from({length:12},(_,i)=>(state.pengajuan||[]).filter(p=>p.bulan==i+1).length);
  const ctx=document.getElementById('dashBar');if(dashBarInst)dashBarInst.destroy();
  dashBarInst=new Chart(ctx,{type:'bar',data:{labels,datasets:[{data:counts,backgroundColor:counts.map((_,i)=>i===bln-1?'#2563eb':'#93c5fd'),borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{font:{size:10}}},y:{ticks:{stepSize:1,font:{size:10}}}}}});
  const rkaForBln=getRkaForBulan(bln).length>0?getRkaForBulan(bln):getRkaForBulan('all');
  const subs=getRkaSubList(rkaForBln);
  document.getElementById('dashSubTbody').innerHTML=subs.length===0
    ?`<tr><td colspan="5"><div class="empty" style="padding:16px;"><p>Belum ada data RKA.</p></div></td></tr>`
    :subs.map(s=>{
      const items=rkaForBln.filter(r=>(r.kode_sub||r.nama_sub)===s.kode);
      const diusulkan=items.filter(r=>getSudahUsul(r.item_key)>0).length;
      const pct=items.length>0?Math.round(diusulkan/items.length*100):0;
      const barC=pct>=80?'#059669':pct>=40?'#f59e0b':'#2563eb';
      return`<tr style="cursor:pointer;" onclick="goPage('subkegiatan')"><td><div style="font-weight:600;font-size:12px;">${s.nama}</div></td><td class="mono">${items.length}</td><td class="mono" style="color:var(--green);">${diusulkan}</td><td class="mono" style="color:var(--muted);">${items.length-diusulkan}</td><td><div style="font-size:10px;margin-bottom:2px;">${pct}%</div><div class="pb"><div class="pf" style="width:${pct}%;background:${barC};"></div></div></td></tr>`;
    }).join('');
}

// ─────────────────────────────────────────
//  DATA RKA TABLE — tidak menampilkan harga
// ─────────────────────────────────────────
let rkaPage=1;
function renderRkaTable(){
  const filterBulan=document.getElementById('rkaBulanFilter').value;const bulanNum=filterBulan==='all'?null:parseInt(filterBulan);
  let list=getRkaForBulan(bulanNum===null?'all':bulanNum);const hasRka=list.length>0;
  const noBanner=document.getElementById('rkaNoBanner');
  if(!hasRka){noBanner.style.display='';noBanner.innerHTML=IS_ADMIN?`<div class="alert alert-err">🚫 <span>Belum ada data DPA. <b onclick="goPage('import-rka')" style="cursor:pointer;text-decoration:underline;">Import data DPA</b> terlebih dahulu.</span></div>`:`<div class="alert alert-err">🚫 <span>Belum ada data DPA. Hubungi Administrator.</span></div>`;}
  else{noBanner.style.display='none';}
  document.getElementById('rkaFilterInfo').textContent=bulanNum?BLN[bulanNum]+' 2026':'Semua Bulan';
  const q=(document.getElementById('filterRka')||{}).value||'';const sub=(document.getElementById('filterRkaSub')||{}).value||'';
  const pp=parseInt((document.getElementById('filterRkaPP')||{}).value)||25;
  if(sub)list=list.filter(r=>(r.kode_sub||r.nama_sub)===sub);
  if(q){const l=q.toLowerCase();list=list.filter(r=>(r.uraian||'').toLowerCase().includes(l)||(r.nama_sub||'').toLowerCase().includes(l));}
  const total=list.length;const maxP=Math.ceil(total/pp)||1;if(rkaPage>maxP)rkaPage=maxP;
  const shown=list.slice((rkaPage-1)*pp,rkaPage*pp);
  document.getElementById('rkaCountStat').textContent=list.length;document.getElementById('rkaSubCount').textContent=getRkaSubList(list).length;
  document.getElementById('rkaUsulCount').textContent=list.filter(r=>getSudahUsul(r.item_key)>0).length;
  document.getElementById('rkaCardSub').textContent=`TA 2026${bulanNum?' · '+BLN[bulanNum]:' · Semua Bulan'} · ${list.length} item`;
  // Harga satuan TIDAK ditampilkan di tabel Data RKA
  document.getElementById('rkaTbody').innerHTML=shown.length===0
    ?`<tr><td colspan="8"><div class="empty"><div class="ei">📤</div><p>Belum ada data DPA</p></div></td></tr>`
    :shown.map((r,i)=>{
      const sudah=getSudahUsul(r.item_key);const sisa=r.jumlah-sudah;const sisaColor=sisa<=0?'var(--red)':sisa<r.jumlah*0.2?'var(--orange)':'var(--green)';const blnLabel=r._bulan>0?BLN_S[r._bulan]:'—';
      return`<tr><td class="muted">${(rkaPage-1)*pp+i+1}</td><td><span class="tag-month">${blnLabel}</span></td><td><div style="font-size:11px;font-weight:700;color:var(--accent);">${r.nama_sub||'—'}</div></td><td><div style="font-weight:600;font-size:12px;">${r.uraian||'—'}</div></td><td class="mono" style="text-align:right;">${r.jumlah||0}</td><td class="muted">${r.satuan||'—'}</td><td class="mono" style="color:${sudah>0?'var(--orange)':'var(--muted2)'};text-align:right;">${sudah>0?sudah:'—'}</td><td class="mono" style="color:${sisaColor};font-weight:700;text-align:right;">${sisa}</td></tr>`;
    }).join('');
  document.getElementById('rkaInfo').textContent=`Menampilkan ${shown.length?((rkaPage-1)*pp+1):0}–${Math.min(rkaPage*pp,total)} dari ${total} item`;
  let pbHtml='';for(let p=1;p<=Math.min(maxP,5);p++)pbHtml+=`<button class="pb-btn${p===rkaPage?' active':''}" onclick="rkaPage=${p};renderRkaTable();">${p}</button>`;
  if(maxP>5)pbHtml+=`<button class="pb-btn" onclick="rkaPage=${maxP};renderRkaTable();">›</button>`;
  document.getElementById('rkaPagiBtn').innerHTML=pbHtml;
}

// ─────────────────────────────────────────
//  USULAN PAGE
// ─────────────────────────────────────────
function renderUsulanPage(){
  const bln=getMonth();const rka=getRkaForUsulan(bln);const hasRka=rka.length>0;const hasSpecificRka=hasBulanRka(bln);
  const noRkaEl=document.getElementById('usulanNoRka');
  if(!hasRka){noRkaEl.style.display='';noRkaEl.innerHTML=IS_ADMIN?`<div class="alert alert-err">🚫 <span>Belum ada data DPA untuk bulan <b>${BLN[bln]}</b>. <b onclick="goPage('import-rka')" style="cursor:pointer;text-decoration:underline;">Import data DPA</b> terlebih dahulu.</span></div>`:`<div class="alert alert-err">🚫 <span>Belum ada data DPA untuk bulan <b>${BLN[bln]}</b>. Hubungi Administrator.</span></div>`;}
  else{noRkaEl.style.display='none';}
  document.getElementById('usulanSubtitle').textContent='Input usulan — Bulan Aktif: '+BLN[bln]+' 2026'+(hasSpecificRka?' (DPA '+BLN[bln]+' tersedia)':' (menggunakan DPA gabungan)');
  document.getElementById('usulanBulanLabel').textContent=BLN[bln]+' 2026';
  const bP=getPengajuanBulan(bln);const bItems=bP.flatMap(p=>p.items||[]);const totalNilai=bItems.reduce((s,it)=>s+(it.total||0),0);
  document.getElementById('usulanStats').innerHTML=`
    <div class="sc sc-blue"><span class="ico">📋</span><div class="lbl">Jumlah Pengajuan</div><div class="val">${bP.length}</div><div class="sub">${BLN_S[bln]} 2026</div></div>
    <div class="sc sc-orange"><span class="ico">📦</span><div class="lbl">Total Item Barang</div><div class="val">${bItems.length}</div><div class="sub">dari ${bP.length} pengajuan</div></div>
    <div class="sc sc-green"><span class="ico">🏛️</span><div class="lbl">Sub Kegiatan</div><div class="val">${[...new Set(bItems.map(it=>it.kode_sub||it.nama_sub))].length}</div><div class="sub">tercakup bulan ini</div></div>
    <div class="sc sc-teal"><span class="ico">💰</span><div class="lbl">Total Nilai</div><div class="val" style="font-size:11px;">${totalNilai>0?formatRp(totalNilai):'—'}</div><div class="sub">semua pengajuan</div></div>`;
  document.getElementById('usulanMonthStrip').innerHTML=`
    <div class="ms-item">📅 Bulan Aktif: <b>${BLN[bln]} 2026</b></div>
    <div class="ms-item">📋 Pengajuan: <b>${bP.length}</b></div>
    <div class="ms-item">📦 Item: <b>${bItems.length}</b></div>
    <div class="ms-item">💰 Total: <b>${totalNilai>0?formatRp(totalNilai):'—'}</b></div>
    <div class="ms-item" style="font-size:10.5px;color:${hasSpecificRka?'var(--green)':'var(--orange)'};">${hasSpecificRka?'✅ DPA '+BLN[bln]+' tersedia':'⚠️ Menggunakan DPA gabungan'}</div>
    <div class="ms-item" style="margin-left:auto;"><button class="btn btn-ghost btn-xs" onclick="goPage('subkegiatan')">🏛️ Cek Stok Sub Kegiatan</button></div>`;
  renderUsulanTable();
  const subs=getRkaSubList(rka);
  document.getElementById('filterUsulanSub').innerHTML='<option value="">Semua Sub</option>'+subs.map(s=>`<option value="${s.kode}">${s.nama}</option>`).join('');
}

const NCOL=9;
function mkHeaderRow(p,pi,items,subTotal,delFn){
  const tgl=new Date(p.tgl).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
  return`<tr class="group-row-header"><td colspan="${NCOL}" style="padding:8px 14px;"><div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;"><div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;"><span class="tag-group">#${pi+1}</span><span class="tag-pihak">👤 ${esc(p.pihak||'—')}</span>${p.seksi?`<span class="tag-seksi">🏢 ${esc(p.seksi)}</span>`:''} ${p.nip?`<span class="tag-nip">NIP: ${esc(p.nip)}</span>`:''} ${p.jabatan?`<span class="tag-jabatan">${esc(p.jabatan)}</span>`:''}<span class="tag-surat">📄 ${esc(p.nomor_surat||'—')}</span><span style="font-size:10px;color:var(--muted);">🗓 ${tgl}</span><span style="font-size:10px;color:var(--muted);">${items.length} item</span>${subTotal>0?`<span style="font-size:10.5px;font-weight:700;color:var(--green);font-family:var(--mono);">${formatRp(subTotal)}</span>`:''}</div><div style="display:flex;align-items:center;gap:5px;flex-shrink:0;"><button class="btn-nota" style="background:#fef3c7;border-color:#fcd34d;color:#92400e;" onclick="openEditPengajuan('${p.id}')">✏️ Edit</button><button class="btn-nota" onclick="openExportWordSingle('${p.id}')">⬇️ Usulan Permintaan</button><button onclick="${delFn}('${p.id}')" style="padding:3px 8px;background:#fef2f2;color:var(--red);border:1px solid #fca5a5;border-radius:5px;font-size:10px;font-weight:700;cursor:pointer;white-space:nowrap;">🗑️ Hapus</button></div></div></td></tr>`;
}
function mkItemRow(it,no){
  const harga=it.harga||0;const total=it.total||(it.volume*harga)||0;
  return`<tr class="group-row-item"><td class="muted" style="padding-left:18px;font-size:11px;">${no}</td><td style="font-size:10.5px;font-weight:700;color:var(--accent);">${esc(it.nama_sub||'—')}</td><td style="font-weight:600;font-size:12px;">${esc(it.uraian||'—')}</td><td class="mono" style="text-align:right;">${it.volume}</td><td class="muted">${esc(it.satuan||'—')}</td><td class="col-harga">${harga>0?formatRp(harga):'<span style="color:var(--muted2);">—</span>'}</td><td class="col-total">${total>0?formatRp(total):'<span style="color:var(--muted2);">—</span>'}</td><td style="font-size:11px;color:var(--muted);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${esc(it.keterangan||'')}">${esc(it.keterangan||'—')}</td><td></td></tr>`;
}
function renderUsulanTable(){
  const bln=getMonth();const q=(document.getElementById('filterUsulan')||{}).value||'';const sub=(document.getElementById('filterUsulanSub')||{}).value||'';
  let bP=getPengajuanBulan(bln);const tbody=document.getElementById('usulanTbody');
  if(!bP.length){tbody.innerHTML=`<tr><td colspan="${NCOL}"><div class="empty"><div class="ei">📭</div><p>Belum ada pengajuan bulan ini. Klik "+ Buat Pengajuan Baru".</p></div></td></tr>`;document.getElementById('usulanInfo').textContent='—';return;}
  let rows=[];let globalNo=1;let grandTotal=0;
  bP.forEach((p,pi)=>{
    let items=p.items||[];
    if(sub)items=items.filter(it=>(it.kode_sub||it.nama_sub)===sub);
    if(q){const l=q.toLowerCase();const matchH=(p.pihak||'').toLowerCase().includes(l)||(p.seksi||'').toLowerCase().includes(l)||(p.nip||'').toLowerCase().includes(l)||(p.jabatan||'').toLowerCase().includes(l)||(p.nomor_surat||'').toLowerCase().includes(l);if(!matchH)items=items.filter(it=>(it.uraian||'').toLowerCase().includes(l));}
    if(!items.length)return;
    const subTotal=items.reduce((s,it)=>s+(it.total||0),0);grandTotal+=subTotal;
    rows.push(mkHeaderRow(p,pi,items,subTotal,'delPengajuan'));
    items.forEach(it=>rows.push(mkItemRow(it,globalNo++)));
    if(items.length>1)rows.push(`<tr class="group-row-total"><td colspan="6" style="padding:5px 14px;font-size:10.5px;font-weight:700;color:#166534;text-align:right;">Subtotal #${pi+1} (${items.length} item)</td><td class="mono" style="font-weight:800;color:#059669;text-align:right;padding:5px 14px;">${formatRp(subTotal)}</td><td colspan="2"></td></tr>`);
  });
  if(!rows.length){tbody.innerHTML=`<tr><td colspan="${NCOL}"><div class="empty"><div class="ei">📭</div><p>Tidak ada data sesuai filter.</p></div></td></tr>`;document.getElementById('usulanInfo').textContent='—';return;}
  if(grandTotal>0)rows.push(`<tr style="background:#1a2d6b;color:#fff;"><td colspan="6" style="padding:9px 14px;font-size:11px;font-weight:800;text-align:right;letter-spacing:.3px;">GRAND TOTAL ${BLN[bln].toUpperCase()} 2026</td><td style="padding:9px 14px;font-family:var(--mono);font-weight:800;font-size:13px;text-align:right;color:#fbbf24;">${formatRp(grandTotal)}</td><td colspan="2"></td></tr>`);
  tbody.innerHTML=rows.join('');
  document.getElementById('usulanInfo').textContent=`${bP.length} pengajuan · ${bP.flatMap(p=>p.items||[]).length} item${grandTotal>0?' · '+formatRp(grandTotal):''}`;
}

// ─────────────────────────────────────────
//  MODAL PENGAJUAN — harga otomatis dari RKA
// ─────────────────────────────────────────
function openModalUsulan(){
  const bln=getMonth();const rka=getRkaForUsulan(bln);
  if(!rka.length){if(IS_ADMIN&&confirm('Belum ada data DPA. Pergi ke halaman Import DPA?')){goPage('import-rka');}else if(!IS_ADMIN)alert('Belum ada data RKA. Hubungi Administrator.');return;}
  modalItems=[];itemListExpanded=true;
  document.getElementById('mBulan').value=bln;
  ['mNomorSurat','mPihak','mNIP','mJabatan'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('mSeksi').value='';
  onMBulanChange();
  document.getElementById('mItemInfo').style.display='none';
  ['mVol','mSat','mHarga','mTotal','mKet'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('mHargaAutoBadge').style.display='none';
  const ve=document.getElementById('miValidMsg');if(ve)ve.innerHTML='';
  clearSenderErr();renderModalItems();openModal('modalUsulan');
  setTimeout(()=>document.getElementById('mPihak').focus(),120);
}
function onMBulanChange(){
  const bln=parseInt(document.getElementById('mBulan').value)||getMonth();const rka=getRkaForUsulan(bln);const warnEl=document.getElementById('mNoRkaWarn');
  if(!rka.length){warnEl.style.display='flex';warnEl.innerHTML=IS_ADMIN?`⚠️ <span>Belum ada DPA untuk bulan <b>${BLN[bln]}</b>. <b onclick="closeModal('modalUsulan');goPage('import-rka');" style="cursor:pointer;text-decoration:underline;">Import DPA dulu</b></span>`:`⚠️ <span>Belum ada DPA untuk bulan <b>${BLN[bln]}</b>. Hubungi Administrator.</span>`;}
  else{warnEl.style.display='none';}
  populateMSubKeg();
  document.getElementById('mItemSel').innerHTML='<option value="">— Pilih Sub dulu —</option>';
  document.getElementById('mItemInfo').style.display='none';
}
function resetItemForm(){
  document.getElementById('mItemSel').value='';document.getElementById('mItemInfo').style.display='none';
  const ve=document.getElementById('miValidMsg');if(ve)ve.innerHTML='';
  ['mVol','mSat','mHarga','mTotal','mKet'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('mHargaAutoBadge').style.display='none';
  setTimeout(()=>{const el=document.getElementById('mItemSel');if(el)el.focus();},60);
}
function toggleItemList(){if(!modalItems.length)return;itemListExpanded=!itemListExpanded;refreshListDisplay();}
function refreshListDisplay(){
  const listEl=document.getElementById('mItemList');const sumEl=document.getElementById('mItemSummary');
  const icon=document.getElementById('mItemToggleIcon');const badge=document.getElementById('mItemCountBadge');
  const totalBar=document.getElementById('mItemsTotal');const countText=document.getElementById('mItemCountText');
  if(!modalItems.length){
    itemListExpanded=true;listEl.style.display='';sumEl.style.display='none';sumEl.innerHTML='';
    if(icon)icon.textContent='▼';
    const emptyEl=document.getElementById('mEmptyItems');
    if(emptyEl){listEl.innerHTML='';listEl.appendChild(emptyEl);emptyEl.style.display='';}
    if(totalBar)totalBar.style.display='none';if(countText)countText.textContent='0 item';
    if(badge){badge.style.background='var(--surface2)';badge.style.borderColor='var(--border)';badge.style.color='var(--muted)';}return;
  }
  const grandTotal=modalItems.reduce((s,it)=>s+(it.total||0),0);
  if(countText)countText.textContent=modalItems.length+' item';
  if(badge){badge.style.background='#eff6ff';badge.style.borderColor='#bfdbfe';badge.style.color='var(--accent)';}
  if(totalBar)totalBar.style.display='flex';document.getElementById('mTotalNilai').textContent=formatRp(grandTotal);
  if(icon)icon.textContent=itemListExpanded?'▼':'▲';
  if(itemListExpanded){
    listEl.style.display='';sumEl.style.display='none';
    const emptyEl2=document.getElementById('mEmptyItems');if(emptyEl2)emptyEl2.style.display='none';
    listEl.innerHTML=modalItems.map((it,i)=>{
      const harga=it.harga||0;const total=it.total||0;
      return'<div style="background:var(--surface);border:1.5px solid var(--border);border-radius:9px;padding:10px 13px;display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:6px;">'
        +'<div style="flex:1;min-width:0;"><div style="display:flex;align-items:center;gap:7px;margin-bottom:3px;flex-wrap:wrap;"><span style="background:var(--navy);color:#fff;font-size:9px;font-weight:800;border-radius:4px;padding:1px 7px;font-family:var(--mono);">#'+(i+1)+'</span><span style="font-size:12.5px;font-weight:700;">'+esc(it.uraian)+'</span></div>'
        +'<div style="font-size:10px;font-weight:600;color:var(--accent);margin-bottom:5px;">📂 '+esc(it.nama_sub)+'</div>'
        +'<div style="display:flex;align-items:center;gap:6px;font-size:11.5px;flex-wrap:wrap;"><b class="mono">'+it.volume+' '+(it.satuan||'unit')+'</b><span style="color:var(--muted2);">×</span><span class="mono">'+(harga>0?formatRp(harga):'—')+'</span><span style="color:var(--muted2);">=</span><b class="mono" style="color:var(--green);font-size:13px;">'+(total>0?formatRp(total):'—')+'</b></div>'
        +(it.keterangan?'<div style="font-size:10px;color:var(--muted);font-style:italic;margin-top:4px;">📝 '+esc(it.keterangan)+'</div>':'')+'</div>'
        +'<button data-idx="'+i+'" class="rm-modal-item" style="flex-shrink:0;background:none;border:none;cursor:pointer;color:var(--muted2);font-size:16px;padding:4px 6px;border-radius:5px;line-height:1;">✕</button></div>';
    }).join('');
  } else {
    listEl.style.display='none';sumEl.style.display='';
    const rows=modalItems.map((it,i)=>'<tr><td style="padding:5px 10px;font-size:10px;color:var(--muted);font-family:var(--mono);">#'+(i+1)+'</td><td style="padding:5px 10px;font-size:11.5px;font-weight:600;">🟢 '+esc(it.uraian)+'</td><td style="padding:5px 10px;font-size:10.5px;color:var(--muted);white-space:nowrap;">'+it.volume+' '+(it.satuan||'unit')+'</td><td style="padding:5px 10px;font-size:11px;font-family:var(--mono);text-align:right;white-space:nowrap;">'+(it.harga>0?formatRp(it.harga):'—')+'</td><td style="padding:5px 10px;font-size:11.5px;font-family:var(--mono);font-weight:700;color:var(--green);text-align:right;white-space:nowrap;">'+(it.total>0?formatRp(it.total):'—')+'</td><td style="padding:5px 6px;"><button data-idx="'+i+'" class="rm-modal-item" style="background:none;border:none;cursor:pointer;color:var(--muted2);font-size:13px;padding:2px 6px;border-radius:4px;">✕</button></td></tr>').join('');
    sumEl.innerHTML='<div style="background:var(--surface);border:1.5px solid var(--border);border-radius:9px;overflow:hidden;"><table style="width:100%;border-collapse:collapse;"><thead><tr style="background:var(--surface2);"><th style="padding:6px 10px;font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;text-align:left;">No</th><th style="padding:6px 10px;font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;text-align:left;">Nama Barang</th><th style="padding:6px 10px;font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;text-align:left;">Volume</th><th style="padding:6px 10px;font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;text-align:right;">Harga Sat.</th><th style="padding:6px 10px;font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;text-align:right;">Jumlah</th><th style="width:30px;"></th></tr></thead><tbody>'+rows+'</tbody><tfoot><tr style="background:#f0fdf4;border-top:2px solid #86efac;"><td colspan="4" style="padding:7px 10px;font-size:11px;font-weight:700;color:#166534;text-align:right;">Total ('+modalItems.length+' item)</td><td style="padding:7px 10px;font-family:var(--mono);font-weight:800;font-size:13px;color:#059669;text-align:right;">'+formatRp(grandTotal)+'</td><td></td></tr></tfoot></table></div>';
  }
  [listEl,sumEl].forEach(container=>{
    container.querySelectorAll('.rm-modal-item').forEach(btn=>{
      btn.onclick=function(){modalItems.splice(parseInt(this.getAttribute('data-idx')),1);refreshListDisplay();};
      btn.onmouseover=function(){this.style.color='var(--red)';this.style.background='#fee2e2';};
      btn.onmouseout=function(){this.style.color='var(--muted2)';this.style.background='none';};
    });
  });
}
function renderModalItems(){refreshListDisplay();}
function populateItemSel(){
  const sub=document.getElementById('mSubKeg').value;const bln=parseInt(document.getElementById('mBulan').value)||getMonth();
  const sel=document.getElementById('mItemSel');sel.innerHTML='<option value="">— Pilih nama barang —</option>';if(!sub)return;
  const rka=getRkaForUsulan(bln);
  rka.filter(r=>(r.kode_sub||r.nama_sub)===sub).forEach(r=>{
    const sudah=getSudahUsul(r.item_key);const sudahDiModal=modalItems.filter(it=>it.item_key===r.item_key).reduce((s,it)=>s+(it.volume||0),0);const sisa=r.jumlah-sudah-sudahDiModal;
    const icon=sisa<=0?'🔴':sisa<r.jumlah*0.2?'🟡':'🟢';
    sel.innerHTML+=`<option value="${r.item_key}">${icon} ${r.uraian} — sisa: ${sisa<=0?'Habis':sisa+' '+r.satuan}</option>`;
  });
  document.getElementById('mItemInfo').style.display='none';document.getElementById('miValidMsg').innerHTML='';
  document.getElementById('mVol').value='';document.getElementById('mHarga').value='';document.getElementById('mTotal').value='';
  document.getElementById('mHargaAutoBadge').style.display='none';
}

// ─── onItemSelect: isi harga otomatis dari RKA ───
function onItemSelect(){
  const v=document.getElementById('mItemSel').value;
  const autoBadge=document.getElementById('mHargaAutoBadge');
  if(!v){document.getElementById('mItemInfo').style.display='none';autoBadge.style.display='none';return;}
  const bln=parseInt(document.getElementById('mBulan').value)||getMonth();const rka=getRkaForUsulan(bln);const rkaItem=rka.find(r=>r.item_key===v);if(!rkaItem)return;
  const sudah=getSudahUsul(rkaItem.item_key);const sudahDiModal=modalItems.filter(it=>it.item_key===v).reduce((s,it)=>s+(it.volume||0),0);const sisa=rkaItem.jumlah-sudah-sudahDiModal;
  document.getElementById('miVolRka').textContent=rkaItem.jumlah+' '+(rkaItem.satuan||'');
  document.getElementById('miSudah').textContent=(sudah+sudahDiModal)>0?(sudah+sudahDiModal)+' '+(rkaItem.satuan||''):'—';
  document.getElementById('miSisa').textContent=sisa<=0?'Habis':sisa+' '+(rkaItem.satuan||'');
  document.getElementById('miSisa').style.color=sisa<=0?'var(--red)':sisa<rkaItem.jumlah*0.2?'var(--orange)':'var(--green)';
  const pill=document.getElementById('miStockPill');
  if(sisa<=0){pill.textContent='🔴 Stok Habis';pill.style.cssText='font-size:10px;font-weight:700;padding:2px 10px;border-radius:20px;background:#fee2e2;color:#991b1b;';}
  else if(sisa<rkaItem.jumlah*0.2){pill.textContent='🟡 Stok Hampir Habis';pill.style.cssText='font-size:10px;font-weight:700;padding:2px 10px;border-radius:20px;background:#fef9c3;color:#92400e;';}
  else{pill.textContent='🟢 Stok Tersedia';pill.style.cssText='font-size:10px;font-weight:700;padding:2px 10px;border-radius:20px;background:#d1fae5;color:#065f46;';}
  document.getElementById('mItemInfo').style.display='block';
  document.getElementById('mSat').value=rkaItem.satuan||'';

  // ─── Isi harga satuan otomatis dari RKA ───
  if(rkaItem.harga_satuan&&rkaItem.harga_satuan>0){
    document.getElementById('mHarga').value=rkaItem.harga_satuan;
    autoBadge.style.display='inline-flex';
  } else {
    // Tidak ada harga di RKA — biarkan user isi manual
    document.getElementById('mHarga').value='';
    autoBadge.style.display='none';
  }

  if(!document.getElementById('mVol').value||document.getElementById('mVol').value==='0')document.getElementById('mVol').value=sisa>0?1:0;
  document.getElementById('miValidMsg').innerHTML='';
  onVolInput();
  setTimeout(()=>document.getElementById('mVol').select(),60);
}

// ─── onVolInput: update total ───
function onVolInput(){
  const v=document.getElementById('mItemSel').value;const vol=parseFloat(document.getElementById('mVol').value)||0;const harga=parseFloat(document.getElementById('mHarga').value)||0;
  document.getElementById('mTotal').value=(vol&&harga)?formatRp(vol*harga):'';if(!v||!vol)return;
  const bln=parseInt(document.getElementById('mBulan').value)||getMonth();const rka=getRkaForUsulan(bln);const rkaItem=rka.find(r=>r.item_key===v);if(!rkaItem)return;
  const sudahTersimpan=getSudahUsul(rkaItem.item_key);const sudahDiModal=modalItems.filter(it=>it.item_key===v).reduce((s,it)=>s+(it.volume||0),0);const sisaEfektif=rkaItem.jumlah-sudahTersimpan-sudahDiModal;
  const validEl=document.getElementById('miValidMsg');
  if(sisaEfektif<=0){validEl.innerHTML=`<div style="background:#fff5f5;border:1.5px solid #fca5a5;border-radius:8px;padding:9px 13px;display:flex;align-items:flex-start;gap:8px;">🚫 <div><div style="font-weight:700;color:#991b1b;font-size:12px;">Stok habis!</div><div style="color:#b91c1c;font-size:11.5px;margin-top:2px;">Item ini sudah mencapai batas RKA <b>(${rkaItem.jumlah} ${rkaItem.satuan||''})</b>.</div></div></div>`;}
  else if(vol>sisaEfektif){validEl.innerHTML=`<div style="background:#fffbeb;border:1.5px solid #fcd34d;border-radius:8px;padding:9px 13px;display:flex;align-items:flex-start;gap:8px;">⚠️ <div style="flex:1;"><div style="font-weight:700;color:#92400e;font-size:12px;">Jumlah melebihi sisa stok!</div><div style="color:#78350f;font-size:11.5px;margin-top:3px;line-height:1.7;"><div>📦 Jumlah RKA: <b style="font-family:var(--mono);">${rkaItem.jumlah} ${rkaItem.satuan||''}</b></div><div>✅ Sudah diusulkan: <b style="font-family:var(--mono);">${sudahTersimpan+sudahDiModal} ${rkaItem.satuan||''}</b></div><div style="border-top:1px dashed #fcd34d;margin-top:4px;padding-top:4px;">📌 Sisa: <b style="font-family:var(--mono);font-size:13px;color:#b45309;">${sisaEfektif} ${rkaItem.satuan||''}</b></div></div><button onclick="document.getElementById('mVol').value=${sisaEfektif};onVolInput();" style="margin-top:7px;padding:4px 12px;background:#f59e0b;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;font-family:var(--font);">✏️ Gunakan sisa (${sisaEfektif} ${rkaItem.satuan||''})</button></div></div>`;}
  else{validEl.innerHTML=`<div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:7px 13px;display:flex;align-items:center;gap:7px;">✅ <span style="color:#166534;font-size:11.5px;font-weight:600;">Volume valid — sisa setelah: <b style="font-family:var(--mono);">${sisaEfektif-vol} ${rkaItem.satuan||''}</b></span></div>`;}
}
function addItemToList(){
  const itemKey=document.getElementById('mItemSel').value;const vol=parseFloat(document.getElementById('mVol').value)||0;
  const harga=parseFloat(document.getElementById('mHarga').value)||0;const sat=document.getElementById('mSat').value.trim();const ket=document.getElementById('mKet').value.trim();
  const validEl=document.getElementById('miValidMsg');
  if(!itemKey){validEl.innerHTML='<span style="color:var(--red);font-weight:600;">⚠️ Pilih nama barang.</span>';document.getElementById('mItemSel').focus();return;}
  if(!vol||vol<=0){validEl.innerHTML='<span style="color:var(--red);font-weight:600;">⚠️ Isi volume.</span>';document.getElementById('mVol').focus();return;}
  const bln=parseInt(document.getElementById('mBulan').value)||getMonth();const rka=getRkaForUsulan(bln);const rkaItem=rka.find(r=>r.item_key===itemKey);
  if(!rkaItem){validEl.innerHTML='<span style="color:var(--red);">⚠️ Item RKA tidak ditemukan.</span>';return;}
  const sudahTersimpan=getSudahUsul(itemKey);const sudahDiModal=modalItems.filter(it=>it.item_key===itemKey).reduce((s,it)=>s+(it.volume||0),0);const sisaEfektif=rkaItem.jumlah-sudahTersimpan-sudahDiModal;
  if(sisaEfektif<=0){validEl.innerHTML=`<div style="background:#fff5f5;border:1.5px solid #fca5a5;border-radius:8px;padding:9px 13px;">🚫 <b>Stok habis!</b></div>`;return;}
  if(vol>sisaEfektif){validEl.innerHTML=`<div style="background:#fffbeb;border:1.5px solid #fcd34d;border-radius:8px;padding:9px 13px;">⚠️ Melebihi sisa (${sisaEfektif} ${rkaItem.satuan||''}). <button onclick="document.getElementById('mVol').value=${sisaEfektif};onVolInput();" style="margin-left:8px;padding:3px 10px;background:#f59e0b;color:#fff;border:none;border-radius:5px;font-size:11px;font-weight:700;cursor:pointer;">Gunakan sisa</button></div>`;document.getElementById('mVol').value=sisaEfektif;onVolInput();document.getElementById('mVol').select();return;}
  modalItems.push({id:uid(),kode_sub:rkaItem.kode_sub||rkaItem.nama_sub,nama_sub:rkaItem.nama_sub,item_key:itemKey,uraian:rkaItem.uraian,volume:vol,satuan:sat,harga,total:vol*harga,keterangan:ket});
  renderModalItems();resetItemForm();
}
function showSenderErr(msg){
  let el=document.getElementById('mSenderErr');
  if(!el){el=document.createElement('div');el.id='mSenderErr';el.style.cssText='font-size:11px;font-weight:600;color:var(--red);margin-top:8px;padding:7px 11px;background:#fff5f5;border:1.5px solid #fca5a5;border-radius:7px;display:flex;align-items:flex-start;gap:6px;';document.querySelector('.sender-block').appendChild(el);}
  el.innerHTML='⚠️ '+msg;el.style.display='flex';document.querySelector('#modalUsulan .mb').scrollTop=0;
}
function clearSenderErr(){const el=document.getElementById('mSenderErr');if(el)el.style.display='none';}
function submitPengajuan(){
  const bulan=parseInt(document.getElementById('mBulan').value);const nomor_surat=document.getElementById('mNomorSurat').value.trim();
  const pihak=document.getElementById('mPihak').value.trim();const seksi=document.getElementById('mSeksi').value.trim();const nip=document.getElementById('mNIP').value.trim();const jabatan=document.getElementById('mJabatan').value.trim();
  clearSenderErr();
  if(!pihak){showSenderErr('Isi Pihak yang Mengajukan.');document.getElementById('mPihak').focus();return;}
  if(!seksi){showSenderErr('Pilih Dari Seksi.');document.getElementById('mSeksi').focus();return;}
  if(!nomor_surat){showSenderErr('Isi Nomor Surat.');document.getElementById('mNomorSurat').focus();return;}
  if(!modalItems.length){const ve=document.getElementById('miValidMsg');if(ve)ve.innerHTML='<span style="color:var(--red);font-weight:600;">⚠️ Tambahkan minimal 1 barang.</span>';return;}
  const rka=getRkaForUsulan(bulan);const violations=[];const groupedVol={};
  modalItems.forEach(it=>{if(!groupedVol[it.item_key])groupedVol[it.item_key]=0;groupedVol[it.item_key]+=it.volume||0;});
  Object.entries(groupedVol).forEach(([itemKey,totalVol])=>{
    const rkaItem=rka.find(r=>r.item_key===itemKey);if(!rkaItem)return;const sudah=getSudahUsul(itemKey);const sisa=rkaItem.jumlah-sudah;
    if(totalVol>sisa)violations.push(`• <b>${rkaItem.uraian}</b>: diajukan <b>${totalVol}</b>, sisa hanya <b>${sisa<=0?'0 (habis)':sisa}</b>`);
  });
  if(violations.length>0){showSenderErr(`Pengajuan GAGAL — item melebihi sisa stok:<br><div style="margin-top:6px;line-height:1.8;">${violations.join('<br>')}</div>`);return;}
  if(!state.pengajuan)state.pengajuan=[];
  state.pengajuan.push({id:uid(),bulan,tgl:new Date().toISOString(),pihak,seksi,nip,jabatan,nomor_surat,items:JSON.parse(JSON.stringify(modalItems))});
  save();renderUsulanPage();renderRkaTable();renderDashboard();
  modalItems=[];itemListExpanded=true;
  ['mNomorSurat','mPihak','mNIP','mJabatan'].forEach(id=>document.getElementById(id).value='');document.getElementById('mSeksi').value='';
  document.getElementById('mSubKeg').value='';document.getElementById('mItemSel').innerHTML='<option value="">— Pilih Sub dulu —</option>';
  document.getElementById('mItemInfo').style.display='none';['mVol','mSat','mHarga','mTotal','mKet'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('mHargaAutoBadge').style.display='none';
  const ve=document.getElementById('miValidMsg');if(ve)ve.innerHTML='';document.getElementById('mItemsTotal').style.display='none';document.getElementById('mItemSummary').style.display='none';document.getElementById('mItemToggleIcon').textContent='▼';
  const badge=document.getElementById('mItemCountBadge');if(badge){badge.style.background='var(--surface2)';badge.style.borderColor='var(--border)';badge.style.color='var(--muted)';}
  document.getElementById('mItemCountText').textContent='0 item';const listEl=document.getElementById('mItemList');listEl.style.display='';listEl.innerHTML='';
  const emptyEl=document.getElementById('mEmptyItems');listEl.appendChild(emptyEl);emptyEl.style.display='';clearSenderErr();
  let toast=document.getElementById('mToastSukses');
  if(!toast){toast=document.createElement('div');toast.id='mToastSukses';toast.style.cssText='background:#d1fae5;border:1.5px solid #6ee7b7;color:#065f46;font-size:12px;font-weight:700;padding:9px 14px;border-radius:8px;margin-bottom:12px;display:flex;align-items:center;gap:8px;';document.querySelector('#modalUsulan .mb').prepend(toast);}
  toast.innerHTML='✅ Pengajuan berhasil disimpan!';toast.style.display='flex';clearTimeout(window._toastTimer);window._toastTimer=setTimeout(()=>{if(toast)toast.style.display='none';},4500);
  document.querySelector('#modalUsulan .mb').scrollTop=0;setTimeout(()=>document.getElementById('mPihak').focus(),80);
}
function delPengajuan(id){state.pengajuan=(state.pengajuan||[]).filter(p=>p.id!==id);save();renderUsulanPage();renderRkaTable();renderDashboard();}

// ─────────────────────────────────────────
//  EXPORT USULAN PERMINTAAN
// ─────────────────────────────────────────
function openExportWordSingle(pengajuanId){
  const p=(state.pengajuan||[]).find(x=>x.id===pengajuanId);if(!p){alert('Pengajuan tidak ditemukan.');return;}
  document.getElementById('wBulan').value=p.bulan;
  document.getElementById('wNomorND').value=p.nomor_surat||'';
  document.getElementById('wDari').value=p.pihak||'';
  document.getElementById('wNIPDari').value=p.nip||'';
  document.getElementById('wJabatanDari').value=p.jabatan||'';
  document.getElementById('wSeksiDari').value=p.seksi||'';
  document.getElementById('wSinglePengajuanId').value=pengajuanId;
  document.getElementById('wFilterSub').value='';
  // Reset field mengetahui
  document.getElementById('wNamaMengetahui').value='';
  document.getElementById('wNIPMengetahui').value='';
  document.getElementById('wJabatanMengetahui').value='';
  openModal('modalExportWord');
  setTimeout(()=>document.getElementById('wNamaMengetahui').focus(),120);
}

async function doExportWord(){
  const bln=parseInt(document.getElementById('wBulan').value);
  const nomorND=document.getElementById('wNomorND').value.trim()||'—';
  // Kepada Yth — dari hidden fields (hardcoded)
  const kepada=document.getElementById('wKepada').value.trim();
  const nipKepada=document.getElementById('wNIPKepada').value.trim();
  const jabKepada=document.getElementById('wJabatanKepada').value.trim();
  // Data pengajuan (dari)
  const dari=document.getElementById('wDari').value.trim();
  const nipDari=document.getElementById('wNIPDari').value.trim();
  const jabDari=document.getElementById('wJabatanDari').value.trim();
  const seksiDari=document.getElementById('wSeksiDari').value.trim();
  // Pihak yang mengetahui — dari input user
  const namaMengetahui=document.getElementById('wNamaMengetahui').value.trim();
  const nipMengetahui=document.getElementById('wNIPMengetahui').value.trim();
  const jabMengetahui=document.getElementById('wJabatanMengetahui').value.trim();

  const singleId=document.getElementById('wSinglePengajuanId').value;
  let bP=singleId?(state.pengajuan||[]).filter(p=>p.id===singleId):getPengajuanBulan(bln);
  const allItems=bP.flatMap(p=>p.items||[]);
  if(!allItems.length){alert('Tidak ada data untuk di-export.');return;}

  const tglObj=new Date();const tglNum=tglObj.getDate();const tahun=tglObj.getFullYear();const bulanNama=BLN[bln];
  let grandTotal=0;allItems.forEach(it=>{grandTotal+=(it.total||(it.volume*(it.harga||0))||0);});

  let noRow=1;
  const itemRows=allItems.map(it=>{
    const harga=it.harga||0;const subtotal=it.total||(it.volume*harga)||0;
    return`<tr><td style="border:1px solid #000;padding:3px 4px;text-align:center;font-size:10pt;">${noRow++}</td><td style="border:1px solid #000;padding:3px 4px;font-size:10pt;">${esc(it.nama_sub||'—')}</td><td style="border:1px solid #000;padding:3px 4px;font-size:10pt;">${esc(it.uraian||'—')}</td><td style="border:1px solid #000;padding:3px 4px;text-align:center;font-size:10pt;">${it.volume||0}</td><td style="border:1px solid #000;padding:3px 4px;text-align:center;font-size:10pt;">${esc(it.satuan||'—')}</td><td style="border:1px solid #000;padding:3px 4px;text-align:right;font-size:10pt;">${harga>0?formatRp(harga):'—'}</td><td style="border:1px solid #000;padding:3px 4px;text-align:right;font-size:10pt;font-weight:bold;">${subtotal>0?formatRp(subtotal):'—'}</td></tr>`;
  }).join('');

  const logoBase64=await new Promise((resolve)=>{
    const img=new Image();
    img.onload=function(){const canvas=document.createElement('canvas');const maxSize=80;let w=img.naturalWidth,h=img.naturalHeight;if(w>h){h=Math.round(h*(maxSize/w));w=maxSize;}else{w=Math.round(w*(maxSize/h));h=maxSize;}canvas.width=w;canvas.height=h;const ctx=canvas.getContext('2d');ctx.fillStyle='#ffffff';ctx.fillRect(0,0,w,h);ctx.drawImage(img,0,0,w,h);resolve(canvas.toDataURL('image/png'));};
    img.onerror=()=>resolve('');img.src='LOGO PEMPROV.png';
  });
  const logoTag=logoBase64?`<img src="${logoBase64}" alt="Logo" style="width:65pt;height:65pt;object-fit:contain;display:block;margin:0 auto;">`:`<span style="font-size:9pt;color:#aaa;">LOGO</span>`;

  const html=`<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head><meta charset='utf-8'><title>Usulan Permintaan ${bulanNama} ${tahun}</title>
<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->
<style>
@page{margin:2cm 2.5cm 2cm 2.5cm;size:A4;}
body{font-family:'Times New Roman',Times,serif;font-size:11pt;color:#000;line-height:1.3;}
.kop-table{width:100%;border-collapse:collapse;margin-bottom:2pt;}
.kop-text{vertical-align:middle;padding-left:6pt;}
.kop-instansi{font-size:13pt;font-weight:bold;text-align:center;}
.kop-upt{font-size:12pt;font-weight:bold;text-align:center;}
.kop-alamat{font-size:9pt;text-align:center;}
.garis-kop{border-top:4px solid #000;margin:2pt 0 6pt 0;}
.info-table{width:100%;border-collapse:collapse;margin-bottom:6pt;}
.info-table td{padding:0.8pt 2pt;font-size:11pt;vertical-align:top;line-height:1.35;}
.info-table .lbl{width:105pt;}
.info-table .sep{width:10pt;text-align:center;}
.garis-thin{border-top:1px solid #000;margin:4pt 0;}
.isi-nd{font-size:11pt;text-align:justify;margin-bottom:5pt;line-height:1.4;}
.tbl-barang{width:100%;border-collapse:collapse;margin-bottom:6pt;font-size:10pt;table-layout:fixed;}
.tbl-barang th{border:1px solid #000;padding:2px 3px;background:#f0f0f0;text-align:center;font-weight:bold;word-break:break-word;line-height:1.2;font-size:10pt;}
.tbl-barang td{word-break:break-word;line-height:1.2;}
.tbl-barang .total-row td{border:1px solid #000;padding:4px 5px;font-weight:bold;background:#f9f9f9;}
.ttd-table{width:100%;border-collapse:collapse;margin-top:10pt;}
.ttd-table td{text-align:center;padding:2pt 4pt;vertical-align:top;font-size:11pt;}
.ttd-nama{font-weight:bold;text-decoration:underline;}
.ttd-nip{font-size:10pt;}
</style></head><body>
<table class="kop-table"><tr>
  <td style="width:75pt;text-align:center;vertical-align:middle;padding-right:8pt;">${logoTag}</td>
  <td class="kop-text">
    <div class="kop-instansi">DINAS SOSIAL PROVINSI JAWA TIMUR</div>
    <div class="kop-upt">UPT REHABILITASI SOSIAL BINA GRAHITA TUBAN</div>
    <div class="kop-alamat">Jalan Teuku Umar No. 16, Latsari, Kecamatan Tuban, Kabupaten Tuban, Jawa Timur | (0356) 322126</div>
  </td>
</tr></table>
<div class="garis-kop"></div>
<table style="width:100%;border-collapse:collapse;margin:8pt 0 2pt 0;">
  <tr><td style="text-align:center;font-size:13pt;font-weight:bold;text-decoration:underline;">USULAN PERMINTAAN</td></tr>
</table>
<table style="width:100%;border-collapse:collapse;margin-bottom:8pt;">
  <tr><td style="text-align:center;font-size:11pt;">Nomor: ${esc(nomorND)}</td></tr>
</table>
<table class="info-table">
  <tr><td class="lbl">Kepada Yth.</td><td class="sep">:</td><td>${esc(kepada||'—')}</td></tr>
  <tr><td class="lbl">NIP</td><td class="sep">:</td><td>${esc(nipKepada||'—')}</td></tr>
  <tr><td class="lbl">Jabatan</td><td class="sep">:</td><td>${esc(jabKepada||'—')}</td></tr>
  <tr><td colspan="3" style="padding:2pt 0;"></td></tr>
  <tr><td class="lbl">Dari</td><td class="sep">:</td><td>${esc(dari||'—')}</td></tr>
  <tr><td class="lbl">NIP</td><td class="sep">:</td><td>${esc(nipDari||'—')}</td></tr>
  <tr><td class="lbl">Jabatan</td><td class="sep">:</td><td>${esc(jabDari||'—')}</td></tr>
  <tr><td class="lbl">Nomor</td><td class="sep">:</td><td>${esc(nomorND)}</td></tr>
  <tr><td class="lbl">Perihal</td><td class="sep">:</td><td><strong>Kebutuhan ${esc(seksiDari||'—')}</strong></td></tr>
</table>
<div class="garis-thin"></div>
<p class="isi-nd">Dengan hormat,</p>
<p class="isi-nd">Sehubungan dengan pelaksanaan operasional ${esc(seksiDari)} di UPT Rehabilitasi Sosial Bina Grahita Tuban, bersama ini kami mengajukan permohonan pengadaan barang bulan ${bulanNama} ${tahun} sebagaimana tercantum dalam tabel berikut:</p>
<table class="tbl-barang">
  <thead><tr>
    <th style="width:5%;">No</th>
    <th style="width:22%;">Sub Kegiatan</th>
    <th style="width:31%;">Uraian / Nama Barang</th>
    <th style="width:6%;">Jml</th>
    <th style="width:6%;">Sat</th>
    <th style="width:15%;">Harga Satuan</th>
    <th style="width:15%;">Jumlah Harga</th>
  </tr></thead>
  <tbody>
    ${itemRows}
    <tr class="total-row">
      <td colspan="6" style="border:1px solid #000;padding:4px 5px;text-align:right;font-weight:bold;">Jumlah Total</td>
      <td style="border:1px solid #000;padding:4px 5px;text-align:right;font-weight:bold;">${grandTotal>0?formatRp(grandTotal):'—'}</td>
    </tr>
  </tbody>
</table>
<p class="isi-nd">Demikian permohonan ini kami sampaikan, atas perhatian dan persetujuan Bapak/Ibu kami ucapkan terima kasih.</p>

<!-- TTD: hanya 2 kolom — Mengetahui (kiri) dan Pengajuan (kanan) -->
<table class="ttd-table"><tr>
  <td style="width:45%;text-align:center;vertical-align:top;font-size:11pt;padding:2pt 4pt;">
    <div>Mengetahui,</div>
    <div>${esc(jabMengetahui||'—')}</div>
    <br><br><br>
    <div class="ttd-nama">${esc(namaMengetahui||'—')}</div>
    <div class="ttd-nip">NIP. ${esc(nipMengetahui||'—')}</div>
  </td>
  <td style="width:10%;"></td>
  <td style="width:45%;text-align:center;vertical-align:top;font-size:11pt;padding:2pt 4pt;">
    <div>Tuban, ${tglNum} ${bulanNama} ${tahun}</div>
    <div>${esc(jabDari||'—')}</div>
    <br><br><br>
    <div class="ttd-nama">${esc(dari||'—')}</div>
    <div class="ttd-nip">NIP. ${esc(nipDari||'—')}</div>
  </td>
</tr></table>
</body></html>`;

  const blob=new Blob(['\ufeff'+html],{type:'application/msword;charset=utf-8'});
  const url=URL.createObjectURL(blob);const a=document.createElement('a');
  const pSuffix=singleId&&bP[0]?'_'+(bP[0].pihak||'').replace(/\s+/g,'_').replace(/[^a-zA-Z0-9_]/g,'').slice(0,20):'';
  a.href=url;a.download=`Usulan_Permintaan_${bulanNama}_${tahun}${pSuffix}.doc`;
  document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(url);
  closeModal('modalExportWord');document.getElementById('wSinglePengajuanId').value='';
}

// ─────────────────────────────────────────
//  RIWAYAT
// ─────────────────────────────────────────
function renderRiwayat(){
  const bulanAda=[...new Set((state.pengajuan||[]).map(p=>p.bulan))].sort((a,b)=>a-b);const tabsEl=document.getElementById('riwayatMonthTabs');
  if(!bulanAda.length){tabsEl.innerHTML='<span style="font-size:12px;color:var(--muted);">Belum ada data pengajuan.</span>';document.getElementById('riwayatContent').innerHTML='<div class="empty"><div class="ei">📭</div><p>Belum ada data.</p></div>';return;}
  tabsEl.innerHTML=bulanAda.map(b=>`<button class="mt-btn${riwayatSelectedMonth==b?' active':''}" onclick="selectRiwayatMonth(${b})">${BLN_S[b]} 2026</button>`).join('');
  if(!riwayatSelectedMonth&&bulanAda.length)riwayatSelectedMonth=bulanAda[bulanAda.length-1];renderRiwayatDetail(riwayatSelectedMonth);
}
function selectRiwayatMonth(b){riwayatSelectedMonth=b;renderRiwayat();}
function renderRiwayatDetail(bln){
  if(!bln){document.getElementById('riwayatContent').innerHTML='<div class="empty"><div class="ei">📅</div><p>Pilih bulan.</p></div>';return;}
  const bP=getPengajuanBulan(bln);const bItems=bP.flatMap(p=>p.items||[]);const grandTotal=bItems.reduce((s,it)=>s+(it.total||0),0);
  document.getElementById('riwayatContent').innerHTML=`<div class="card"><div class="card-head"><h3>📅 Pengajuan Bulan ${BLN[bln]} 2026</h3><div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;"><span class="badge b-info">${bP.length} pengajuan · ${bItems.length} item</span>${grandTotal>0?`<span class="badge b-green">💰 ${formatRp(grandTotal)}</span>`:''}<button class="btn btn-ghost btn-xs" onclick="exportBulanExcel(${bln})">⬇️ Rekapitulasi</button></div></div>
    <div class="filter-bar" style="margin-bottom:0;"><div class="sw"><span class="si">🔍</span><input type="text" class="si-inp" id="filterRiwayat" placeholder="Cari…" oninput="filterRiwayatTable(${bln})"></div><select class="sel" id="filterRiwayatSub" onchange="filterRiwayatTable(${bln})"><option value="">Semua Sub</option>${getRkaSubList(getRkaForBulan('all')).map(s=>`<option value="${s.kode}">${s.nama}</option>`).join('')}</select></div>
    <div class="tw" style="margin-top:8px;"><table><thead><tr><th style="width:36px;">No</th><th>Sub Kegiatan</th><th>Uraian Barang</th><th style="text-align:right;width:50px;">Jumlah</th><th style="width:55px;">Sat</th><th class="th-right" style="width:115px;">Harga Satuan</th><th class="th-right" style="width:115px;">Jumlah Harga</th><th>Keterangan</th><th style="width:130px;">Aksi</th></tr></thead><tbody id="riwayatTbody"></tbody></table></div>
    <div class="pagi"><span class="pagi-info" id="riwayatInfo">—</span></div></div>`;
  renderRiwayatRows(bln,bP);
}
function filterRiwayatTable(bln){
  const q=(document.getElementById('filterRiwayat')||{}).value||'';const sub=(document.getElementById('filterRiwayatSub')||{}).value||'';
  let bP=getPengajuanBulan(bln);
  if(sub||q){bP=bP.map(p=>{let items=p.items||[];if(sub)items=items.filter(it=>(it.kode_sub||it.nama_sub)===sub);if(q){const l=q.toLowerCase();const matchH=(p.pihak||'').toLowerCase().includes(l)||(p.seksi||'').toLowerCase().includes(l)||(p.nip||'').toLowerCase().includes(l)||(p.jabatan||'').toLowerCase().includes(l)||(p.nomor_surat||'').toLowerCase().includes(l);if(!matchH)items=items.filter(it=>(it.uraian||'').toLowerCase().includes(l));}return{...p,items};}).filter(p=>p.items.length>0);}
  renderRiwayatRows(bln,bP);
}
function renderRiwayatRows(bln,bP){
  const tbody=document.getElementById('riwayatTbody');const infoEl=document.getElementById('riwayatInfo');if(!tbody)return;
  if(!bP||!bP.length){tbody.innerHTML=`<tr><td colspan="${NCOL}"><div class="empty" style="padding:20px;"><p>Tidak ada data.</p></div></td></tr>`;if(infoEl)infoEl.textContent='—';return;}
  let rows=[];let globalNo=1;let grandTotal=0;
  bP.forEach((p,pi)=>{const items=p.items||[];const subTotal=items.reduce((s,it)=>s+(it.total||0),0);grandTotal+=subTotal;rows.push(mkHeaderRow(p,pi,items,subTotal,'delPengajuanRiwayat'));items.forEach(it=>rows.push(mkItemRow(it,globalNo++)));if(items.length>1)rows.push(`<tr class="group-row-total"><td colspan="6" style="padding:5px 14px;font-size:10.5px;font-weight:700;color:#166534;text-align:right;">Subtotal #${pi+1} (${items.length} item)</td><td class="mono" style="font-weight:800;color:#059669;text-align:right;padding:5px 14px;">${formatRp(subTotal)}</td><td colspan="2"></td></tr>`);});
  if(grandTotal>0)rows.push(`<tr style="background:#1a2d6b;color:#fff;"><td colspan="6" style="padding:9px 14px;font-size:11px;font-weight:800;text-align:right;">GRAND TOTAL ${BLN[bln].toUpperCase()} 2026</td><td style="padding:9px 14px;font-family:var(--mono);font-weight:800;font-size:13px;text-align:right;color:#fbbf24;">${formatRp(grandTotal)}</td><td colspan="2"></td></tr>`);
  tbody.innerHTML=rows.join('');if(infoEl)infoEl.textContent=`${bP.length} pengajuan · ${bP.flatMap(p=>p.items||[]).length} item${grandTotal>0?' · '+formatRp(grandTotal):''}`;
}
function delPengajuanRiwayat(id){state.pengajuan=(state.pengajuan||[]).filter(p=>p.id!==id);save();renderDashboard();renderRiwayat();}

// ─────────────────────────────────────────
//  LAPORAN
// ─────────────────────────────────────────
function renderLaporan(){
  const allP=state.pengajuan||[];const allItems=allP.flatMap(p=>p.items||[]);const rkaAll=getRkaForBulan('all');
  document.getElementById('lapStats').innerHTML=`<div class="sc sc-blue"><span class="ico">📋</span><div class="lbl">Total Pengajuan TA</div><div class="val">${allP.length}</div></div><div class="sc sc-green"><span class="ico">📦</span><div class="lbl">Total Item Barang</div><div class="val">${allItems.length}</div></div><div class="sc sc-teal"><span class="ico">💰</span><div class="lbl">Total Nilai</div><div class="val" style="font-size:12px;">${formatRp(allItems.reduce((s,it)=>s+(it.total||0),0))}</div></div>`;
  const subs=getRkaSubList(rkaAll);const subCounts=subs.map(s=>allItems.filter(it=>(it.kode_sub||it.nama_sub)===s.kode).length);
  const ctx=document.getElementById('lapBar');if(lapBarInst)lapBarInst.destroy();
  lapBarInst=new Chart(ctx,{type:'bar',data:{labels:subs.map(s=>s.nama.split(' ').slice(0,2).join(' ')),datasets:[{data:subCounts,backgroundColor:['#2563eb','#059669','#f59e0b','#0d9488','#7c3aed','#db2777','#ea580c'],borderRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{ticks:{stepSize:1}}}}});
  const itemTotals={};allItems.forEach(it=>{if(!itemTotals[it.item_key])itemTotals[it.item_key]=0;itemTotals[it.item_key]+=it.volume||0;});
  const rows=rkaAll.filter(r=>itemTotals[r.item_key]>0);
  if(!rows.length){document.getElementById('lapTbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>Belum ada pengajuan.</p></div></td></tr>';return;}
  document.getElementById('lapTbody').innerHTML=rows.map((r,i)=>{const usul=itemTotals[r.item_key]||0;const sisa=r.jumlah-usul;const pct=r.jumlah>0?(usul/r.jumlah*100).toFixed(0):0;const blnLabel=r._bulan>0?BLN_S[r._bulan]:'—';return`<tr><td class="muted">${i+1}</td><td><span class="tag-month">${blnLabel}</span></td><td style="font-size:10.5px;">${(r.nama_sub||'').split(' ').slice(0,3).join(' ')}</td><td style="font-weight:600;">${r.uraian||'—'}</td><td class="mono" style="text-align:right;">${r.jumlah}</td><td class="muted">${r.satuan||'—'}</td><td class="mono" style="color:var(--orange);text-align:right;">${usul}</td><td class="mono" style="color:${sisa<=0?'var(--red)':'var(--muted)'};text-align:right;">${sisa}</td><td class="mono">${pct}%</td></tr>`;}).join('');
}

// ─────────────────────────────────────────
//  EXPORT EXCEL
// ─────────────────────────────────────────
// ─── HELPER: set column widths ───
function styleHeaderRow(row){
  row.eachCell(cell=>{
    cell.alignment={horizontal:'center',vertical:'middle',wrapText:true};
    cell.font={bold:true,color:{argb:'FFFFFFFF'}};
    cell.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF1A2D6B'}};
    cell.border={
      top:{style:'thin'},bottom:{style:'thin'},
      left:{style:'thin'},right:{style:'thin'}
    };
  });
  row.height=22;
}
function styleDataRow(row,numFmtCols=[]){
  row.eachCell({includeEmpty:true},cell=>{
    cell.alignment={horizontal:'center',vertical:'middle',wrapText:true};
    cell.border={
      top:{style:'thin'},bottom:{style:'thin'},
      left:{style:'thin'},right:{style:'thin'}
    };
  });
  numFmtCols.forEach(c=>{ const cell=row.getCell(c); if(typeof cell.value==='number') cell.numFmt='#,##0'; });
}
async function downloadXlsx(wb,filename){
  const buf=await wb.xlsx.writeBuffer();
  const blob=new Blob([buf],{type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
  const url=URL.createObjectURL(blob);
  const a=document.createElement('a');a.href=url;a.download=filename;
  document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(url);
}

function setColWidths(ws,widths){ws['!cols']=widths.map(w=>({wch:w}));}
async function exportRkaExcel(){
  const filterBulan=document.getElementById('rkaBulanFilter').value;
  const bulanNum=filterBulan==='all'?null:parseInt(filterBulan);
  const list=getRkaForBulan(bulanNum===null?'all':bulanNum);

  const wb=new ExcelJS.Workbook();
  const ws=wb.addWorksheet('Data RKA');

  const headers=['No','Bulan','Sub Kegiatan','Uraian','Jumlah','Satuan','Harga Satuan'];
  const widths  =[6,    14,    40,            50,      10,      14,      18];

  ws.columns=headers.map((h,i)=>({header:h,key:String(i),width:widths[i]}));
  styleHeaderRow(ws.getRow(1));

  list.forEach((r,i)=>{
    const row=ws.addRow([
      i+1,
      r._bulan>0?BLN[r._bulan]:'—',
      r.nama_sub||'—',
      r.uraian||'—',
      r.jumlah||0,
      r.satuan||'—',
      r.harga_satuan||0
    ]);
    styleDataRow(row,[7]); // kolom 7 = Harga Satuan
  });

  await downloadXlsx(wb,'Data_DPA_SIPUA_2026.xlsx');
}


async function exportBulanExcel(bln){
  const bP=getPengajuanBulan(bln);if(!bP.length){alert('Tidak ada data.');return;}
  const wb=new ExcelJS.Workbook();
  const ws=wb.addWorksheet(BLN[bln]);

  const headers=['No','Tanggal','Pihak Yang Mengajukan','Seksi','NIP','Jabatan','Nomor Surat','Sub Kegiatan','Uraian Barang','Quantity','Satuan','Harga Satuan','Jumlah Harga','Keterangan'];
  const colWidths=[6,14,28,26,22,30,30,40,45,10,10,18,18,35];

  ws.columns=headers.map((h,i)=>({header:h,key:i,width:colWidths[i]}));

  // Style header row
  ws.getRow(1).eachCell(cell=>{
    cell.alignment={horizontal:'center',vertical:'middle',wrapText:true};
    cell.font={bold:true};
    cell.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF1A2D6B'}};
    cell.font={bold:true,color:{argb:'FFFFFFFF'}};
  });
  ws.getRow(1).height=20;

  let no=1;
  bP.forEach(p=>{
    const tgl=new Date(p.tgl).toLocaleDateString('id-ID');
    (p.items||[]).forEach(it=>{
      const row=ws.addRow([no++,tgl,p.pihak,p.seksi||'',p.nip||'',p.jabatan||'',p.nomor_surat,it.nama_sub,it.uraian,it.volume,it.satuan,it.harga||0,it.total||0,it.keterangan||'']);
      row.eachCell(cell=>{
        cell.alignment={horizontal:'center',vertical:'middle',wrapText:true};
      });
      // Format angka
      row.getCell(12).numFmt='#,##0';
      row.getCell(13).numFmt='#,##0';
    });
  });

  // Row TOTAL
  const grandTotal=bP.flatMap(p=>p.items||[]).reduce((s,it)=>s+(it.total||0),0);
  if(grandTotal>0){
    const totalRow=ws.addRow(['','','','','','','','','','','','TOTAL',grandTotal,'']);
    totalRow.getCell(12).alignment={horizontal:'center'};
    totalRow.getCell(13).numFmt='#,##0';
    totalRow.getCell(13).font={bold:true};
    totalRow.getCell(13).alignment={horizontal:'center'};
  }

  const buf=await wb.xlsx.writeBuffer();
  const blob=new Blob([buf],{type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
  const url=URL.createObjectURL(blob);
  const a=document.createElement('a');a.href=url;a.download=`Pengajuan_${BLN[bln]}_2026.xlsx`;
  document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(url);
}


async function exportAllExcel(){
  const bulanAda=[...new Set((state.pengajuan||[]).map(p=>p.bulan))].sort((a,b)=>a-b);
  if(!bulanAda.length){alert('Belum ada data.');return;}

  const wb=new ExcelJS.Workbook();
  const headers=['No','Tanggal','Pihak Yang Mengajukan','Seksi','NIP','Jabatan','Nomor Surat','Sub Kegiatan','Uraian Barang','Quantity','Satuan','Harga Satuan','Jumlah Harga','Keterangan'];
  const widths  =[6,   14,      28,             26,     22,   30,       30,            40,             45,             10,       10,      18,             18,             35];

  bulanAda.forEach(bln=>{
    const bP=getPengajuanBulan(bln);
    const ws=wb.addWorksheet(BLN_S[bln]);
    ws.columns=headers.map((h,i)=>({header:h,key:String(i),width:widths[i]}));
    styleHeaderRow(ws.getRow(1));

    let no=1;
    bP.forEach(p=>{
      const tgl=new Date(p.tgl).toLocaleDateString('id-ID');
      (p.items||[]).forEach(it=>{
        const row=ws.addRow([
          no++,tgl,p.pihak,p.seksi||'',p.nip||'',p.jabatan||'',
          p.nomor_surat,it.nama_sub,it.uraian,
          it.volume,it.satuan,it.harga||0,it.total||0,it.keterangan||''
        ]);
        styleDataRow(row,[12,13]);
      });
    });

    const grandTotal=bP.flatMap(p=>p.items||[]).reduce((s,it)=>s+(it.total||0),0);
    if(grandTotal>0){
      const totalRow=ws.addRow(['','','','','','','','','','','','TOTAL',grandTotal,'']);
      totalRow.eachCell({includeEmpty:true},cell=>{
        cell.alignment={horizontal:'center',vertical:'middle'};
        cell.border={top:{style:'thin'},bottom:{style:'double'},left:{style:'thin'},right:{style:'thin'}};
      });
      totalRow.getCell(12).font={bold:true};
      totalRow.getCell(13).font={bold:true};
      totalRow.getCell(13).numFmt='#,##0';
      totalRow.height=18;
    }
  });

  await downloadXlsx(wb,'Rekapitulasi_SIPUA_2026.xlsx');
}


async function exportLaporan(){
  const itemTotals={};
  (state.pengajuan||[]).flatMap(p=>p.items||[]).forEach(it=>{
    if(!itemTotals[it.item_key])itemTotals[it.item_key]=0;
    itemTotals[it.item_key]+=it.volume||0;
  });

  const rkaAll=getRkaForBulan('all');
  const wb=new ExcelJS.Workbook();
  const ws=wb.addWorksheet('Laporan');

  const headers=['No','Bulan RKA','Sub Kegiatan','Uraian','Jumlah RKA','Satuan','Total Diusulkan','Sisa','%'];
  const widths  =[6,   14,        40,             50,      12,          12,      16,               10,    10];

  ws.columns=headers.map((h,i)=>({header:h,key:String(i),width:widths[i]}));
  styleHeaderRow(ws.getRow(1));

  rkaAll.filter(r=>itemTotals[r.item_key]>0).forEach((r,i)=>{
    const usul=itemTotals[r.item_key]||0;
    const sisa=r.jumlah-usul;
    const pct=r.jumlah>0?((usul/r.jumlah)*100).toFixed(1)+'%':'0%';
    const row=ws.addRow([
      i+1,
      r._bulan>0?BLN[r._bulan]:'—',
      r.nama_sub||'—',
      r.uraian||'—',
      r.jumlah||0,
      r.satuan||'—',
      usul,
      sisa,
      pct
    ]);
    styleDataRow(row,[]);

    // Warna sisa merah jika habis
    const sisaCell=row.getCell(8);
    if(sisa<=0) sisaCell.font={color:{argb:'FFDC2626'},bold:true};
    else if(sisa<r.jumlah*0.2) sisaCell.font={color:{argb:'FFF59E0B'},bold:true};
  });

  await downloadXlsx(wb,'Laporan_Rekapitulasi_SIPUA_2026.xlsx');
}
// ─────────────────────────────────────────
//  EDIT PENGAJUAN
// ─────────────────────────────────────────
let editPengajuanId = null;
let editItems = [];

function openEditPengajuan(id) {
  const p = (state.pengajuan || []).find(x => x.id === id);
  if (!p) { alert('Pengajuan tidak ditemukan.'); return; }
  editPengajuanId = id;
  editItems = JSON.parse(JSON.stringify(p.items || []));

  // Isi identitas
  document.getElementById('eBulan').value = p.bulan || getMonth();
  document.getElementById('eNomorSurat').value = p.nomor_surat || '';
  document.getElementById('ePihak').value = p.pihak || '';
  document.getElementById('eSeksi').value = p.seksi || '';
  document.getElementById('eNIP').value = p.nip || '';
  document.getElementById('eJabatan').value = p.jabatan || '';

  // Populate sub kegiatan
  const bln = p.bulan || getMonth();
  const rka = getRkaForUsulan(bln);
  const subs = getRkaSubList(rka);
  document.getElementById('eSubKeg').innerHTML = '<option value="">— Pilih —</option>' + subs.map(s => `<option value="${s.kode}">${s.nama}</option>`).join('');
  document.getElementById('eItemSel').innerHTML = '<option value="">— Pilih Sub dulu —</option>';
  document.getElementById('eHargaAutoBadge').style.display = 'none';
  ['eVol','eSat','eHarga','eTotal','eKet'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });

  // Reset error
  const errEl = document.getElementById('eErrMsg');
  if (errEl) errEl.style.display = 'none';

  renderEditItems();
  openModal('modalEditPengajuan');
  setTimeout(() => document.getElementById('ePihak').focus(), 120);
}

function renderEditItems() {
  const listEl = document.getElementById('eItemList');
  const countBadge = document.getElementById('eItemCountBadge');
  const totalBar = document.getElementById('eItemsTotal');
  const totalVal = document.getElementById('eTotalNilai');

  countBadge.textContent = editItems.length + ' item';

  if (!editItems.length) {
    listEl.innerHTML = '<div style="text-align:center;padding:16px;color:var(--muted2);font-size:12px;border:1.5px dashed var(--border);border-radius:8px;">Belum ada barang. Tambahkan di bawah.</div>';
    if (totalBar) totalBar.style.display = 'none';
    return;
  }

  const grandTotal = editItems.reduce((s, it) => s + (it.total || 0), 0);
  if (totalBar) totalBar.style.display = 'flex';
  if (totalVal) totalVal.textContent = formatRp(grandTotal);

  listEl.innerHTML = editItems.map((it, i) => {
    const harga = it.harga || 0; const total = it.total || 0;
    return '<div style="background:var(--surface);border:1.5px solid var(--border);border-radius:9px;padding:10px 13px;display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:4px;">'
      + '<div style="flex:1;min-width:0;">'
      + '<div style="display:flex;align-items:center;gap:7px;margin-bottom:3px;flex-wrap:wrap;"><span style="background:var(--navy);color:#fff;font-size:9px;font-weight:800;border-radius:4px;padding:1px 7px;font-family:var(--mono);">#' + (i + 1) + '</span><span style="font-size:12.5px;font-weight:700;">' + esc(it.uraian) + '</span></div>'
      + '<div style="font-size:10px;font-weight:600;color:var(--accent);margin-bottom:4px;">📂 ' + esc(it.nama_sub) + '</div>'
      + '<div style="display:flex;align-items:center;gap:6px;font-size:11.5px;flex-wrap:wrap;">'
      + '<b class="mono">' + it.volume + ' ' + (it.satuan || 'unit') + '</b>'
      + '<span style="color:var(--muted2);">×</span>'
      + '<span class="mono">' + (harga > 0 ? formatRp(harga) : '—') + '</span>'
      + '<span style="color:var(--muted2);">=</span>'
      + '<b class="mono" style="color:var(--green);font-size:13px;">' + (total > 0 ? formatRp(total) : '—') + '</b>'
      + '</div>'
      + (it.keterangan ? '<div style="font-size:10px;color:var(--muted);font-style:italic;margin-top:4px;">📝 ' + esc(it.keterangan) + '</div>' : '')
      + '</div>'
      + '<button data-eidx="' + i + '" class="rm-edit-item" style="flex-shrink:0;background:none;border:none;cursor:pointer;color:var(--muted2);font-size:16px;padding:4px 6px;border-radius:5px;line-height:1;" title="Hapus item ini">✕</button>'
      + '</div>';
  }).join('');

  listEl.querySelectorAll('.rm-edit-item').forEach(btn => {
    btn.onclick = function () {
      editItems.splice(parseInt(this.getAttribute('data-eidx')), 1);
      renderEditItems();
    };
    btn.onmouseover = function () { this.style.color = 'var(--red)'; this.style.background = '#fee2e2'; };
    btn.onmouseout = function () { this.style.color = 'var(--muted2)'; this.style.background = 'none'; };
  });
}

function ePopulateItemSel() {
  const sub = document.getElementById('eSubKeg').value;
  const bln = parseInt(document.getElementById('eBulan').value) || getMonth();
  const sel = document.getElementById('eItemSel');
  sel.innerHTML = '<option value="">— Pilih nama barang —</option>';
  if (!sub) return;
  const rka = getRkaForUsulan(bln);
  // Hitung sudah usul KECUALI dari pengajuan yang sedang diedit
  rka.filter(r => (r.kode_sub || r.nama_sub) === sub).forEach(r => {
    const sudahLain = (state.pengajuan || []).filter(p => p.id !== editPengajuanId)
      .flatMap(p => p.items || []).filter(it => it.item_key === r.item_key)
      .reduce((s, it) => s + (it.volume || 0), 0);
    const sudahEdit = editItems.filter(it => it.item_key === r.item_key).reduce((s, it) => s + (it.volume || 0), 0);
    const sisa = r.jumlah - sudahLain - sudahEdit;
    const icon = sisa <= 0 ? '🔴' : sisa < r.jumlah * 0.2 ? '🟡' : '🟢';
    sel.innerHTML += `<option value="${r.item_key}">${icon} ${r.uraian} — sisa: ${sisa <= 0 ? 'Habis' : sisa + ' ' + r.satuan}</option>`;
  });
  document.getElementById('eHargaAutoBadge').style.display = 'none';
  ['eVol','eSat','eHarga','eTotal','eKet'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
}

function eOnItemSelect() {
  const v = document.getElementById('eItemSel').value;
  const autoBadge = document.getElementById('eHargaAutoBadge');
  if (!v) { autoBadge.style.display = 'none'; return; }
  const bln = parseInt(document.getElementById('eBulan').value) || getMonth();
  const rka = getRkaForUsulan(bln);
  const rkaItem = rka.find(r => r.item_key === v);
  if (!rkaItem) return;
  document.getElementById('eSat').value = rkaItem.satuan || '';
  if (rkaItem.harga_satuan && rkaItem.harga_satuan > 0) {
    document.getElementById('eHarga').value = rkaItem.harga_satuan;
    autoBadge.style.display = 'inline-flex';
  } else {
    document.getElementById('eHarga').value = '';
    autoBadge.style.display = 'none';
  }
  if (!document.getElementById('eVol').value) document.getElementById('eVol').value = 1;
  eOnVolInput();
  setTimeout(() => document.getElementById('eVol').select(), 60);
}

function eOnVolInput() {
  const vol = parseFloat(document.getElementById('eVol').value) || 0;
  const harga = parseFloat(document.getElementById('eHarga').value) || 0;
  document.getElementById('eTotal').value = (vol && harga) ? formatRp(vol * harga) : '';
}

function eAddItem() {
  const itemKey = document.getElementById('eItemSel').value;
  const vol = parseFloat(document.getElementById('eVol').value) || 0;
  const harga = parseFloat(document.getElementById('eHarga').value) || 0;
  const sat = document.getElementById('eSat').value.trim();
  const ket = document.getElementById('eKet').value.trim();

  const errEl = document.getElementById('eErrMsg');
  if (!itemKey) { errEl.innerHTML = '⚠️ Pilih nama barang.'; errEl.style.display = 'flex'; return; }
  if (!vol || vol <= 0) { errEl.innerHTML = '⚠️ Isi jumlah.'; errEl.style.display = 'flex'; return; }
  errEl.style.display = 'none';

  const bln = parseInt(document.getElementById('eBulan').value) || getMonth();
  const rka = getRkaForUsulan(bln);
  const rkaItem = rka.find(r => r.item_key === itemKey);
  if (!rkaItem) { errEl.innerHTML = '⚠️ Item DPA tidak ditemukan.'; errEl.style.display = 'flex'; return; }

  const sudahLain = (state.pengajuan || []).filter(p => p.id !== editPengajuanId)
    .flatMap(p => p.items || []).filter(it => it.item_key === itemKey)
    .reduce((s, it) => s + (it.volume || 0), 0);
  const sudahEdit = editItems.filter(it => it.item_key === itemKey).reduce((s, it) => s + (it.volume || 0), 0);
  const sisaEfektif = rkaItem.jumlah - sudahLain - sudahEdit;

  if (sisaEfektif <= 0) { errEl.innerHTML = '🚫 Stok item ini sudah habis.'; errEl.style.display = 'flex'; return; }
  if (vol > sisaEfektif) { errEl.innerHTML = `⚠️ Jumlah melebihi sisa stok (${sisaEfektif} ${rkaItem.satuan || ''}).`; errEl.style.display = 'flex'; document.getElementById('eVol').value = sisaEfektif; eOnVolInput(); return; }

  editItems.push({
    id: uid(), kode_sub: rkaItem.kode_sub || rkaItem.nama_sub, nama_sub: rkaItem.nama_sub,
    item_key: itemKey, uraian: rkaItem.uraian, volume: juml, satuan: sat, harga, total: juml * harga, keterangan: ket
  });
  renderEditItems();
  ['eItemSel','eVol','eSat','eHarga','eTotal','eKet'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
  document.getElementById('eHargaAutoBadge').style.display = 'none';
  document.getElementById('eSubKeg').value = '';
  document.getElementById('eItemSel').innerHTML = '<option value="">— Pilih Sub dulu —</option>';
}

function saveEditPengajuan() {
  const pihak = document.getElementById('ePihak').value.trim();
  const seksi = document.getElementById('eSeksi').value.trim();
  const nomor_surat = document.getElementById('eNomorSurat').value.trim();
  const bulan = parseInt(document.getElementById('eBulan').value);
  const nip = document.getElementById('eNIP').value.trim();
  const jabatan = document.getElementById('eJabatan').value.trim();

  const errEl = document.getElementById('eErrMsg');
  if (!pihak) { errEl.innerHTML = '⚠️ Isi Pihak yang Mengajukan.'; errEl.style.display = 'flex'; document.getElementById('ePihak').focus(); return; }
  if (!seksi) { errEl.innerHTML = '⚠️ Pilih Dari Seksi.'; errEl.style.display = 'flex'; document.getElementById('eSeksi').focus(); return; }
  if (!nomor_surat) { errEl.innerHTML = '⚠️ Isi Nomor Surat.'; errEl.style.display = 'flex'; document.getElementById('eNomorSurat').focus(); return; }
  if (!editItems.length) { errEl.innerHTML = '⚠️ Minimal 1 barang harus ada.'; errEl.style.display = 'flex'; return; }
  errEl.style.display = 'none';

  const idx = (state.pengajuan || []).findIndex(p => p.id === editPengajuanId);
  if (idx === -1) { alert('Pengajuan tidak ditemukan.'); return; }

  state.pengajuan[idx] = {
    ...state.pengajuan[idx],
    bulan, nomor_surat, pihak, seksi, nip, jabatan,
    items: JSON.parse(JSON.stringify(editItems))
  };
  save();
  closeModal('modalEditPengajuan');
  renderUsulanPage();
  renderRkaTable();
  renderDashboard();
  if (riwayatSelectedMonth) renderRiwayat();
}
// ─────────────────────────────────────────
//  INIT
// ─────────────────────────────────────────
load();
updateSubSelects();
updateBadgeRka();
if(getRkaBulanList().length===0&&IS_ADMIN){goPage('import-rka');}else{goPage('dashboard');}
</script>
</body>
</html>