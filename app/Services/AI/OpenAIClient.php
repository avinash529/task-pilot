<?php

namespace App\Services\AI;

use App\Contracts\AI\AIClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIClient implements AIClientInterface
{
    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function generateText(string $prompt, array $options = []): string
    {
        $apiKey = (string) config('ai.api_key');
        $model = (string) ($options['model'] ?? config('ai.model'));

        if ($apiKey === '' || $model === '') {
            throw new RuntimeException('AI client is not configured. Set OPENAI_API_KEY and OPENAI_MODEL.');
        }

        $response = Http::baseUrl(rtrim((string) config('ai.base_url'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->post('/responses', [
                'model' => $model,
                'input' => $prompt,
            ])
            ->throw();

        $text = data_get($response->json(), 'output.0.content.0.text');

        if (! is_string($text) || $text === '') {
            throw new RuntimeException('AI response did not contain text output.');
        }

        return $text;
    }
}
