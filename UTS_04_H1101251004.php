<?php

const nama_saya   = 'Dimas';
const nama_teman1 = 'Apis';
const nama_teman2 = 'Atong';

abstract class ProdukKue
{
    protected $id;
    protected $nama;
    protected $hargaDasar;

    public function __construct(string $id, string $nama, float $hargaDasar)
    {
        if (trim($id) === '' || trim($nama) === '') {
            throw new InvalidArgumentException('ID dan nama tidak boleh kosong.');
        }
        if ($hargaDasar < 0) {
            throw new InvalidArgumentException('Harga dasar tidak boleh negatif.');
        }
        $this->id         = $id;
        $this->nama       = $nama;
        $this->hargaDasar = $hargaDasar;
    }

    public function getId(): string        { return $this->id; }
    public function getNama(): string      { return $this->nama; }
    public function getHargaDasar(): float { return $this->hargaDasar; }

    abstract public function hitungTotal(): float;
    abstract public function getJenis(): string;

    protected static function pastikanJumlahValid(int $jumlah, string $label): int
    {
        if ($jumlah < 1) {
            throw new InvalidArgumentException("Jumlah $label minimal 1.");
        }
        return $jumlah;
    }
}

class KueBasah extends ProdukKue
{
    private $kotak;

    public function __construct(string $id, string $nama, float $hargaDasar, int $kotak)
    {
        parent::__construct($id, $nama, $hargaDasar);
        $this->kotak = self::pastikanJumlahValid($kotak, 'kotak');
    }

    public function hitungTotal(): float { return $this->hargaDasar * $this->kotak; }
    public function getJenis(): string   { return 'Kue Basah'; }
}

class KueKering extends ProdukKue
{
    const AMBANG_DISKON = 3;
    const PERSEN_DISKON = 0.05;

    private $toples;

    public function __construct(string $id, string $nama, float $hargaDasar, int $toples)
    {
        parent::__construct($id, $nama, $hargaDasar);
        $this->toples = self::pastikanJumlahValid($toples, 'toples');
    }

    public function hitungTotal(): float
    {
        $total = $this->hargaDasar * $this->toples;
        if ($this->toples > self::AMBANG_DISKON) {
            $total -= $total * self::PERSEN_DISKON;
        }
        return round($total, 2);
    }

    public function getJenis(): string { return 'Kue Kering'; }
}

class Tart extends ProdukKue
{
    const BIAYA_PER_TINGKAT = 15000;

    private $tingkat;

    public function __construct(string $id, string $nama, float $hargaDasar, int $tingkat)
    {
        parent::__construct($id, $nama, $hargaDasar);
        $this->tingkat = self::pastikanJumlahValid($tingkat, 'tingkat');
    }

    public function hitungTotal(): float
    {
        return $this->hargaDasar + (self::BIAYA_PER_TINGKAT * $this->tingkat);
    }

    public function getJenis(): string { return 'Tart'; }
}

function rupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function garisTabel(array $lebar): string
{
    $garis = '+';
    foreach ($lebar as $w) {
        $garis .= str_repeat('-', $w + 2) . '+';
    }
    return $garis . "\n";
}

function barisTabel(array $sel, array $lebar, array $rataKanan = []): string
{
    $baris = '|';
    foreach ($sel as $i => $teks) {
        $arah   = in_array($i, $rataKanan, true) ? STR_PAD_LEFT : STR_PAD_RIGHT;
        $baris .= ' ' . str_pad($teks, $lebar[$i], ' ', $arah) . ' |';
    }
    return $baris . "\n";
}

function cetakTabel(array $daftarProduk): void
{
    $header = ['No', 'ID', 'Nama', 'Jenis', 'Harga Dasar', 'Total'];
    $rataKanan = [4, 5];

    $data = [];
    foreach ($daftarProduk as $i => $p) {
        $data[] = [
            (string) ($i + 1),
            $p->getId(),
            $p->getNama(),
            $p->getJenis(),
            rupiah($p->getHargaDasar()),
            rupiah($p->hitungTotal()),
        ];
    }

    $lebar = array_map('strlen', $header);
    foreach ($data as $baris) {
        foreach ($baris as $i => $teks) {
            $lebar[$i] = max($lebar[$i], strlen($teks));
        }
    }

    echo "DAFTAR PRODUK KUE\n";
    echo garisTabel($lebar);
    echo barisTabel($header, $lebar, $rataKanan);
    echo garisTabel($lebar);
    foreach ($data as $baris) {
        echo barisTabel($baris, $lebar, $rataKanan);
    }
    echo garisTabel($lebar);
}

$daftarProduk = [
    new KueBasah('KB-001', 'Bolu Kukus ' . nama_saya,  35000, 2),
    new KueKering('KK-001', 'Nastar ' . nama_teman1,    60000, 5),
    new Tart('TT-001', 'Tart Coklat ' . nama_teman2,   120000, 3),
    new KueBasah('KB-002', 'Lapis Legit',               85000, 1),
    new KueKering('KK-002', 'Kastengel',                55000, 2),
];

cetakTabel($daftarProduk);