<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Envolve um resultado consolidado no formato padrao da API.
 *
 * O recurso espera um array no shape:
 *   array{motion_id: int, yes_count: int, no_count: int}
 *
 * O campo `total` e computado aqui (soma yes + no), nao precisa ser passado.
 */
class ResultResource extends JsonResource
{
    /**
     * @return array{motion_id: int, yes_count: int, no_count: int, total: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'motion_id' => $this['motion_id'],
            'yes_count' => $this['yes_count'],
            'no_count' => $this['no_count'],
            'total' => $this['yes_count'] + $this['no_count'],
        ];
    }
}
