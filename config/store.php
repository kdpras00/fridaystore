<?php

return [
    'name' => env('STORE_NAME', env('APP_NAME', 'Friday Store')),
    'tagline' => env('STORE_TAGLINE', 'POS Toko'),
    'address' => env('STORE_ADDRESS', 'Jl. Contoh No. 1, Kota'),
    'phone' => env('STORE_PHONE', '08123456789'),
    'receipt_note' => env('STORE_RECEIPT_NOTE', 'Barang yang sudah dibeli tidak dapat ditukar.'),
    'brand_text' => env('STORE_BRAND_TEXT', 'FRIDAY'),
];
