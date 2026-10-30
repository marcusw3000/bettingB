<?php

namespace VanguardLTE\Services\Payments;

use Illuminate\Support\Facades\Http;
use VanguardLTE\User;

class PushinpayDriver implements PaymentDriverInterface
{
    public function __construct(private array $config)
    {
    }

    public function createInvoice(User $user, float $amount, array $meta = []): array
    {
        $token = (string) ($this->config['token'] ?? '');
        $baseUrl = rtrim((string) ($this->config['base_url'] ?? ''), '/');

        if (empty($this->config['enabled']) || !$token || !$baseUrl) {
            throw new \RuntimeException('PushinPay não está configurado.');
        }

        $valueInCents = (int) round($amount * 100);
        if ($valueInCents < 50) {
            throw new \RuntimeException('O valor mínimo para PIX é R$ 0,50.');
        }

        $response = Http::acceptJson()
            ->withToken($token)
            ->timeout(15)
            ->post($baseUrl . '/pix/cashIn', [
                'value' => $valueInCents,
                'webhook_url' => $meta['webhook_url'] ?? null,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Não foi possível gerar o PIX. Tente novamente em instantes.');
        }

        $data = $response->json();
        if (empty($data['id']) || empty($data['qr_code'])) {
            throw new \RuntimeException('A PushinPay retornou uma cobrança inválida.');
        }

        return [
            'external_id' => (string) $data['id'],
            // PushinPay returns a QR code instead of a hosted checkout URL.
            'payment_url' => null,
            'meta' => [
                'qr_code' => (string) $data['qr_code'],
                'qr_code_base64' => (string) ($data['qr_code_base64'] ?? ''),
                'value_cents' => (int) ($data['value'] ?? $valueInCents),
            ],
        ];
    }
}
