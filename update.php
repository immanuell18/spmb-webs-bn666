<?php
$p = App\Models\Pendaftar::first();
$p->tgl_verifikasi_adm = now()->subMinutes(120);
$p->tgl_verifikasi_payment = now()->subMinutes(60);
$p->tgl_pengumuman = now();
$p->save();

$u = $p->user;
$u->created_at = now()->subMinutes(180);
$u->save();

$p->created_at = now()->subMinutes(180);
$p->tanggal_daftar = now()->subMinutes(180);
$p->save();
echo "Updated to WIB";
