<?php
require_once 'config.php';

class Auth extends Database {
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $this->conn->query($query);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['divisi'] = $user['divisi'];
            return $user['role'];
        }
        return false;
    }

    public function submitAbsen($username) {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $jam_sekarang = date('H:i:s');
        $waktu = date('H:i');
        
        $hari_indo = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
        $hari = $hari_indo[date('l')];
        $shift = "";
        
        if ($hari == 'Senin') {
            if ($waktu >= '07:00' && $waktu <= '07:30') $shift = 'Pagi';
            elseif ($waktu >= '12:00' && $waktu <= '12:30') $shift = 'Siang';
            elseif ($waktu >= '16:00' && $waktu <= '16:30') $shift = 'Sore';
        } elseif ($hari == 'Jumat') {
            if ($waktu >= '09:00' && $waktu <= '09:30') $shift = 'Pagi';
            elseif ($waktu >= '16:00' && $waktu <= '16:30') $shift = 'Sore';
        } elseif (in_array($hari, ['Selasa', 'Rabu', 'Kamis'])) {
            if ($waktu >= '09:00' && $waktu <= '09:30') $shift = 'Pagi';
            elseif ($waktu >= '12:00' && $waktu <= '12:30') $shift = 'Siang';
            elseif ($waktu >= '16:00' && $waktu <= '16:30') $shift = 'Sore';
        }

        if ($shift == "") return "diluar_jam";

        $cek = $this->conn->query("SELECT * FROM absensi WHERE username = '$username' AND tanggal = '$tanggal' AND jenis_absen = '$shift'");
        if ($cek->num_rows == 0) {
            return $this->conn->query("INSERT INTO absensi (username, tanggal, jenis_absen, jam_masuk, keterangan) VALUES ('$username', '$tanggal', '$shift', '$jam_sekarang', 'Hadir')");
        }
        return "sudah";
    }

    public function getRekap($role, $username = null) {
        if ($role == 'Admin' || $role == 'Pendiri') {
            return $this->conn->query("SELECT absensi.*, users.nama_lengkap, users.role as user_role, users.divisi FROM absensi JOIN users ON absensi.username = users.username ORDER BY tanggal DESC, jam_masuk DESC");
        } else {
            return $this->conn->query("SELECT * FROM absensi WHERE username = '$username' ORDER BY tanggal DESC, jam_masuk DESC");
        }
    }

    // --- FITUR BARU: MANAJEMEN KARYAWAN (CRUD) ---
    public function getAllUsers() {
        return $this->conn->query("SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
    }

    public function addUser($username, $password, $role, $nama, $divisi) {
        // Cek duplikat username
        $cek = $this->conn->query("SELECT * FROM users WHERE username = '$username'");
        if($cek->num_rows > 0) return false;
        return $this->conn->query("INSERT INTO users (username, password, role, nama_lengkap, divisi) VALUES ('$username', '$password', '$role', '$nama', '$divisi')");
    }

    public function editUser($id, $username, $role, $nama, $divisi) {
        return $this->conn->query("UPDATE users SET username='$username', role='$role', nama_lengkap='$nama', divisi='$divisi' WHERE id='$id'");
    }

    public function deleteUser($id) {
        return $this->conn->query("DELETE FROM users WHERE id='$id'");
    }
}
?>