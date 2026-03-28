<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Input Usulan — SIPUA</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg:#eef2f7; --surface:#fff; --surface2:#f5f7fb; --border:#dde3ee;
  --navy:#0d1b3e; --accent:#2563eb; --accent2:#1d4ed8;
  --success:#059669; --warning:#d97706; --danger:#dc2626;
  --text:#111827; --muted:#6b7280;
  --font:'Plus Jakarta Sans',sans-serif; --mono:'JetBrains Mono',monospace;
  --radius:12px; --shadow:0 1px 3px rgba(0,0,0,.07),0 4px 16px rgba(0,0,0,.05);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:var(--font);min-height:100vh;}

/* ── SIDEBAR ── */
.sidebar{position:fixed;left:0;top:0;bottom:0;width:252px;background:var(--navy);display:flex;flex-direction:column;z-index:200;box-shadow:4px 0 24px rgba(0,0,0,.18);}
.sb-logo{padding:26px 22px 22px;border-bottom:1px solid rgba(255,255,255,.08);}
.sb-logo .mark{width:42px;height:42px;background:linear-gradient(135deg,var(--accent),#60a5fa);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px;box-shadow:0 4px 12px rgba(37,99,235,.4);}
.sb-logo h1{font-size:16px;font-weight:800;color:#fff;letter-spacing:-.3px;}
.sb-logo p{font-size:10px;color:rgba(255,255,255,.38);margin-top:3px;text-transform:uppercase;letter-spacing:.7px;line-height:1.4;}
.nav-sec{padding:18px 14px 6px;font-size:9.5px;color:rgba(255,255,255,.28);font-weight:700;letter-spacing:1.2px;text-transform:uppercase;}
.nav-item{display:flex;align-items:center;gap:11px;padding:10px 16px;margin:2px 8px;border-radius:9px;font-size:13px;font-weight:500;color:rgba(255,255,255,.6);text-decoration:none;transition:all .18s;}
.nav-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.nav-item.active{background:rgba(37,99,235,.85);color:#fff;font-weight:600;box-shadow:0 2px 10px rgba(37,99,235,.35);}
.nav-icon{font-size:15px;width:18px;text-align:center;}
.sb-year-btn{display:block;width:100%;text-align:left;padding:8px 12px;margin:2px 0;border:none;border-radius:8px;background:none;color:rgba(255,255,255,.55);font-family:var(--mono);font-size:13px;font-weight:700;cursor:pointer;transition:all .18s;}
.sb-year-btn:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-year-btn.active{background:rgba(37,99,235,.75);color:#fff;}
.sb-foot{margin-top:auto;padding:14px 16px;border-top:1px solid rgba(255,255,255,.07);font-size:11px;color:rgba(255,255,255,.28);text-align:center;}

/* ── MAIN ── */
.main{margin-left:252px;min-height:100vh;}
.topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 32px;height:58px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.topbar h2{font-size:15px;font-weight:700;}
.year-pill{padding:4px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:20px;font-size:11px;font-weight:700;color:var(--accent);font-family:var(--mono);}
.content{padding:28px 32px;max-width:900px;}

/* ── CARDS ── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px;box-shadow:var(--shadow);}
.card-head{padding:15px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-head h3{font-size:13.5px;font-weight:700;}
.card-head .sub{font-size:11.5px;color:var(--muted);}
.card-body{padding:22px;}

/* ── NO-DATA NOTICE ── */
.no-data-notice{background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1.5px solid #fde68a;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px;margin-bottom:20px;}
.no-data-notice .nd-icon{font-size:26px;flex-shrink:0;}
.no-data-notice .nd-text h4{font-size:13.5px;font-weight:700;color:#92400e;margin-bottom:3px;}
.no-data-notice .nd-text p{font-size:12px;color:#b45309;line-height:1.5;}
.no-data-notice a{display:inline-block;margin-top:8px;padding:6px 14px;background:#d97706;color:#fff;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;transition:background .18s;}
.no-data-notice a:hover{background:#b45309;}

/* ── PROGRAM INFO BOX ── */
.prog-info{background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:16px;display:none;}
.prog-info.show{display:block;}
.prog-info-title{font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;}
.prog-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.pig-item{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:10px 12px;}
.pig-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;}
.pig-val{font-size:13.5px;font-weight:800;font-family:var(--mono);}
.pv-pagu{color:var(--accent);}
.pv-used{color:var(--warning);}
.pv-sisa{color:var(--success);}
.pv-sisa.warn{color:var(--warning);}
.pv-sisa.danger{color:var(--danger);}
.prog-bar-wrap{margin-top:10px;}
.prog-bar-label{font-size:10.5px;color:var(--muted);margin-bottom:4px;}
.prog-bar{height:7px;background:#e5e7eb;border-radius:4px;overflow:hidden;}
.prog-bar-fill{height:100%;border-radius:4px;transition:width .5s ease;}

/* ── FORM ── */
.sec-title{font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:13px;padding-bottom:8px;border-bottom:1px dashed var(--border);}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:13px;}
label{font-size:11.5px;font-weight:600;color:#374151;}
.req{color:var(--danger);}
input,select,textarea{background:var(--surface2);border:1.5px solid var(--border);color:var(--text);padding:9px 13px;border-radius:8px;font-family:var(--font);font-size:13px;outline:none;transition:all .18s;width:100%;}
input:focus,select:focus,textarea:focus{border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.09);}
input.err,select.err{border-color:var(--danger);box-shadow:0 0 0 3px rgba(220,38,38,.09);}
.fgrid2{display:grid;grid-template-columns:1fr 1fr;gap:13px;}
.fgrid3{display:grid;grid-template-columns:2fr 1fr 1fr;gap:13px;}

/* ── PREVIEW PANEL ── */
.preview{background:linear-gradient(135deg,#f0f7ff,#e8f3ff);border:1.5px solid #bdd7ff;border-radius:10px;padding:16px;margin-bottom:16px;}
.preview-lbl{font-size:10.5px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:.5px;margin-bottom:11px;}
.preview-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px dashed #ccdeff;}
.preview-row:last-child{border-bottom:none;}
.pl{font-size:12px;color:var(--muted);}
.pv{font-size:12.5px;font-weight:700;font-family:var(--mono);}
.preview-total{background:#fff;border-radius:9px;padding:13px 15px;margin-top:11px;display:flex;justify-content:space-between;align-items:center;border:1.5px solid #bdd7ff;}
.ptl{font-size:12.5px;font-weight:600;color:var(--navy);}
.ptv{font-size:21px;font-weight:800;font-family:var(--mono);color:var(--accent);}
.ptv.over{color:var(--danger);}
.pagu-ind{padding:9px 14px;border-radius:8px;font-size:12px;font-weight:600;margin-top:9px;display:none;}
.pi-ok{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;display:block;}
.pi-warn{background:#fef9c3;color:#92400e;border:1px solid #fde68a;display:block;}
.pi-over{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;display:block;}

/* ── ALERT ── */
.alert{padding:12px 16px;border-radius:9px;font-size:12.5px;font-weight:600;display:none;margin-bottom:14px;align-items:center;gap:10px;}
.alert.show{display:flex;}
.al-success{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;}
.al-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
.al-warning{background:#fef9c3;color:#92400e;border:1px solid #fde68a;}

/* ── BUTTONS ── */
.btn-row{display:flex;gap:11px;}
.btn{padding:10px 22px;border-radius:8px;border:none;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .18s;}
.btn-primary{background:var(--accent);color:#fff;flex:1;}
.btn-primary:hover{background:var(--accent2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(37,99,235,.3);}
.btn-primary:disabled{background:#bfdbfe;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-reset{background:var(--surface2);color:var(--muted);border:1.5px solid var(--border);}
.btn-reset:hover{background:var(--border);}

/* ── RIWAYAT TABLE ── */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th{text-align:left;padding:9px 14px;font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;background:var(--surface2);border-bottom:1px solid var(--border);}
td{padding:11px 14px;border-bottom:1px solid var(--border);font-size:12.5px;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
.badge{padding:3px 9px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;}
.b-pending{background:#fef9c3;color:#92400e;} .b-ok{background:#d1fae5;color:#065f46;} .b-reject{background:#fee2e2;color:#991b1b;}
.mono{font-family:var(--mono);font-size:12px;font-weight:600;}
.chip{display:inline-block;padding:2px 9px;border-radius:6px;font-size:11px;font-weight:600;background:#eff6ff;color:var(--accent);}
.empty{text-align:center;padding:30px;color:var(--muted);font-size:13px;}

@media(max-width:900px){
  .sidebar{width:200px;}.main{margin-left:200px;}
  .fgrid2,.fgrid3{grid-template-columns:1fr;}
  .prog-info-grid{grid-template-columns:1fr 1fr;}
}
</style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
  <div class="sb-logo">
    <div class="mark">📋</div>
    <h1>SIPUA</h1>
    <p>Sistem Pengendalian Usulan Anggaran</p>
  </div>
  <div class="nav-sec">Menu Utama</div>
  <a class="nav-item" href="dashboard.php"><span class="nav-icon">📊</span> Dashboard</a>
  <a class="nav-item active" href="input_usulan.php"><span class="nav-icon">➕</span> Input Usulan</a>

  <div class="nav-sec" style="margin-top:8px;">Tahun Anggaran</div>
  <div style="padding:0 10px;" id="sbYearList">
    <div style="padding:6px;font-size:11px;color:rgba(255,255,255,.3);">Belum ada data</div>
  </div>

  <div class="sb-foot">SIPUA v2.0 &mdash; Input Usulan</div>
</aside>

<!-- ═══ MAIN ═══ -->
<main class="main">
  <div class="topbar">
    <div class="topbar-left">
      <h2>➕ Input Usulan Baru</h2>
      <span class="year-pill" id="topbarYear">—</span>
    </div>
  </div>

  <div class="content">

    <!-- NO DATA NOTICE -->
    <div class="no-data-notice" id="noDataNotice" style="display:none;">
      <div class="nd-icon">⚠️</div>
      <div class="nd-text">
        <h4>Data pagu belum tersedia</h4>
        <p>Anda perlu mengimport file Excel RKA/RAPBD terlebih dahulu agar dapat memasukkan usulan.</p>
        <a href="dashboard.php">← Pergi ke Dashboard untuk Import Data</a>
      </div>
    </div>

    <!-- FORM CARD -->
    <div class="card" id="formCard">
      <div class="card-head">
        <h3>📝 Form Usulan Barang / Kegiatan</h3>
        <span class="sub" id="formYearLabel">—</span>
      </div>
      <div class="card-body">

        <div id="alertBox" class="alert"></div>

        <!-- PILIH PROGRAM / SUB KEGIATAN -->
        <div class="sec-title">🗂️ Pilih Sub Kegiatan / Rekening</div>
        <div class="fgrid2" style="margin-bottom:6px;">
          <div class="form-group" style="margin-bottom:0;">
            <label>Program <span class="req">*</span></label>
            <select id="fProgramFilter" onchange="filterSub()">
              <option value="">-- Semua Program --</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label>Sub Kegiatan / Rekening <span class="req">*</span></label>
            <select id="fSubKegiatan" onchange="onSubChange()">
              <option value="">-- Pilih Sub Kegiatan --</option>
            </select>
          </div>
        </div>

        <!-- INFO PAGU SUB KEGIATAN TERPILIH -->
        <div class="prog-info" id="progInfo">
          <div class="prog-info-title">📊 Info Pagu Sub Kegiatan Terpilih</div>
          <div class="prog-info-grid">
            <div class="pig-item"><div class="pig-lbl">💰 Pagu Anggaran</div><div class="pig-val pv-pagu" id="piPagu">—</div></div>
            <div class="pig-item"><div class="pig-lbl">📦 Sudah Diusulkan</div><div class="pig-val pv-used" id="piUsed">—</div></div>
            <div class="pig-item"><div class="pig-lbl">✅ Sisa Tersedia</div><div class="pig-val pv-sisa" id="piSisa">—</div></div>
          </div>
          <div class="prog-bar-wrap">
            <div class="prog-bar-label" id="piPct">0% terpakai</div>
            <div class="prog-bar"><div class="prog-bar-fill" id="piBarFill" style="width:0%;background:#2563eb;"></div></div>
          </div>
        </div>

        <!-- INFORMASI BARANG -->
        <div class="sec-title" style="margin-top:4px;">📦 Informasi Barang / Kegiatan</div>
        <div class="form-group">
          <label>Nama Barang / Kegiatan <span class="req">*</span></label>
          <input type="text" id="fNama" placeholder="Contoh: Laptop Dell Inspiron i7, Pengadaan ATK, dll." oninput="updatePreview()">
        </div>
        <div class="fgrid3">
          <div class="form-group">
            <label>Volume <span class="req">*</span></label>
            <input type="number" id="fVolume" value="1" min="1" oninput="updatePreview()">
          </div>
          <div class="form-group">
            <label>Satuan <span class="req">*</span></label>
            <input type="text" id="fSatuan" placeholder="Unit / Rim / Paket">
          </div>
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="req">*</span></label>
            <input type="number" id="fHarga" placeholder="0" min="0" oninput="updatePreview()">
          </div>
        </div>
        <div class="form-group">
          <label>Spesifikasi / Keterangan</label>
          <input type="text" id="fKet" placeholder="Contoh: RAM 16GB, SSD 512GB, warna hitam">
        </div>

        <!-- PREVIEW KALKULASI -->
        <div class="sec-title" style="margin-top:4px;">🔢 Kalkulasi Nilai Usulan</div>
        <div class="preview">
          <div class="preview-lbl">📊 Rincian Perhitungan</div>
          <div class="preview-row"><span class="pl">Volume</span><span class="pv" id="pvVol">0</span></div>
          <div class="preview-row"><span class="pl">Harga Satuan</span><span class="pv" id="pvHarga">Rp 0</span></div>
          <div class="preview-row"><span class="pl">Pagu Sub Kegiatan</span><span class="pv" id="pvPagu">—</span></div>
          <div class="preview-row"><span class="pl">Sisa Pagu Tersedia</span><span class="pv" id="pvSisa">— (pilih sub kegiatan dulu)</span></div>
          <div class="preview-total">
            <span class="ptl">TOTAL NILAI USULAN</span>
            <span class="ptv" id="pvTotal">Rp 0</span>
          </div>
          <div id="paguInd" class="pagu-ind"></div>
        </div>

        <!-- TOMBOL -->
        <div class="btn-row">
          <button class="btn btn-reset" onclick="resetForm()">🔄 Reset</button>
          <button class="btn btn-primary" id="btnSubmit" onclick="submitUsulan()">✅ Ajukan Usulan</button>
        </div>

      </div>
    </div>

    <!-- RIWAYAT SESI -->
    <div class="card" id="riwayatCard" style="display:none;">
      <div class="card-head">
        <h3>📋 Riwayat Usulan Sesi Ini</h3>
        <span class="sub" id="riwayatCount">0 usulan</span>
      </div>
      <div class="table-wrap"><table>
        <thead><tr>
          <th>#</th><th>Nama Barang / Kegiatan</th><th>Sub Kegiatan</th><th>Volume</th><th>Total Nilai</th><th>Status</th>
        </tr></thead>
        <tbody id="riwayatTbody"></tbody>
      </table></div>
    </div>

  </div>
</main>

<script>
// ══════════════════════════════════════════════
//  STATE & STORAGE
// ══════════════════════════════════════════════
let state = { activeYear:null, years:[], paguData:{}, usulan:[] };
let riwayatSesi = [];

function save() { try { localStorage.setItem('sipua_v2', JSON.stringify(state)); } catch(e){} }
function load() { try { const r = localStorage.getItem('sipua_v2'); if(r) state = JSON.parse(r); } catch(e){} }
const rp = n => 'Rp ' + Math.round(Number(n)).toLocaleString('id-ID');
const uid = () => Math.random().toString(36).slice(2,9);

// ══════════════════════════════════════════════
//  COMPUTED PAGU
// ══════════════════════════════════════════════
function getPaguList(year) {
  const raw = state.paguData[year];
  if (!raw) return [];
  return Object.entries(raw).map(([subKey, sub]) => {
    const used = (state.usulan||[])
      .filter(u => u.year===year && u.subId===subKey && u.status!=='ditolak')
      .reduce((s,u) => s+u.total, 0);
    return { subKey, ...sub, used, sisa: sub.pagu-used, persen: sub.pagu>0 ? Math.round((used/sub.pagu)*100) : 0 };
  });
}

// ══════════════════════════════════════════════
//  SIDEBAR YEAR RENDER
// ══════════════════════════════════════════════
function renderSidebar() {
  const el = document.getElementById('sbYearList');
  if (!state.years.length) {
    el.innerHTML = '<div style="padding:6px;font-size:11px;color:rgba(255,255,255,.3);">Belum ada data</div>';
    document.getElementById('topbarYear').textContent = '—';
    return;
  }
  el.innerHTML = state.years.map(y =>
    `<button class="sb-year-btn ${state.activeYear===y?'active':''}" onclick="setYear('${y}')">📅 TA ${y}</button>`
  ).join('');
  document.getElementById('topbarYear').textContent = state.activeYear ? 'TA '+state.activeYear : '—';
}

function setYear(y) {
  state.activeYear = y; save();
  renderSidebar(); initForm();
}

// ══════════════════════════════════════════════
//  INIT FORM — Isi dropdowns
// ══════════════════════════════════════════════
function initForm() {
  const y = state.activeYear;
  document.getElementById('formYearLabel').textContent = y ? 'TA '+y : '—';

  const noData = document.getElementById('noDataNotice');
  const formCard = document.getElementById('formCard');

  if (!y || !state.paguData[y] || !Object.keys(state.paguData[y]).length) {
    noData.style.display = 'flex';
    formCard.style.display = 'none';
    return;
  }
  noData.style.display = 'none';
  formCard.style.display = 'block';

  const paguList = getPaguList(y);

  // Isi filter program
  const programs = [...new Set(paguList.map(p=>p.program).filter(Boolean))];
  const selProg = document.getElementById('fProgramFilter');
  selProg.innerHTML = '<option value="">-- Semua Program --</option>' +
    programs.map(p => `<option value="${escHtml(p)}">${p.length>60?p.slice(0,60)+'…':p}</option>`).join('');

  // Isi sub kegiatan (semua)
  fillSubSelect(paguList);
}

function fillSubSelect(paguList) {
  const sel = document.getElementById('fSubKegiatan');
  sel.innerHTML = '<option value="">-- Pilih Sub Kegiatan --</option>' +
    paguList.map(p => {
      const label = p.sub || p.kegiatan || p.program;
      return `<option value="${escHtml(p.subKey)}">${label.length>65?label.slice(0,65)+'…':label}</option>`;
    }).join('');
}

function filterSub() {
  const progFilter = document.getElementById('fProgramFilter').value;
  const y = state.activeYear;
  if (!y) return;
  let paguList = getPaguList(y);
  if (progFilter) paguList = paguList.filter(p => p.program === progFilter);
  fillSubSelect(paguList);
  document.getElementById('fSubKegiatan').value = '';
  hideProgInfo();
  updatePreview();
}

function onSubChange() {
  const subKey = document.getElementById('fSubKegiatan').value;
  if (!subKey || !state.activeYear) { hideProgInfo(); updatePreview(); return; }
  const paguList = getPaguList(state.activeYear);
  const p = paguList.find(x => x.subKey === subKey);
  if (!p) { hideProgInfo(); return; }

  // Tampilkan info pagu
  const pct = Math.min(p.persen, 100);
  const barColor = pct>=90?'#dc2626':pct>=70?'#d97706':'#2563eb';
  document.getElementById('piPagu').textContent = rp(p.pagu);
  document.getElementById('piUsed').textContent = rp(p.used);
  const sisaEl = document.getElementById('piSisa');
  sisaEl.textContent = rp(p.sisa);
  sisaEl.className = 'pig-val pv-sisa' + (p.sisa<0?' danger':pct>=70?' warn':'');
  document.getElementById('piPct').textContent = pct + '% telah diusulkan';
  document.getElementById('piBarFill').style.width = pct + '%';
  document.getElementById('piBarFill').style.background = barColor;
  document.getElementById('progInfo').classList.add('show');

  updatePreview();
}

function hideProgInfo() {
  document.getElementById('progInfo').classList.remove('show');
}

function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;'); }

// ══════════════════════════════════════════════
//  PREVIEW REAL-TIME
// ══════════════════════════════════════════════
function updatePreview() {
  const vol    = parseFloat(document.getElementById('fVolume').value) || 0;
  const harga  = parseFloat(document.getElementById('fHarga').value) || 0;
  const subKey = document.getElementById('fSubKegiatan').value;
  const total  = vol * harga;

  document.getElementById('pvVol').textContent   = vol.toLocaleString('id-ID');
  document.getElementById('pvHarga').textContent = rp(harga);
  const pvTotal = document.getElementById('pvTotal');
  pvTotal.textContent = rp(total);
  pvTotal.className = 'ptv';

  const ind = document.getElementById('paguInd');
  ind.className = 'pagu-ind';
  ind.textContent = '';

  if (subKey && state.activeYear) {
    const p = getPaguList(state.activeYear).find(x => x.subKey === subKey);
    if (p) {
      document.getElementById('pvPagu').textContent = rp(p.pagu);
      document.getElementById('pvSisa').textContent = rp(p.sisa);
      if (total > 0) {
        if (total > p.sisa) {
          pvTotal.className = 'ptv over';
          ind.className = 'pagu-ind pi-over';
          ind.textContent = `🚫 Melebihi pagu! Kekurangan: ${rp(total - p.sisa)} — Usulan akan otomatis ditolak.`;
        } else if ((p.sisa - total) / (p.pagu||1) < 0.1) {
          ind.className = 'pagu-ind pi-warn';
          ind.textContent = `⚠️ Sisa pagu setelah usulan: ${rp(p.sisa - total)} (hampir habis)`;
        } else {
          ind.className = 'pagu-ind pi-ok';
          ind.textContent = `✅ Sisa pagu setelah usulan: ${rp(p.sisa - total)}`;
        }
      }
    }
  } else {
    document.getElementById('pvPagu').textContent = '—';
    document.getElementById('pvSisa').textContent = '— (pilih sub kegiatan dulu)';
  }
}

// ══════════════════════════════════════════════
//  SUBMIT USULAN
// ══════════════════════════════════════════════
function submitUsulan() {
  if (!state.activeYear) return showAlert('warning','Tidak ada tahun anggaran aktif. Kembali ke Dashboard untuk import data.');

  const nama   = document.getElementById('fNama').value.trim();
  const subKey = document.getElementById('fSubKegiatan').value;
  const vol    = parseFloat(document.getElementById('fVolume').value) || 0;
  const satuan = document.getElementById('fSatuan').value.trim() || 'Unit';
  const harga  = parseFloat(document.getElementById('fHarga').value) || 0;
  const ket    = document.getElementById('fKet').value.trim();

  let errors = [];
  const setErr = (id,cond) => { document.getElementById(id).classList.toggle('err',cond); return cond; };
  if (setErr('fNama',    !nama))      errors.push('Nama barang');
  if (setErr('fSubKegiatan', !subKey)) errors.push('Sub Kegiatan');
  if (setErr('fVolume',  vol<=0))     errors.push('Volume');
  if (setErr('fHarga',   harga<=0))   errors.push('Harga satuan');
  if (errors.length) { showAlert('warning', 'Field wajib diisi: <b>'+errors.join(', ')+'</b>'); return; }

  const total = vol * harga;
  const paguList = getPaguList(state.activeYear);
  const p = paguList.find(x => x.subKey === subKey);
  const status = (p && total > p.sisa) ? 'ditolak' : 'pending';

  const usulan = {
    id: uid(), year: state.activeYear, subId: subKey,
    nama, volume: vol, satuan, harga, total, keterangan: ket,
    status, tgl: new Date().toISOString()
  };
  if (!state.usulan) state.usulan = [];
  state.usulan.push(usulan);
  save();

  riwayatSesi.unshift(usulan);

  if (status === 'ditolak') {
    showAlert('danger', `🚫 Usulan <b>"${nama}"</b> DITOLAK otomatis karena melebihi sisa pagu yang tersedia.`, false);
  } else {
    showAlert('success', `✅ Usulan <b>"${nama}"</b> berhasil diajukan! Status: <b>Pending</b> — Menunggu persetujuan.`);
    resetFormFields();
  }

  renderRiwayat();
  onSubChange(); // refresh info pagu
}

function showAlert(type, msg, autohide=true) {
  const el = document.getElementById('alertBox');
  const icon = type==='danger'?'🚫':type==='success'?'✅':'⚠️';
  el.className = `alert al-${type} show`;
  el.innerHTML = `<span style="font-size:16px">${icon}</span><span>${msg}</span>`;
  el.scrollIntoView({behavior:'smooth',block:'nearest'});
  if (autohide) setTimeout(()=>el.classList.remove('show'), 5500);
}

// ══════════════════════════════════════════════
//  RESET
// ══════════════════════════════════════════════
function resetFormFields() {
  ['fNama','fSatuan','fKet'].forEach(id => document.getElementById(id).value='');
  document.getElementById('fVolume').value = 1;
  document.getElementById('fHarga').value = '';
  document.getElementById('fSubKegiatan').value = '';
  ['fNama','fVolume','fHarga','fSubKegiatan'].forEach(id => document.getElementById(id).classList.remove('err'));
  hideProgInfo();
  document.getElementById('pvVol').textContent = '0';
  document.getElementById('pvHarga').textContent = 'Rp 0';
  document.getElementById('pvTotal').textContent = 'Rp 0';
  document.getElementById('pvTotal').className = 'ptv';
  document.getElementById('pvPagu').textContent = '—';
  document.getElementById('pvSisa').textContent = '— (pilih sub kegiatan dulu)';
  document.getElementById('paguInd').className = 'pagu-ind';
}

function resetForm() {
  resetFormFields();
  document.getElementById('alertBox').classList.remove('show');
}

// ══════════════════════════════════════════════
//  RIWAYAT SESI
// ══════════════════════════════════════════════
function renderRiwayat() {
  document.getElementById('riwayatCard').style.display = 'block';
  document.getElementById('riwayatCount').textContent = riwayatSesi.length+' usulan';
  document.getElementById('riwayatTbody').innerHTML = riwayatSesi.map((u,i) => {
    const sc = u.status==='pending'?'b-pending':u.status==='ditolak'?'b-reject':'b-ok';
    const sl = u.status==='pending'?'⏳ Pending':u.status==='ditolak'?'🚫 Ditolak':'✅ Disetujui';
    const subData = state.paguData[u.year]?.[u.subId];
    const subLabel = subData ? (subData.sub||subData.kegiatan||subData.program||'') : u.subId;
    return `<tr>
      <td style="color:var(--muted);font-size:11px;">${i+1}</td>
      <td style="font-weight:600">${u.nama}<div style="font-size:11px;color:var(--muted)">${u.keterangan||''}</div></td>
      <td><span class="chip" title="${subLabel}">${subLabel.length>32?subLabel.slice(0,32)+'…':subLabel}</span></td>
      <td style="font-size:12px">${u.volume} ${u.satuan}</td>
      <td class="mono" style="color:${u.status==='ditolak'?'var(--danger)':''}">${rp(u.total)}</td>
      <td><span class="badge ${sc}">${sl}</span></td>
    </tr>`;
  }).join('');
}

// ══════════════════════════════════════════════
//  INIT
// ══════════════════════════════════════════════
load();
if (!state.years)    state.years=[];
if (!state.paguData) state.paguData={};
if (!state.usulan)   state.usulan=[];
renderSidebar();
initForm();
</script>
</body>
</html>