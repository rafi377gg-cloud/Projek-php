<?php
class User{
    protected $nama;
    protected $noHp;

    function __construct($nama,$noHp){
        $this->nama = $nama;
        $this->noHp = $noHp;
  }

    function getNama(){
        return $this->nama;
 }

    function getStatus(){
        return "User";
 }
}
class Pelanggan extends User{
    private $poin = 0;

    function getStatus(){
        return "Pelanggan Premium";
  }

    function tambahPoin($total){
        $this->poin += floor($total / 10000);
 }

    function getPoin(){
        return $this->poin;
  }
}

    class Layanan{
        public $nama;
        public $tarif;

        function __construct($nama,$tarif){
        $this->nama = $nama;
        $this->tarif = $tarif;

        }
}
class Voucher{
    public $diskon;

    function __construct($diskon){
        $this->diskon = $diskon;
  }

  }

class Pembayaran{
    function getMetode(){
        return "Pembayaran";
  }
}
class EWallet extends Pembayaran{
    function getMetode(){
        return "E-Wallet";
  }
}
class TransferBank extends Pembayaran{
    function getMetode(){
        return "Transfer Bank";
 }
}
class Cash extends Pembayaran{
    function getMetode(){
        return "Cash";
  }
}
class Transaksi{

    private static $totalTransaksi = 0;

    function __construct(){
        self::$totalTransaksi++;
 }

    static function getTotalTransaksi(){
        return self::$totalTransaksi;
 }
}

$hasil = "";

if(isset($_POST['hitung'])){

    $nama = $_POST['nama'];
    $hp = $_POST['hp'];
    $jarak = $_POST['jarak'];

    if(empty($nama)){
        $hasil = "Nama tidak boleh kosong!";
    }
    elseif(strlen($hp) < 10){
        $hasil = "Nomor HP minimal 10 digit!";
    }
    elseif($jarak <= 0){
        $hasil = "Jarak harus lebih dari 0!";
    }
    else{

        $pelanggan = new Pelanggan($nama,$hp);

        $tarif = [
            "GoRide Reguler" => 2500,
            "GoRide Prioritas" => 3000,
            "GoCar" => 4500,
            "GoCar XL" => 6000,
            "GoFood" => 2000
        ];

        $layanan = new Layanan(
            $_POST['layanan'],
            $tarif[$_POST['layanan']]
        );

        $subtotal = $jarak * $layanan->tarif;

        $diskonMember = ($subtotal > 50000)
            ? $subtotal * 0.05
            : 0;

        $voucherList = [
            "" => 0,
            "HEMAT10" => 10,
            "HEMAT20" => 20,
            "HEMAT30" => 30
        ];

        $voucher = new Voucher(
            $voucherList[$_POST['voucher']]
        );

        $diskonVoucher =
            $subtotal * $voucher->diskon / 100;

        if($_POST['bayar']=="E-Wallet"){
            $bayar = new EWallet();
            $admin = 1000;
        }
        elseif($_POST['bayar']=="Transfer Bank"){
            $bayar = new TransferBank();
            $admin = 2500;
        }
        else{
            $bayar = new Cash();
            $admin = 0;
        }
        $total = $subtotal
                - $diskonMember
                - $diskonVoucher
                + $admin;

        $pelanggan->tambahPoin($total);

        $trx = new Transaksi();

        $hasil = "
        <h3>Detail Transaksi</h3>

        Nama : ".$pelanggan->getNama()."<br>
        Status : ".$pelanggan->getStatus()."<br>
        Layanan : ".$layanan->nama."<br>
        Metode Pembayaran : ".$bayar->getMetode()."<br><br>

        Subtotal : Rp ".number_format($subtotal)."<br>
        Diskon Member : Rp ".number_format($diskonMember)."<br>
        Diskon Voucher : Rp ".number_format($diskonVoucher)."<br>
        Biaya Admin : Rp ".number_format($admin)."<br><hr>

        <b>Total Bayar : Rp ".number_format($total)."</b><br><br>

        Poin Reward : ".$pelanggan->getPoin()." poin<br>
        Total Transaksi : ".Transaksi::getTotalTransaksi();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ojek Online Premium</title>

<style>
body{
    font-family: Arial;
}
.container{
    width: 500px;
    margin: auto;
}
input,
select,
button{
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 10px;
}
button{
    background: purple;
    color: white;
    border: none;
}
.hasil{
    margin-top: 20px;
    padding: 10px;
    border: 1px solid black;
}

</style>
</head>
<body>

<div class="container">

<h2>Sistem Ojek Online Premium</h2>

<form method="POST" id="formOjek">

<input type="text"
name="nama"
placeholder="Masukkan Nama">

<input type="text"
name="hp"
placeholder="Masukkan Nomor HP">

<input type="number"
name="jarak"
placeholder="Jarak Tempuh (KM)">

<select name="layanan">
    <option>GoRide Reguler</option>
    <option>GoRide Prioritas</option>
    <option>GoCar</option>
    <option>GoCar XL</option>
    <option>GoFood</option>
</select>

<select name="voucher">
    <option value="">Tanpa Voucher</option>
    <option>HEMAT10</option>
    <option>HEMAT20</option>
    <option>HEMAT30</option>
</select>

<select name="bayar">
    <option>E-Wallet</option>
    <option>Transfer Bank</option>
    <option>Cash</option>
</select>

<button type="submit" name="hitung">
Hitung Transaksi
</button>

</form>
<div class="hasil">
<?= $hasil ?>
</div>
</div>

<script>
document.getElementById("formOjek")
.addEventListener("submit",function(e){

    let nama =
    document.querySelector("[name='nama']").value;

    let hp =
    document.querySelector("[name='hp']").value;

    let jarak =
    document.querySelector("[name='jarak']").value;

    if(nama==""){
        alert("Nama tidak boleh kosong!");
        e.preventDefault();
    }
    else if(hp.length < 10){
        alert("Nomor HP minimal 10 digit!");
        e.preventDefault();
    }
    else if(jarak <= 0){
        alert("Jarak harus lebih dari 0!");
        e.preventDefault();
    }

});
</script>
</body>
</html>