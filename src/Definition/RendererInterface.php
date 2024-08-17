<?php declare(strict_types=1);

namespace AlanVdb\Renderer\Definition;

interface RendererInterface
{
    public function render(string $template, array $vars = []) : string;
}
