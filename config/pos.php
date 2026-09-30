<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Smart Mini POS Receipt Printer
    |--------------------------------------------------------------------------
    |
    | Settings for printing bet receipts on an 80mm thermal printer
    | (Sunmi / iMin / generic Android POS via Chrome print).
    |
    | paper_width  : roll width in mm (80mm paper).
    | print_width  : printable area in mm. 80mm printers only print ~72mm,
    |                content wider than this gets cut off on the right side.
    |
    */

    'paper_width' => (int) env('POS_PAPER_WIDTH', 80),

    'print_width' => (int) env('POS_PRINT_WIDTH', 72),

    'font_size' => (int) env('POS_FONT_SIZE', 14),

    'logo' => env('POS_RECEIPT_LOGO', 'images/logo-2888.png'),

    // Days a receipt stays valid, shown as the expire date on the receipt.
    'validity_days' => (int) env('POS_RECEIPT_VALIDITY_DAYS', 3),

    // Open the print dialog automatically when the receipt page loads.
    'auto_print' => (bool) env('POS_AUTO_PRINT', true),

    /*
    |--------------------------------------------------------------------------
    | POS Station (print from phone to Mini POS)
    |--------------------------------------------------------------------------
    |
    | Open /pos/station on the Mini POS, logged in with the same account as
    | the phone. Receipts sent with "Send to Mini POS" print there.
    |
    */

    // How often the station checks for new receipts (seconds).
    'station_poll_seconds' => (int) env('POS_STATION_POLL_SECONDS', 3),

    // Jobs older than this (minutes) are skipped, so a station that was
    // offline does not suddenly print a pile of old receipts.
    'station_job_ttl' => (int) env('POS_STATION_JOB_TTL', 10),

];
