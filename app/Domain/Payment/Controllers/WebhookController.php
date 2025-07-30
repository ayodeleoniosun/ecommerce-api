<?php

namespace App\Domain\Payment\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\Webhook\FlutterwaveWebhookDto;
use App\Domain\Payment\Requests\Webhook\FlutterwaveWebhookRequest;
use App\Infrastructure\Services\Payments\Flutterwave\Webhook;
use Exception;
use Illuminate\Http\JsonResponse;

class WebhookController
{
    use UtilitiesTrait;

    public function __construct(
        private readonly Webhook $webhook,
    ) {}

    public function flutterwave(FlutterwaveWebhookRequest $request): JsonResponse
    {
        $webhookDto = FlutterwaveWebhookDto::fromArray($request->validated());

        try {
            $this->webhook->execute($webhookDto);

            return ApiResponse::success('Flutterwave webhook processed');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
