<?php

namespace App\AI\Contracts;

interface ImageProvider
{
    public function slug(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /** @return array{ok:bool,binary:string,mime:string,credit:string,error:string} */
    public function create(string $prompt, array $args = []): array;

    /** @return array<int, array{id:string,label:string}> */
    public function models(): array;
}
