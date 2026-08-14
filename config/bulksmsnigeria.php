<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BulkSMSNigeria API Settings
    |--------------------------------------------------------------------------
    */

    'base_url'  => env('BULKSMSNIGERIA_BASE_URL', 'https://www.bulksmsnigeria.com/api/v2'),

    'token'     => env('BULKSMSNIGERIA_API_TOKEN'),

    // Max 11 characters, no spaces/special characters
    'sender_id' => env('BULKSMSNIGERIA_SENDER_ID', 'ChristEmbassyLekki'),

];
