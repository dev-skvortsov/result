<?php

declare(strict_types=1);

namespace Skd\Result\Error;

/**
 * The error contains information about what the error was, where it occurred and additional information.
 *
 * @author Dmitrii Skvortsov <dev.skvortsov@gmail.com>
 */
final readonly class Error
{
    public function __construct(
        public string $field,
        public string $errorMessage,
        public array $extra = [],
    ) {}

    public function isEqual(Error $error): bool
    {
        return $this->field === $error->field
               && $this->errorMessage === $error->errorMessage;
    }
}
