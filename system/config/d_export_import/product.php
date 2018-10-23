<?php
$_['opencart_version'] = array(
    "2.0.0.0",
    "2.0.1.0",
    "2.0.1.1",
    "2.0.2.0",
    "2.0.3.1",
    "2.1.0.1",
    "2.1.0.2",
    "2.2.0.0",
    "2.3.0.0",
    "2.3.0.1",
    "2.3.0.2");
$_['main_sheet'] = array(
    'name' => 'Products',
    'event_export' => array(
        'extension/d_export_import_module/product/export'
        ),
    'event_inport' => array(
        'extension/d_export_import_module/product/import'
        ),
    'table' =>  array(
        'name' => 'p',
        'full_name' => 'product',
        'key' => 'product_id'
        ),
    'tables' => array(
        array(
            'name' => 'pd',
            'full_name' => 'product_description',
            'key' => 'product_id',
            'join' => 'INNER',
            'multi_language' => 1
            ),
        array(
            'name' => 'p2c',
            'full_name' => 'product_to_category',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            ),
        array(
            'name' => 'p2s',
            'full_name' => 'product_to_store',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            ),
        array(
            'name' => 'p2d',
            'full_name' => 'product_to_download',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            ),
        array(
            'name' => 'pf',
            'full_name' => 'product_filter',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            ),
        array(
            'name' => 'pr',
            'full_name' => 'product_related',
            'join' => 'LEFT',
            'key' => 'product_id',
            'concat' => 1
            ),
        array(
            'name' => 'ua',
            'full_name' => 'url_alias',
            'key' => 'query',
            'related_key' => 'query',
            'prefix' => 'product_id=',
            'clear' => 1,
            'not_empty' => 1,
            'join' => 'LEFT'
            )
        ),
    'columns' => array(
        array(
            'column' => 'product_id',
            'table' => 'p',
            'name' => 'Product ID',
            'filter' => 1
            ),
        array(
            'column' => 'name',
            'table' => 'pd',
            'name' => 'Name',
            'filter' => 1
            ),
        array(
            'column' => 'model',
            'table' => 'p',
            'name' => 'Model',
            'filter' => 1
            ),
        array(
            'column' => 'sku',
            'table' => 'p',
            'name' => 'SKU',
            'filter' => 1
            ),
        array(
            'column' => 'description',
            'table' => 'pd',
            'name' => 'Description',
            'filter' => 1
            ),
        array(
            'column' => 'meta_title',
            'table' => 'pd',
            'name' => 'Meta Title',
            'filter' => 1
            ),
        array(
            'column' => 'meta_keyword',
            'table' => 'pd',
            'name' => 'Meta Keyword',
            'filter' => 1
            ),
        array(
            'column' => 'meta_description',
            'table' => 'pd',
            'name' => 'Meta Description',
            'filter' => 1
            ),
        array(
            'column' => 'tag',
            'table' => 'pd',
            'name' => 'Tags',
            'filter' => 1
            ),
        array(
            'column' => 'upc',
            'table' => 'p',
            'name' => 'UPC',
            'filter' => 1
            ),
        array(
            'column' => 'ean',
            'table' => 'p',
            'name' => 'EAN',
            'filter' => 1
            ),
        array(
            'column' => 'jan',
            'table' => 'p',
            'name' => 'JAN',
            'filter' => 1
            ),
        array(
            'column' => 'isbn',
            'table' => 'p',
            'name' => 'ISBN',
            'filter' => 1
            ),
        array(
            'column' => 'mpn',
            'table' => 'p',
            'name' => 'MPN',
            'filter' => 1
            ),
        array(
            'column' => 'price',
            'table' => 'p',
            'name' => 'Price',
            'filter' => 1
            ),
        array(
            'column' => 'location',
            'table' => 'p',
            'name' => 'Location'
            ),
        array(
            'column' => 'status',
            'table' => 'p',
            'name' => 'Status',
            'filter' => 1
            ),
        array(
            'column' => 'tax_class_id',
            'table' => 'p',
            'name' => 'Tax Class Id'
            ),
        array(
            'column' => 'quantity',
            'table' => 'p',
            'name' => 'Quantity',
            'filter' => 1
            ),
        array(
            'column' => 'minimum',
            'table' => 'p',
            'name' => 'Minimum Quantity'
            ),
        array(
            'column' => 'image',
            'table' => 'p',
            'name' => 'Image',
            'filter' => 1
            ),
        array(
            'column' => 'subtract',
            'table' => 'p',
            'name' => 'Subtrack Stock'
            ),
        array(
            'column' => 'stock_status_id',
            'table' => 'p',
            'name' => 'Out Of Stock Status',
            'filter' => 1
            ),
        array(
            'column' => 'shipping',
            'table' => 'p',
            'name' => 'Requires Shipping',
            'filter' => 1
            ),
        array(
            'column' => 'date_available',
            'table' => 'p',
            'name' => 'Date Available'
            ),
        array(
            'column' => 'viewed',
            'table' => 'p',
            'name' => 'Viewed'
            ),
        array(
            'column' => 'length',
            'table' => 'p',
            'name' => 'Length'
            ),
        array(
            'column' => 'width',
            'table' => 'p',
            'name' => 'Width'
            ),
        array(
            'column' => 'height',
            'table' => 'p',
            'name' => 'Height'
            ),
        array(
            'column' => 'length_class_id',
            'table' => 'p',
            'name' => 'Laength Class ID'
            ),
        array(
            'column' => 'weight',
            'table' => 'p',
            'name' => 'Weight'
            ),
        array(
            'column' => 'weight_class_id',
            'table' => 'p',
            'name' => 'Weight Class ID'
            ),
        array(
            'column' => 'points',
            'table' => 'p',
            'name' => 'Points',
            'filter' => 1
            ),
        array(
            'column' => 'keyword',
            'table' => 'ua',
            'name' => 'SEO Keyword'
            ),
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
            ),
        array(
            'column' => 'download_id',
            'table' => 'p2d',
            'concat' => 1,
            'name' => 'Downloads',
            'filter' => 1
            ),
        array(
            'column' => 'related_id',
            'table' => 'pr',
            'concat' => 1,
            'name' => 'Related Products',
            'filter' => 1
            )
        )
);

$_['sheets'] = array();
