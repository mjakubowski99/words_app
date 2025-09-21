<?php

namespace User\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use Illuminate\Http\JsonResponse;
use Shared\Utils\ValueObjects\Language;
use OpenApi\Attributes as OAT;

class LanguageController
{
    #[OAT\Get(
        path: '/api/v2/languages',
        operationId: 'languages.get',
        description: 'Retrieve a list of available languages',
        summary: 'Get available languages',
        tags: [Tags::USER, Tags::V2, Tags::LANGUAGE],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of available languages',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(
                            property: 'data',
                            type: 'array',
                            items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'code', type: 'string', example: 'en'),
                                    new OAT\Property(property: 'flag', type: 'string', example: 'https://api.vocasmart.pl/flags/en.svg'),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OAT\Response(ref: '#/components/responses/server_error', response: 500),
        ]
    )]
    public function get(): JsonResponse
    {
        return new JsonResponse([
            'data' => array_map(fn(Language $lang) => [
                'code' => $lang->getValue(),
                'flag' => asset('assets/flags/' . strtolower($lang->getValue()) . '.svg'),
            ], Language::all()),
        ]);
    }
}