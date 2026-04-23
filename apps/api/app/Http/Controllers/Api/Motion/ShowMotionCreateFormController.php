<?php

namespace App\Http\Controllers\Api\Motion;

use App\Http\Controllers\Controller;
use App\ScreenMessages\ButtonItem;
use App\ScreenMessages\CallbackUrl;
use App\ScreenMessages\FieldItem;
use App\ScreenMessages\FieldType;
use App\ScreenMessages\FormMessage;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;

#[Group('UI (Anexo 1)', 'Mensagens JSON que o cliente mobile interpreta para montar telas')]
class ShowMotionCreateFormController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    #[Endpoint(
        title: 'Tela de cadastro de pauta',
        description: 'Retorna uma mensagem UI do tipo FORMULARIO com os campos necessários para cadastrar uma nova pauta.',
    )]
    #[Response(status: 200, description: 'Mensagem FORMULARIO conforme Anexo 1 do PDF.')]
    public function __invoke(): array
    {
        $items = [
            new FieldItem(FieldType::Text, 'Título', 'title'),
            new FieldItem(FieldType::Text, 'Descrição', 'description'),
        ];

        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        $buttons = [
            new ButtonItem(
                text: 'Cancelar',
                url: $frontendUrl.'/motions',
                method: 'GET',
            ),
            new ButtonItem(
                text: 'Cadastrar',
                url: CallbackUrl::to('/api/v1/motions'),
            ),
        ];

        return (new FormMessage(
            title: 'Cadastrar nova pauta',
            items: $items,
            buttons: $buttons,
        ))->toArray();
    }
}
