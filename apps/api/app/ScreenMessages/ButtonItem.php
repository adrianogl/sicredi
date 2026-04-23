<?php

namespace App\ScreenMessages;

class ButtonItem
{
    public function __construct(
        public readonly string $text,
        public readonly string $url,
        // Quando null, o cliente deve enviar POST (padrão do Anexo 1).
        // Quando 'GET', o cliente apenas navega — útil para botões de cancelar/voltar.
        public readonly ?string $method = null,
    ) {}

    /**
     * @return array{texto: string, url: string, metodo?: string}
     */
    public function toArray(): array
    {
        $data = [
            'texto' => $this->text,
            'url' => $this->url,
        ];

        if ($this->method !== null) {
            $data['metodo'] = $this->method;
        }

        return $data;
    }
}
