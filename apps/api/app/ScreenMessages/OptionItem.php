<?php

namespace App\ScreenMessages;

class OptionItem
{
    /**
     * @param  array<string, mixed>|null  $body
     */
    public function __construct(
        public readonly string $text,
        public readonly string $url,
        public readonly ?array $body = null,
    ) {}

    /**
     * @return array{texto: string, url: string, body?: array<string, mixed>}
     */
    public function toArray(): array
    {
        $data = [
            'texto' => $this->text,
            'url' => $this->url,
        ];

        if ($this->body !== null) {
            $data['body'] = $this->body;
        }

        return $data;
    }
}
