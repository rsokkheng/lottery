<?php

namespace App\Enums;

// KHR MienBac (Hanoi) multipliers
// Derived from getPrizeLevel() order_counts for KHR MienBac prize structure
enum MultiplierHNKHEnum
{
    // 2D individual channels
    public const A    = 4;   // GiaiBay (order_count=4)
    public const B    = 1;   // KH_B_2D (order_count=1)
    public const C    = 1;   // KH_C_2D (order_count=1)
    public const D    = 1;   // KH_D_2D (order_count=1)
    public const ABCD = 7;   // A(4)+B(1)+C(1)+D(1) combined

    // 3D individual channels
    public const A_3D    = 3;   // GiaiSau (order_count=3)
    public const B_3D    = 1;   // KH_B_3D (order_count=1)
    public const C_3D    = 1;   // KH_C_3D (order_count=1)
    public const D_3D    = 1;   // KH_D_3D (order_count=1)
    public const ABCD_3D = 6;   // A_3D(3)+B_3D(1)+C_3D(1)+D_3D(1) combined

    // Roll multipliers
    public const ROLL    = 32;  // 2D roll: sum of all order_counts across all MienBac levels
    public const ROLL_3D = 25;  // 3D roll
    public const ROLL_4D = 22;  // 4D roll
    public const ROLL2   = 28;  // Roll 2 (2D)
    public const ROLL2_3D  = 23;  // Roll 2 (3D)

}
