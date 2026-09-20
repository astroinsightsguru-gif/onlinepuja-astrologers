<?php

namespace App\AI\Contracts;

interface TextProvider
{
    public function slug(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /** @return array{ok:bool,text:string,model:string,usage:array,error:string} */
    public function chat(string $system, string $prompt, array $args = []): array;

    /** @return array<int, array{id:string,label:string}> */
    public function models(): array;
}
