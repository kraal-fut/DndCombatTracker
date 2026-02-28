<?php

declare(strict_types=1);

namespace App\Assessment\Q2;

final class NestedSortService
{
    /**
     * Sort a nested array by one or more keys at any depth, recursively.
     *
     * @param array<int|string, mixed> $data
     * @param string[]                 $keys
     * @return array<int|string, mixed>
     */
    public function sort(array $data, array $keys): array
    {
        $data = $this->sortChildrenRecursively($data, $keys);

        if ($this->isSequentialArray($data) && $this->containsMatchingKeys($data, $keys)) {
            usort($data, $this->buildComparator($keys));
        }

        return $data;
    }

    /**
     * @param array<int|string, mixed> $data
     * @param string[]                 $keys
     * @return array<int|string, mixed>
     */
    private function sortChildrenRecursively(array $data, array $keys): array
    {
        foreach ($data as $index => $value) {
            if (is_array($value)) {
                $data[$index] = $this->sort($value, $keys);
            }
        }

        return $data;
    }

    /**
     * @param string[] $keys
     * @return callable(mixed, mixed): int
     */
    private function buildComparator(array $keys): callable
    {
        return function (mixed $a, mixed $b) use ($keys): int {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $a) || !array_key_exists($key, $b)) {
                    continue;
                }

                $comparison = $this->compareValues($a[$key], $b[$key]);

                if ($comparison !== 0) {
                    return $comparison;
                }
            }

            return 0;
        };
    }

    private function compareValues(mixed $a, mixed $b): int
    {
        if (is_string($a) && is_string($b)) {
            return strcasecmp($a, $b);
        }

        return $a <=> $b;
    }

    /**
     * @param array<int|string, mixed> $data
     */
    private function isSequentialArray(array $data): bool
    {
        return array_is_list($data);
    }

    /**
     * @param array<int|string, mixed> $data
     * @param string[]                 $keys
     */
    private function containsMatchingKeys(array $data, array $keys): bool
    {
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            foreach ($keys as $key) {
                if (array_key_exists($key, $item)) {
                    return true;
                }
            }
        }

        return false;
    }
}
