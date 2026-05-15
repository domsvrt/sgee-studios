<?php

declare(strict_types=1);

namespace App\Services;

class DiceBearService
{
    private const BASE_URL = 'https://api.dicebear.com/9.x';
    private const IDENTICON_DEFAULT_OPTIONS = [
        'backgroundType' => 'gradientLinear',
        'backgroundColor' => 'fde68a,fca5a5,c4b5fd',
        'rowColor' => '7c3aed,0ea5e9,f43f5e',
        'radius' => '50',
        'scale' => '90',
    ];

    public function avatarUrl(string $seed, string $style = 'initials', array $options = [], string $format = 'svg'): string
    {
        $cleanSeed = trim($seed);
        if ($cleanSeed === '') {
            $cleanSeed = 'user';
        }

        $style = trim($style);
        if ($style === '') {
            $style = 'initials';
        }

        $query = $this->buildQuery($options);
        $url = self::BASE_URL . '/' . rawurlencode($style) . '/' . rawurlencode($cleanSeed) . '.' . rawurlencode($format);
        return $query === '' ? $url : $url . '?' . $query;
    }

    public function defaultUserAvatarUrl(string $name = '', string $email = ''): string
    {
        $seed = trim($email) !== '' ? strtolower(trim($email)) : trim($name);
        if ($seed === '') {
            $seed = 'user';
        }

        return $this->avatarUrl($seed, 'identicon', self::IDENTICON_DEFAULT_OPTIONS);
    }

    public function defaultIdenticonOptions(): array
    {
        return self::IDENTICON_DEFAULT_OPTIONS;
    }

    private function buildQuery(array $options): string
    {
        $normalized = [];
        foreach ($options as $key => $value) {
            if (!is_string($key) || $key === '' || $value === null) {
                continue;
            }
            $normalized[$key] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }

        return http_build_query($normalized, '', '&', PHP_QUERY_RFC3986);
    }
}
