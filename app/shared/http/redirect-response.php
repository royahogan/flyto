<?php

namespace App\Shared\Http;

class RedirectResponse
{
    public static function to(string $path, array $query = [], int $statusCode = 302, ?string $fragment = null): void
    {
        $target = self::basePath() . $path;

        if ($query !== []) {
            $target .= '?' . http_build_query($query);
        }

        if ($fragment !== null && $fragment !== '') {
            $target .= '#' . ltrim($fragment, '#');
        }

        header('Location: ' . $target, true, $statusCode);
    }

    private static function basePath(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    }
}
