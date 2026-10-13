<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config/Database.php';
require 'includes/fungsi.php';

$db = Database::getInstance()->getConnection();

$cari = trim($_GET['cari'] ?? '');
$perHalaman = 5;
$halaman = max(1, (int) ($_GET['hal'] ?? 1));

$from = "FROM produk p
         JOIN kategori k ON p.kategori_id = k.id
         JOIN supplier s ON p.supplier_id = s.id";

$where = '';
$param = [];

if ($cari !== '') {
    $where = "WHERE p.nama_produk LIKE :cari1
              OR p.kode_produk LIKE :cari2
              OR k.nama_kategori LIKE :cari3";
    $kata = '%' . $cari . '%';
    $param = [':cari1' => $kata, ':cari2' => $kata, ':cari3' => $kata];
}

// hitung total data untuk pagination
$stmt = $db->prepare("SELECT COUNT(*) $from $where");
$stmt->execute($param);
$total = (int) $stmt->fetchColumn();

$totalHalaman = max(1, (int) ceil($total / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

$sql = "SELECT p.id, p.kode_produk, p.nama_produk, p.harga, p.stok,
               k.nama_kategori, s.nama_supplier
        $from $where
        ORDER BY p.id DESC
        LIMIT :batas OFFSET :mulai";

$stmt = $db->prepare($sql);
foreach ($param as $nama => $nilai) {
    $stmt->bindValue($nama, $nilai);
}
$stmt->bindValue(':batas', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':mulai', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftar = $stmt->fetchAll();

$judul = 'Daftar Produk';
include 'includes/header.php';
?>

<div class="judul-halaman">
    <h1>Daftar Produk</h1>
    <a href="tambah.php" class="tombol">+ Tambah Produk</a>
</div>

<form method="get" action="index.php" class="cari">
    <label for="cari" class="sr-only">Cari produk</label>
    <input type="text" id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Cari nama, kode, atau kategori">
    <button type="submit" class="tombol">Cari</button>
    <?php if ($cari !== ''): ?>
        <a href="index.php" class="tombol abu">Reset</a>
    <?php endif; ?>
</form>

<div class="tabel-wrap">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$daftar): ?>
                <tr>
                    <td colspan="8" class="kosong">Data produk tidak ditemukan.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($daftar as $i => $p): ?>
                <tr>
                    <td><?= $offset + $i + 1 ?></td>
                    <td><?= e($p['kode_produk']) ?></td>
                    <td><?= e($p['nama_produk']) ?></td>
                    <td><?= e($p['nama_kategori']) ?></td>
                    <td><?= e($p['nama_supplier']) ?></td>
                    <td><?= e(rupiah($p['harga'])) ?></td>
                    <td>
                        <?= e($p['stok']) ?>
                        <?php if ($p['stok'] < 10): ?>
                            <span class="menipis">Menipis</span>
                        <?php endif; ?>
                    </td>
                    <td class="aksi">
                        <a href="edit.php?id=<?= e($p['id']) ?>" class="tombol kecil">Edit</a>
                        <form method="post" action="hapus.php" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                            <button type="submit" class="tombol kecil merah">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalHalaman > 1): ?>
    <nav class="halaman" aria-label="Halaman data">
        <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
            <a href="?<?= e(http_build_query(['cari' => $cari, 'hal' => $i])) ?>"
               class="<?= $i === $halaman ? 'aktif' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>

<p class="total">Total <?= e($total) ?> produk</p>

<?php include 'includes/footer.php'; ?>
// ... (kode Anda sebelumnya sampai variabel $from)

// 1. Siapkan query SELECT (berdasarkan pencarian jika ada)
$sql = "SELECT p.*, k.nama as nama_kategori, s.nama as nama_supplier " . $from;
if ($cari !== '') {
    $sql .= " WHERE p.nama_produk LIKE :cari OR p.kode_produk LIKE :cari";
}

// 2. Eksekusi query (Prepared Statement)
$stmt = $db->prepare($sql);
if ($cari !== '') {
    $stmt->bindValue(':cari', "%$cari%", PDO::PARAM_STR);
}
$stmt->execute();
$produk_list = $stmt->fetchAll();
?>

<!-- 3. Buat Tabel HTML -->
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Supplier</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($produk_list)): ?>
            <tr><td colspan="6">Data produk tidak ditemukan.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach($produk_list as $row): ?>
            <tr>
                <td><?= $no++ ?></td>
                <!-- Wajib menggunakan htmlspecialchars() sesuai requirement tugas -->
                <td><?= htmlspecialchars($row['kode_produk']) ?></td>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | 
                    <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>