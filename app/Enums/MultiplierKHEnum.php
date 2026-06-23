<?php

namespace App\Enums;

// KHR MienNam / MienTrung multipliers
// Derived from getPrizeLevel() order_counts across all 14 KHR prize levels
enum MultiplierKHEnum
{
    // 2D individual channels (each has 1 prize position for MienNam/MienTrung)
    public const A    = 1;   // 1/A   (order_count=1)
    public const B    = 1;   // KH_B_2D (order_count=1)
    public const C    = 1;   // KH_C_2D (order_count=1)
    public const D    = 1;   // KH_D_2D (order_count=1)
    public const ABCD = 4;   // A+B+C+D combined

    // 3D individual channels
    public const A_3D    = 1;   // GiaiBay/1A 3D (order_count=1)
    public const B_3D    = 1;   // KH_B_3D (order_count=1)
    public const C_3D    = 1;   // KH_C_3D (order_count=1)
    public const D_3D    = 1;   // KH_D_3D (order_count=1)
    public const ABCD_3D = 4;   // A_3D+B_3D+C_3D+D_3D combined

    // Roll multipliers
    public const ROLL    = 23;  // 2D roll: sum of all order_counts
    public const ROLL_3D = 19;  // 3D roll
    public const ROLL_4D = 18;  // 4D roll
    public const ROLL2   = 19;  // Roll 2 (2D)
    public const ROLL2_3D = 17; // Roll 2 (3D)
    
}
