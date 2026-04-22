<?php

namespace App\ScreenMessages;

class FormMessage
{
    /**
     * @param  list<FieldItem>  $items
     * @param  list<ButtonItem>  $buttons
     */
    public function __construct(
        public readonly string $title,
        public readonly array $items,
        public readonly array $buttons,
    ) {}

    /**
     * @return array{tipo: string, titulo: string, itens: list<array<string, mixed>>, botoes: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'tipo' => MessageType::Form->value,
            'titulo' => $this->title,
            'itens' => array_map(fn (FieldItem $item) => $item->toArray(), $this->items),
            'botoes' => array_map(fn (ButtonItem $button) => $button->toArray(), $this->buttons),
        ];
    }
}
