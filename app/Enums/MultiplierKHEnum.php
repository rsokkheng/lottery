<?php

namespace App\Enums;

// KHR MienNam / MienTrung multipliers
// Derived from getPrizeLevel() order_counts across all 14 KHR prize levels
enum MultiplierKHEnum
{
    public const ABCD    = 4;   // A+B+C+D: 1 prize position each = 4
    public const ROLL    = 23;  // 2D roll: sum of all order_counts across 14 levels
    public const ROLL_3D = 19;  // 3D roll: excludes the 4 pure-2D prize positions
    public const ROLL_4D = 12;  // 4D roll: only prizes with input_length >= 4
    public const ROLL2   = 7;   // Roll 2 (2D): 1/A + GiaiBay + GiaiSau(3) + GiaiNam + GiaiTu(1st)
}
