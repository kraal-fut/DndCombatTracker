<?php

declare(strict_types=1);

namespace App\Assessment\Q1;

use Illuminate\Support\Str;

final class GuestDataPrinterService
{
    private const int INDENT_SPACES = 4;

    public function print(array $data, int $depth = 0): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->printLabel($key, $depth);
                $this->print($value, $depth + 1);
                continue;
            }

            $this->printKeyValue($key, $value, $depth);
        }
    }

    private function printLabel(string|int $key, int $depth): void
    {
        if (is_string($key)) {
            echo str_repeat(' ', $depth * self::INDENT_SPACES) . Str::headline((string) $key) . ':' . PHP_EOL;
        }
    }

    private function printKeyValue(string|int $key, mixed $value, int $depth): void
    {
        $indent = str_repeat(' ', $depth * self::INDENT_SPACES);
        $label = is_string($key) ? Str::headline((string) $key) . ': ' : '';
        $formatted = $this->formatValue($value);

        echo $indent . $label . $formatted . PHP_EOL;
    }

    private function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return '(null)';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }
}
