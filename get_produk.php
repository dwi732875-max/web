<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM produk ORDER BY nama_produk ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

if($num > 0) {
    $produk_arr = array();
    $produk_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Hitung margin keuntungan
        $margin = $harga_jual - $harga_beli;
        $persen_margin = ($margin / $harga_beli) * 100;
        
        $produk_item = array(
            "id" => $id,
            "kode_produk" => $kode_produk,
            "nama_produk" => $nama_produk,
            "kategori" => $kategori,
            "harga_beli" => (float)$harga_beli,
            "harga_jual" => (float)$harga_jual,
            "stok" => $stok,
            "satuan" => $satuan,
            "supplier" => $supplier,
            "tanggal_kadaluarsa" => $tanggal_kadaluarsa,
            "deskripsi" => $deskripsi,
            "margin" => round($margin, 2),
            "persen_margin" => round($persen_margin, 2)
        );

        array_push($produk_arr["data"], $produk_item);
    }

    http_response_code(200);
    echo json_encode($produk_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Data produk tidak ditemukan."));
}
?>