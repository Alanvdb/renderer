<?php declare(strict_types=1);

namespace AlanVdb\Renderer\Definition;

interface PhpRendererFactoryInterface
{
    public function createPhpRenderer() : RendererInterface;
}
