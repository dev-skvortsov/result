<?php

declare(strict_types=1);

namespace Skd\Result\Exception;

use Skd\Result\Error\Notification;

/**
 * @author Dmitrii Skvortsov <dev.skvortsov@gmail.com>
 */
class ResultException extends \Exception
{
    public const RESULT_IN_ERROR_STATE = 'The result is in ERROR state';
    public const RESULT_IN_OK_STATE = 'The result is in OK state';
    public const ERRORS_LIST_IS_EMPTY = 'Errors list is empty';
    public const VALUE_CANNOT_BE_NULL = 'Value cannot be null';

    public function __construct(
        string $message,
        public readonly mixed $value = null,
        public readonly ?Notification $errors = null,
    ) {
        parent::__construct($message);
    }

    public static function theResultIsInErrorState(Notification $errors): ResultException
    {
        return new self(self::RESULT_IN_ERROR_STATE, errors: $errors);
    }

    public static function theResultIsInOkState(mixed $value): ResultException
    {
        return new self(self::RESULT_IN_OK_STATE, value: $value);
    }

    public static function errorsListIsEmpty(): ResultException
    {
        return new self(self::ERRORS_LIST_IS_EMPTY);
    }

    public static function valueCannotBeNull(): ResultException
    {
        return new self(self::VALUE_CANNOT_BE_NULL);
    }
}
