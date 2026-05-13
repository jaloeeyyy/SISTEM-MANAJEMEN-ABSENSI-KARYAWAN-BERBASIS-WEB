<?php
session_start();
require_once 'auth.php';
$auth = new Auth();
if (!isset($_SESSION['username'])) { header("Location: index.php"); exit(); }

$msg = ""; $color = "";

if (isset($_POST['absen_sekarang'])) {
    $proses = $auth->submitAbsen($_SESSION['username']);
    if ($proses === true) { $msg = "Berhasil mencatat absensi!"; $color = "success"; }
    elseif ($proses === "sudah") { $msg = "Anda sudah absen pada sesi ini."; $color = "warning"; }
    elseif ($proses === "diluar_jam") { $msg = "Gagal! Saat ini berada di luar jadwal absensi."; $color = "danger"; }
}

if (isset($_POST['add_user'])) {
    if($auth->addUser($_POST['username'], $_POST['password'], $_POST['role'], $_POST['nama'], $_POST['divisi'])) {
        $msg = "Karyawan baru berhasil ditambahkan!"; $color = "success";
    } else { $msg = "Gagal! Username sudah digunakan."; $color = "danger"; }
}

if (isset($_POST['edit_user'])) {
    $auth->editUser($_POST['id'], $_POST['username'], $_POST['role'], $_POST['nama'], $_POST['divisi']);
    $msg = "Data karyawan berhasil diperbarui!"; $color = "success";
}

