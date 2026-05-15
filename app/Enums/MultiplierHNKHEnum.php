<?php

namespace App\Enums;

// KHR MienBac (Hanoi) multipliers
// Derived from getPrizeLevel() order_counts for KHR MienBac prize structure
enum MultiplierHNKHEnum
{
    public const A       = 4;   // GiaiBay: 4 × 2D positions
    public const ABCD    = 7;   // GiaiBay(4) + KH_B_2D(1) + KH_C_2D(1) + KH_D_2D(1)
    public const ABCD_3D = 6;   // GiaiSau(3) + KH_B_3D(1) + KH_C_3D(1) + KH_D_3D(1)
    public const ROLL    = 32;  // 2D roll: sum of all order_counts across all MienBac levels
    public const ROLL_3D = 25;  // 3D roll: 32 minus 7 pure-2D positions
    public const ROLL_4D = 19;  // 4D roll: only prizes with input_length >= 4
    public const ROLL2   = 14;  // Roll 2 (2D): GiaiBay(4) + GiaiSau(3) + GiaiNam(6) + GiaiTu(1st)
}
