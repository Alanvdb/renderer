<?php declare(strict_types=1);

namespace AlanVdb\Renderer\Exception;

use InvalidArgumentException;
use Throwable;

class TemplateNotFoundException
    extends InvalidArgumentException
    implements Throwable
{}
