<?php

namespace App\Enums;

enum HelperEnum: string
{
    case GiaiTamRow = "1";


    # Region of lottery
    case MienNamSlug = 'mien-nam';
    case MienTrungSlug = 'mien-trung';
    case MienBacDienToanSlug = 'mien-trung-dien-toan';
}