<?php
// includes/functions.php

function formatRupiah($angka) {
    return number_format($angka, 0, ',', '.');
}

function tanggalIndo($tanggal) {
    return date('d-m-Y', strtotime($tanggal));
}

