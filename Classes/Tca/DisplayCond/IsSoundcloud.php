<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tca\DisplayCond;

class IsSoundcloud
{
    /**
     * @param array<string,mixed> $parameters
     */
    public function match(array $parameters): bool
    {
        $record = $parameters['record'] ?? [];
        if (!is_array($record)) {
            return false;
        }

        return (!empty($record['soundcloud_html'] ?? ''));
    }
}
