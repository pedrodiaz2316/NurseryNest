<?php

declare(strict_types=1);

namespace NurseryNest\Tests;

use NurseryNest\Controller\ItemInput;
use NurseryNest\Controller\ThemeController;
use PHPUnit\Framework\TestCase;

final class ItemInputTest extends TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
    }

    public function testValidPlant(): void
    {
        $_POST = [
            'name' => 'Firebush', 'category' => 'Natives', 'genus' => 'Hamelia', 'species' => 'patens',
            'quantity' => '12', 'date_received' => '2026-09-30', 'florida_only' => '1',
        ];
        [$data, $errors] = ItemInput::fromPost();

        $this->assertSame([], $errors);
        $this->assertSame(12, $data['quantity']);
        $this->assertSame(1, $data['florida_only']);
    }

    public function testRejectsBadQuantityDateAndHalfBotanicalName(): void
    {
        $_POST = ['name' => 'X', 'category' => 'Y', 'genus' => 'Citrus', 'quantity' => '-3', 'date_received' => '2026-02-30'];
        [, $errors] = ItemInput::fromPost();

        $this->assertArrayHasKey('quantity', $errors);
        $this->assertArrayHasKey('date_received', $errors);
        $this->assertArrayHasKey('genus', $errors);
    }

    public function testPrice(): void
    {
        $errors = [];
        $this->assertSame(19.99, ItemInput::price('19.99', $errors));
        $this->assertSame([], $errors);

        foreach (['0', '-1', 'abc', '1.999'] as $bad) {
            $errors = [];
            ItemInput::price($bad, $errors);
            $this->assertArrayHasKey('price', $errors, $bad);
        }
    }

    public function testAttributesRoundTrip(): void
    {
        $parsed = ItemInput::attributes("Light: Full sun\nnot a field\nHeight:  6 ft \n: empty");

        $this->assertSame(['Light' => 'Full sun', 'Height' => '6 ft'], $parsed);
    }

    public function testThemeColorsOnlyAllowHex(): void
    {
        $this->assertSame('#2f6b3b', ThemeController::color('#2F6B3B', '#000000'));
        $this->assertSame('#000000', ThemeController::color('red;}body{display:none', '#000000'));
        $this->assertSame('#000000', ThemeController::color(null, '#000000'));
    }
}
