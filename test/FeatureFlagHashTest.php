<?php

namespace PostHog\Test;

use PHPUnit\Framework\TestCase;
use PostHog\FeatureFlag;
use ReflectionMethod;

class FeatureFlagHashTest extends TestCase
{
    /**
     * @dataProvider legacyHashInputs
     */
    public function testPreservesLegacyHash($key, $distinctId, $salt, $expectedHex): void
    {
        $hash = new ReflectionMethod(FeatureFlag::class, "hash");
        self::assertSame(
            base_convert($expectedHex, 16, 10) / \PostHog\LONG_SCALE,
            $hash->invoke(null, $key, $distinctId, $salt)
        );
    }

    public static function legacyHashInputs(): array
    {
        // Expected SHA-1 prefixes from the original utf8_encode() implementation.
        return [
            "ascii" => ["flag", "user-123", "", "93bfb32dcab8a8b"],
            "utf8" => ["drapeau-é", "用户", "", "674be05b758fd88"],
            "variant" => ["flag", "café", "variant", "05cc5e20e903a37"],
            "latin1" => ["flag", "caf\xe9", "", "8f9c3d27a7e59b8"],
            "all-bytes" => ["flag", implode("", array_map("chr", range(0, 255))), "variant", "63725621cd28f08"],
        ];
    }
}
