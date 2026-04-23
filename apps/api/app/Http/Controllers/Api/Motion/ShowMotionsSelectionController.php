<?php

namespace App\Http\Controllers\Api\Motion;

use App\Http\Controllers\Controller;
use App\Models\Motion;
use App\Repositories\Contracts\MotionRepositoryInterface;
use App\ScreenMessages\CallbackUrl;
use App\ScreenMessages\OptionItem;
use App\ScreenMessages\SelectionMessage;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;

#[Group('UI (Anexo 1)', 'Mensagens JSON que o cliente mobile interpreta para montar telas')]
class ShowMotionsSelectionController extends Controller
{
    private const MOTION_SELECTION_LIMIT = 50;

    public function __construct(
        private readonly MotionRepositoryInterface $motionRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    #[Endpoint(
        title: 'Tela de selecao de pauta',
        description: 'Retorna uma mensagem UI do tipo SELECAO com as pautas disponiveis para abrir sessao.',
    )]
    #[Response(status: 200, description: 'Mensagem SELECAO conforme Anexo 1 do PDF.')]
    public function __invoke(): array
    {
        $items = $this->motionRepository->latest(self::MOTION_SELECTION_LIMIT)
            ->map(fn (Motion $motion) => new OptionItem(
                text: $motion->title,
                url: CallbackUrl::to("/api/v1/motions/{$motion->id}/sessions"),
                body: [],
            ))
            ->all();

        return (new SelectionMessage(
            title: 'Selecione uma pauta para abrir uma sessão de votação',
            items: $items,
        ))->toArray();
    }
}
