<?php

namespace App\ScreenMessages;

class SelectionMessage
{
    /**
     * @param  list<OptionItem>  $items
     */
    public function __construct(
        public readonly string $title,
        public readonly array $items,
    ) {}

    /**
     * @return array{tipo: string, titulo: string, itens: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'tipo' => MessageType::Selection->value,
            'titulo' => $this->title,
            'itens' => array_map(fn (OptionItem $item) => $item->toArray(), $this->items),
        ];
    }
}
