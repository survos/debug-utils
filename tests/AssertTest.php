<?php

declare(strict_types=1);

namespace Survos\DebugUtils\Tests;

use PHPUnit\Framework\TestCase;
use Survos\DebugUtils\Assert;

final class AssertTest extends TestCase
{
    // --- keyExists -------------------------------------------------------

    public function testKeyExistsPassesForPresentKey(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::keyExists('name', ['name' => 'Tac', 'email' => 'x@y.z']);
    }

    public function testKeyExistsAcceptsIntegerKeys(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::keyExists(0, ['a', 'b']);
    }

    public function testKeyExistsAcceptsObjects(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::keyExists('foo', (object) ['foo' => 1]);
    }

    public function testKeyExistsThrowsAndListsAvailableKeys(): void
    {
        try {
            Assert::keyExists('emial', ['name' => 'Tac', 'email' => 'x@y.z']);
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString('Missing key [emial]', $e->getMessage());
            // The whole point: the typo is obvious next to the real key.
            self::assertStringContainsString('email', $e->getMessage());
            self::assertStringContainsString('name', $e->getMessage());
        }
    }

    public function testKeyExistsListsKeysSortedForStableOutput(): void
    {
        try {
            Assert::keyExists('zzz', ['charlie' => 1, 'alpha' => 2, 'bravo' => 3]);
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString("alpha\nbravo\ncharlie", $e->getMessage());
        }
    }

    public function testKeyExistsAppendsCustomMessage(): void
    {
        try {
            Assert::keyExists('x', ['y' => 1], 'check the CSV header');
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString('check the CSV header', $e->getMessage());
        }
    }

    // --- inArray ---------------------------------------------------------

    public function testInArrayPassesForAllowedValue(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::inArray('green', ['red', 'green', 'blue']);
    }

    public function testInArrayIsStrict(): void
    {
        // '1' (string) is not the same as 1 (int) under strict comparison.
        $this->expectException(\InvalidArgumentException::class);
        Assert::inArray('1', [1, 2, 3]);
    }

    public function testInArrayThrowsAndListsAllowedValues(): void
    {
        try {
            Assert::inArray('purple', ['red', 'green', 'blue']);
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString("Unexpected value 'purple'", $e->getMessage());
            self::assertStringContainsString("'red'", $e->getMessage());
            self::assertStringContainsString("'green'", $e->getMessage());
            self::assertStringContainsString("'blue'", $e->getMessage());
        }
    }

    public function testInArrayAppendsCustomMessage(): void
    {
        try {
            Assert::inArray('x', ['a'], 'unknown status');
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString('unknown status', $e->getMessage());
        }
    }

    // --- keysExist -------------------------------------------------------

    public function testKeysExistPassesWhenAllPresent(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::keysExist(['name', 'email'], ['name' => 'Tac', 'email' => 'x@y.z', 'extra' => 1]);
    }

    public function testKeysExistAcceptsObjects(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::keysExist(['foo'], (object) ['foo' => 1, 'bar' => 2]);
    }

    public function testKeysExistThrowsListingMissingAndAvailable(): void
    {
        try {
            Assert::keysExist(['name', 'email', 'phone'], ['name' => 'Tac']);
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString('email', $e->getMessage());
            self::assertStringContainsString('phone', $e->getMessage());
            // Available keys are shown so you can spot what you actually have.
            self::assertStringContainsString('name', $e->getMessage());
        }
    }

    public function testKeysExistAppendsCustomMessage(): void
    {
        try {
            Assert::keysExist(['a', 'b'], ['a' => 1], 'API payload incomplete');
            self::fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString('API payload incomplete', $e->getMessage());
        }
    }
}