if (isset($_POST['delete_user'])) {
    $auth->deleteUser($_POST['id']);
    $msg = "Karyawan berhasil dihapus!"; $color = "success";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kantor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .nav-tabs .nav-link.active { font-weight: bold; color: #198754; border-bottom: 3px solid #198754; }
        .nav-tabs .nav-link { color: #6c757d; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <span class="navbar-brand h1 fw-bold"><i class="bi bi-building"></i> Sistem Kantor UIN</span>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?php if($msg != "") echo "<div class='alert alert-$color shadow-sm fw-bold'><i class='bi bi-info-circle'></i> $msg</div>"; ?>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-center shadow-sm mb-4 border-0">
                    <div class="card-body">
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="80" class="mb-3">
                        <h5 class="fw-bold"><?=$_SESSION['nama']?></h5>
                        <p class="badge bg-primary fs-6"><?=$_SESSION['role']?></p>
                        <?php if ($_SESSION['role'] != 'Pendiri'): ?>
    <p class="text-muted small mb-0"><i class="bi bi-briefcase"></i> Divisi: <?= isset($_SESSION['divisi']) ? $_SESSION['divisi'] : '-' ?></p>
<?php endif; ?>
                    </div>
                </div>

                <?php if ($_SESSION['role'] != 'Admin'): ?>
                <div class="card shadow-sm border-0 bg-success text-white text-center mb-4">
                    <div class="card-body">
                        <h6>Absensi Sesi: <?=date('H:i')?></h6>
                        <form method="POST"><button type="submit" name="absen_sekarang" class="btn btn-light fw-bold w-100 mt-2 text-success">ABSEN SEKARANG</button></form>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-3 pb-0 border-0">
                        <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#absen">Rekap Absensi</button></li>
                            <?php if($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Pendiri'): ?>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#karyawan">Manajemen Karyawan</button></li>
                            <?php endif; ?>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#kalender">Kalender Kerja 2026</button></li>
                        </ul>
                    </div>
                    <div class="card-body p-4 tab-content">
                        
                        <div class="tab-pane fade show active" id="absen">
                            <h5 class="fw-bold mb-3"><?=($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Pendiri') ? "Rekap Seluruh Karyawan" : "Riwayat Absensi Saya"?></h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light"><tr>
                                        <th>No</th>
                                        <?php if($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Pendiri') echo "<th>Nama</th>"; ?>
                                        <th>Sesi</th><th>Hari & Tanggal</th><th>Jam Masuk</th><th>Status</th>
                                    </tr></thead>
                                    <tbody>
                                        <?php 
                                        $rekap = $auth->getRekap($_SESSION['role'], $_SESSION['username']); $no=1;
                                        $daftar_hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                                        while($row = $rekap->fetch_assoc()): ?>
                                        <tr>
                                            <td><?=$no++?></td>
                                            <?php if($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Pendiri') echo "<td><b>{$row['nama_lengkap']}</b><br><small class='text-muted'>{$row['divisi']}</small></td>"; ?>
                                            <td><span class="badge bg-info text-dark"><?=$row['jenis_absen']?></span></td>
                                            <td><?=$daftar_hari[date('l', strtotime($row['tanggal']))]?>, <?=date('d-m-Y', strtotime($row['tanggal']))?></td>
                                            <td><?=$row['jam_masuk']?></td>
                                            <td><span class="badge bg-success"><i class="bi bi-check-circle"></i> <?=$row['keterangan']?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Pendiri'): ?>
                        <div class="tab-pane fade" id="karyawan">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0">Data Pegawai</h5>
                                <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd"><i class="bi bi-plus-lg"></i> Tambah Karyawan</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-dark"><tr><th>Nama</th><th>Username</th><th>Role</th><th>Divisi</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php 
                                        $users = $auth->getAllUsers();
                                        while($u = $users->fetch_assoc()): 
                                        ?>
                                        <tr>
                                            <td class="text-start fw-bold"><?=$u['nama_lengkap']?></td>
                                            <td><?=$u['username']?></td>
                                            <td><span class="badge bg-secondary"><?=$u['role']?></span></td>
                                            <td><?=$u['divisi']?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?=$u['id']?>"><i class="bi bi-pencil-square"></i></button>
                                                <?php if($u['username'] != $_SESSION['username']): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?');">
                                                    <input type="hidden" name="id" value="<?=$u['id']?>">
                                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="modalEdit<?=$u['id']?>" tabindex="-1">
                                            <div class="modal-dialog"><div class="modal-content text-start">
                                                <div class="modal-header bg-warning"><h5 class="modal-title fw-bold">Edit Pegawai</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <form method="POST"><div class="modal-body">
                                                    <input type="hidden" name="id" value="<?=$u['id']?>">
                                                    <div class="mb-2"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" value="<?=$u['nama_lengkap']?>" required></div>
                                                    <div class="mb-2"><label>Username</label><input type="text" name="username" class="form-control" value="<?=$u['username']?>" required></div>
                                                    <div class="mb-2"><label>Role</label>
                                                        <select name="role" class="form-select">
                                                            <option value="Karyawan" <?=($u['role']=='Karyawan')?'selected':''?>>Karyawan</option>
                                                            <option value="HR" <?=($u['role']=='HR')?'selected':''?>>HR</option>
                                                            <option value="Admin" <?=($u['role']=='Admin')?'selected':''?>>Admin</option>
                                                            <option value="Pendiri" <?=($u['role']=='Pendiri')?'selected':''?>>Pendiri</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2"><label>Divisi</label><input type="text" name="divisi" class="form-control" value="<?=$u['divisi']?>"></div>
                                                </div><div class="modal-footer"><button type="submit" name="edit_user" class="btn btn-warning fw-bold">Simpan Perubahan</button></div></form>
                                            </div></div>
                                        </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="tab-pane fade" id="kalender">
                            <h5 class="fw-bold mb-3">Informasi Jadwal & Kalender Kerja 2026</h5>
                            <div class="alert alert-secondary">
                                <h6 class="fw-bold"><i class="bi bi-clock-history"></i> Aturan Jam Kerja Nasional (WIB)</h6>
                                <ul class="mb-0">
                                    <li><b>Senin:</b> Pagi (07:00-07:30) | Siang (12:00-12:30) | Sore (16:00-16:30)</li>
                                    <li><b>Selasa - Kamis:</b> Pagi (09:00-09:30) | Siang (12:00-12:30) | Sore (16:00-16:30)</li>
                                    <li><b>Jumat:</b> Pagi (09:00-09:30) | Sore (16:00-16:30) <i>*Tanpa absen siang</i></li>
                                </ul>
                            </div>
                            <h6 class="fw-bold mt-4">Hari Libur Nasional Indonesia 2026 (Perkiraan)</h6>
                            <table class="table table-sm table-striped border">
                                <thead><tr><th>Bulan</th><th>Tanggal</th><th>Keterangan Libur</th></tr></thead>
                                <tbody>
                                    <tr><td>Januari</td><td>1 Jan</td><td>Tahun Baru 2026 Masehi</td></tr>
                                    <tr><td>Maret</td><td>20-21 Mar</td><td>Hari Raya Idul Fitri 1447 Hijriah (Perkiraan)</td></tr>
                                    <tr><td>Mei</td><td>1 Mei</td><td>Hari Buruh Internasional</td></tr>
                                    <tr><td>Mei</td><td>26 Mei</td><td>Hari Raya Waisak</td></tr>
                                    <tr><td>Agustus</td><td>17 Agu</td><td>Hari Kemerdekaan RI</td></tr>
                                    <tr><td>Desember</td><td>25 Des</td><td>Hari Raya Natal</td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold">Tambah Karyawan Baru</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <form method="POST"><div class="modal-body">
                <div class="mb-2"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                <div class="mb-2"><label>Divisi (Contoh: IT, HRD, CS)</label><input type="text" name="divisi" class="form-control" required></div>
                <div class="mb-2"><label>Role / Jabatan</label>
                    <select name="role" class="form-select" required>
                        <option value="Karyawan">Karyawan Biasa</option><option value="HR">Staf HR</option><option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="mb-2"><label>Username Login</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-2"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            </div><div class="modal-footer"><button type="submit" name="add_user" class="btn btn-primary fw-bold">Daftarkan Karyawan</button></div></form>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
