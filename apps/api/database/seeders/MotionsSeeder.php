<?php

namespace Database\Seeders;

use App\Models\Motion;
use Illuminate\Database\Seeder;

class MotionsSeeder extends Seeder
{
    public function run(): void
    {
        $motions = [
            [
                'title' => 'Aprovação do balanço anual de 2025',
                'description' => 'Análise e votação do relatório contábil do exercício de 2025.',
            ],
            [
                'title' => 'Reajuste da mensalidade dos associados',
                'description' => 'Proposta de correção inflacionária de 4,5% sobre a mensalidade vigente.',
            ],
            [
                'title' => 'Eleição do novo conselho fiscal',
                'description' => 'Votação da chapa única indicada para o conselho fiscal do próximo biênio.',
            ],
            [
                'title' => 'Doação para a campanha de solidariedade',
                'description' => 'Deliberação sobre repasse de 2% do resultado líquido para a ação social regional.',
            ],
            [
                'title' => 'Mudança do horário de atendimento da agência central',
                'description' => 'Estender o horário de atendimento até as 18h nos dias úteis, a partir de junho.',
            ],
        ];

        foreach ($motions as $motion) {
            Motion::create($motion);
        }
    }
}
