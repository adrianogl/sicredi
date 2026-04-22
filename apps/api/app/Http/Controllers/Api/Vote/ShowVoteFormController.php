<?php

namespace App\Http\Controllers\Api\Vote;

use App\Enums\VoteOption;
use App\Http\Controllers\Controller;
use App\Models\VotingSession;
use App\ScreenMessages\ButtonItem;
use App\ScreenMessages\CallbackUrl;
use App\ScreenMessages\FieldItem;
use App\ScreenMessages\FieldType;
use App\ScreenMessages\FormMessage;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;

#[Group('UI (Anexo 1)', 'Mensagens JSON que o cliente mobile interpreta para montar telas')]
class ShowVoteFormController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    #[Endpoint(
        title: 'Tela de formulario de voto',
        description: 'Retorna uma mensagem UI do tipo FORMULARIO com campo de ID do associado e botoes Sim/Nao.',
    )]
    #[Response(status: 200, description: 'Mensagem FORMULARIO conforme Anexo 1 do PDF.')]
    #[Response(status: 404, description: 'Sessao nao encontrada.')]
    public function __invoke(VotingSession $session): array
    {
        $items = [
            new FieldItem(FieldType::Text, 'ID do associado', 'member_id'),
        ];

        $buttons = array_map(
            fn (VoteOption $option) => new ButtonItem(
                text: $option->value,
                url: CallbackUrl::to("/api/v1/sessions/{$session->id}/votes").'?option='.urlencode($option->value),
            ),
            VoteOption::cases(),
        );

        return (new FormMessage(
            title: "Votar na sessao #{$session->id}",
            items: $items,
            buttons: $buttons,
        ))->toArray();
    }
}
