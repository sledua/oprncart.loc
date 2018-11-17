<?php
$_['opencart_version'] = array(
    "3.0.0.0",
    "3.0.1.1",
    "3.0.1.2",
    "3.0.2.0");
$_['main_sheet'] = array(
    'name' => 'Categories',
    'event_export' => array(
        'extension/d_export_import_module/product/export'
        ),
    'event_inport' => array(
        'extension/d_export_import_module/product/import'
        ),
    'tables' => array(
        array(
            'name' => 'p2c',
            'full_name' => 'product_to_category',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            )  
        ),
    'columns' => array( 
        array(
            'column' => 'manufacturer_id',
            'table' => 'p',
            'name' => 'Manufacturer ID',
            'filter' => 1
            ),
        array(
            'column' => 'category_id',
            'table' => 'p2c',
            'concat' => 1,
            'name' => 'Categories',
            'filter' => 1
            ),
        array(
            'column' => 'store_id',
            'table' => 'p2s',
            'concat' => 1,
            'name' => 'Stores',
            'filter' => 1
						)
		)
);

$_['sheets'] = array();
