<?php declare(strict_types=1);

namespace AlanVdb\Renderer\Exception;

use InvalidArgumentException;
use Throwable;

class InvalidTemplateVarProvided
    extends InvalidArgumentException
    implements Throwable
{}
