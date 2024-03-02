<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Event;

final class ModifySoundcloudOutputEvent
{
    public function __construct(
        protected string $output = ''
    ) {
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function setOutput(string $output): void
    {
        $this->output = $output;
    }
}
