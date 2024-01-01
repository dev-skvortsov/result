<?php

declare(strict_types=1);

namespace Skd\Result;

use Skd\Result\Error\Notification;
use Skd\Result\Exception\ResultException;

/**
 * The result of the operation.
 *
 * Result instances are immutable
 *
 * @author Dmitrii Skvortsov <dev.skvortsov@gmail.com>
 *
 * @template T
 */
final class Result
{
    /**
     * @param T $value
     */
    private function __construct(
        private readonly mixed $value = null,
        private readonly ?Notification $errors = null,
    ) {}

    /**
     * Initialize Result instance in OK state
     * Value cannot be null.
     *
     * @template I
     *
     * @param I $value
     *
     * @return Result<I>
     */
    public static function ok(mixed $value = new Ok()): Result
    {
        if (null === $value) {
            throw ResultException::valueCannotBeNull();
        }

        return new self(value: $value);
    }

    /**
     * Initialize Result instance in ERROR state.
     *
     * @param Notification $errors - must be non-empty otherwise ResultException will be thrown
     *
     * @throws ResultException
     */
    public static function error(Notification $errors): Result
    {
        if (!$errors->hasErrors()) {
            throw ResultException::errorsListIsEmpty();
        }

        return new self(errors: $errors);
    }

    /**
     * Returns an operation result.
     *
     * NOTE! If the Result instance is in ERROR state ResultException will be thrown
     *
     * @return T
     *
     * @throws ResultException
     */
    public function getValue(): mixed
    {
        if (null !== $errors = $this->errors) {
            throw ResultException::theResultIsInErrorState($errors);
        }

        return $this->value;
    }

    /**
     * Returns a Notification instance with errors.
     *
     * NOTE! If the Result instance is in OK state ResultException will be thrown
     *
     * @throws ResultException
     */
    public function getErrors(): Notification
    {
        if (null === $this->errors) {
            throw ResultException::theResultIsInOkState($this->value);
        }

        return clone $this->errors;
    }

    /**
     * Check if the Result is in OK state.
     */
    public function isOk(): bool
    {
        return null === $this->errors;
    }

    /**
     * Check if the Result is in ERROR state.
     */
    public function isError(): bool
    {
        return !$this->isOk();
    }

    /**
     * Executes the first callback if the Result in OK state otherwise the second one will be executed.
     *
     * @template TFunc
     *
     * @param \Closure(T=): TFunc            $onSuccess
     * @param \Closure(Notification=): TFunc $onError
     *
     * @return TFunc
     */
    public function either(\Closure $onSuccess, \Closure $onError): mixed
    {
        if ($this->isOk()) {
            return $onSuccess($this->getValue());
        }

        return $onError($this->getErrors());
    }
}
