<?php

namespace App\ScreenMessages;

class ButtonItem
{
    public function __construct(
        public readonly string $text,
        public readonly string $url,
    ) {}

    /**
     * @return array{texto: string, url: string}
     */
    public function toArray(): array
    {
        return [
            'texto' => $this->text,
            'url' => $this->url,
        ];
    }
}
