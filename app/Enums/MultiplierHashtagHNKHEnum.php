<?php

namespace App\Enums;

// KHR MienBac (Hanoi) Roll Parlay (hashtag #) multipliers
// Derived from ROLL=32: one = 32×2, two = 32×3, three = 32×12
enum MultiplierHashtagHNKHEnum
{
    const one   = 64;
    const two   = 96;
    const three = 384;
}
