<?php

declare(strict_types=1);

namespace Skd\Result\Error;

/**
 * Notification pattern realization.
 *
 * @author  Dmitrii Skvortsov <dev.skvortsov@gmail.com>
 */
final class Notification
{
    /** @var Error[] */
    private array $errors = [];

    public function __construct(?Error $error = null)
    {
        if (null !== $error) {
            $this->addError($error);
        }
    }

    /**
     * Add an error to the errors list
     * Errors are deduplicated.
     */
    public function addError(Error $error): void
    {
        if (!$this->hasError($error)) {
            $this->errors[] = $error;
        }
    }

    /**
     * Check if a specific error exists in the errors list.
     */
    public function hasError(Error $error): bool
    {
        foreach ($this->getErrors() as $storedError) {
            if ($storedError->isEqual($error)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all errors from a list.
     *
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if the error list is empty.
     */
    public function hasErrors(): bool
    {
        return 0 !== count($this->getErrors());
    }

    /**
     * Merge two Notification objects.
     *
     * NOTE! Only the left object will be changed
     *
     * Errors from the passed Notification object will be added to the end of the list of the left object
     * Uniqueness will also be checked
     */
    public function merge(Notification $notification): void
    {
        foreach ($notification->getErrors() as $error) {
            $this->addError($error);
        }
    }
}
