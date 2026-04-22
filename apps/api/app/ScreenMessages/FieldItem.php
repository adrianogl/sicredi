<?php

namespace App\ScreenMessages;

class FieldItem
{
    public function __construct(
        public readonly FieldType $type,
        public readonly string $label,
        public readonly string $id,
    ) {}

    /**
     * @return array{tipo: string, label: string, id: string}
     */
    public function toArray(): array
    {
        return [
            'tipo' => $this->type->value,
            'label' => $this->label,
            'id' => $this->id,
        ];
    }
}
