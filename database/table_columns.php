<?php

$table_columns_mapping = [

    'users' => [
        'first_name',
        'last_name',
        'email',
        'password',
        'created_at',
        'updated_at'
    ],

    'products' => [
        'img',
        'product_name',
        'description',
        'created_by',
        'created_at',
        'updated_at'
    ],

    'supplier' => [
        'id',
        'supplier_name',
        'supplier_location',
        'email',
        'created_by',
        'created_at',
        'updated_at'
    ],

    'productsupplier' => [
        'id',
        'supplier',
        'product',
        'quantity_order',
        'quantity_received',
        'quantity_remaining',
        'stats',
        'created_by',
        'created_at',
        'updated_at'
    ],

    'stock' => [
        'id',
        'product_name',
        'created_by',
        'quantity',
        'created_at',
        'updated_at'
    ]

];

?>