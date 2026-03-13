<?php

namespace App\Contracts\AI;

interface AIClientInterface
{
    public function generateText(string $prompt, array $options = []): string;
}
