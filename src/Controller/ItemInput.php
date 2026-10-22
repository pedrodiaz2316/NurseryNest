<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Core\Http;

/**
 * Validates the inventory item form.
 */
final class ItemInput
{
    /** @return array{0: array<string, mixed>, 1: array<string, string>} */
    public static function fromPost(): array
    {
        $errors = [];
        $data = [
            'name'          => Http::input('name'),
            'category'      => Http::input('category'),
            'genus'         => self::nullable(Http::input('genus')),
            'species'       => self::nullable(Http::input('species')),
            'description'   => self::nullable(Http::input('description')),
            'quantity'      => Http::input('quantity', '0'),
            'date_received' => self::nullable(Http::input('date_received')),
            'florida_only'  => isset($_POST['florida_only']) ? 1 : 0,
        ];

        if ($data['name'] === '' || mb_strlen($data['name']) > 150) {
            $errors['name'] = 'Enter a name of up to 150 characters.';
        }
        if ($data['category'] === '' || mb_strlen($data['category']) > 80) {
            $errors['category'] = 'Enter a category of up to 80 characters.';
        }
        if (!ctype_digit((string) $data['quantity'])) {
            $errors['quantity'] = 'Quantity must be a whole number, 0 or more.';
        } else {
            $data['quantity'] = (int) $data['quantity'];
        }
        if ($data['date_received'] !== null && !self::isDate($data['date_received'])) {
            $errors['date_received'] = 'Use a valid date.';
        }
        if (($data['genus'] === null) !== ($data['species'] === null)) {
            $errors['genus'] = 'Enter both genus and species for plants, or leave both empty for supplies.';
        }

        return [$data, $errors];
    }

    /** @param array<string, string> $errors */
    public static function price(string $raw, array &$errors): float
    {
        if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $raw) || (float) $raw <= 0) {
            $errors['price'] = 'Enter a price greater than 0, for example 19.99.';
            return 0.0;
        }

        return round((float) $raw, 2);
    }

    /**
     * Parses "name: value" lines into extra fields (INV-5).
     *
     * @return array<string, string>
     */
    public static function attributes(string $text): array
    {
        $attributes = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }
            [$name, $value] = array_map('trim', explode(':', $line, 2));
            if ($name !== '' && $value !== '') {
                $attributes[mb_substr($name, 0, 80)] = mb_substr($value, 0, 255);
            }
        }

        return $attributes;
    }

    /** @param list<array{attribute_name: string, attribute_value: string}> $rows */
    public static function attributesToText(array $rows): string
    {
        return implode("\n", array_map(fn ($r) => $r['attribute_name'] . ': ' . $r['attribute_value'], $rows));
    }

    private static function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private static function isDate(string $value): bool
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
