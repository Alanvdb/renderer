<?php declare(strict_types=1);

namespace AlanVdb\Renderer\Factory;

use AlanVdb\Renderer\Definition\PhpRendererFactoryInterface;
use AlanVdb\Renderer\Definition\RendererInterface;
use AlanVdb\Renderer\PhpRenderer;

class PhpRendererFactory implements PhpRendererFactoryInterface
{
    public function createPhpRenderer() : RendererInterface
    {
        return new PhpRenderer();
    }
}
