# The Result object

This package allows you to work with the results of operations, including errors, in an object-oriented style,
without using exceptions. For example, when using the 'Always valid domain model' approach.
Result object allows you to accumulate errors and return them all at once ([Notification](https://martinfowler.com/eaaDev/Notification.html) pattern is inside).
At the same time, the code becomes more understandable and logical.

## Installation

```bash
composer require skd/result
```


## Result

The Result class is an immutable generic class (using ``@template`` annotation) with 2 states: `Ok` and `Error`.
The ``OK`` state means that there are no errors inside and there is some value (success operation result).
The ``Error`` state means that an error or several errors were received during the operation.

Use ``@return Result<type_or_class>`` annotation to tell the IDE and static analysis tool what type value is inside;

### Ok state

Use static method ``Result::ok`` to return success operation result with some value inside
```php
use Skd\Result\Result;

// some code
return Skd\Result\Result::ok($value);
```

Use method ``Result::getValue(): T`` to get the value. Note that calling this method on Error state
will throw an exception. To prevent that you have to check the result state by calling one of methods 
``Result::isOk(): bool`` or ``Result::isError(): bool`` before.
```php
if($result->isOk()) {
    $value = $result->getValue();
}
```

### Error state

Use static method ``Result:error(Notification $errors)`` to return result with an error (errors). The non-empty [Notification](#notification)
object must be passed as an argument. Passing an empty Notification object (no errors inside) will throw an exception.
```php
use Skd\Result\Result;
use Skd\Result\Error\Notification;

// some code

$error = new Error(...);
$errors = new Notification($error);

return Skd\Result\Result::error($errors);
```

Use method ``Result::getErrors(): Notification`` to get an error (errors). Note that calling that method on
``Ok`` state will throw an exception. You can't change Notification object after Result object is initialized

```php
$result = Result::error(new Notification(...))

// $errors here is a cloned copy, you cannot change the list of errors in the $result object
$errors = $result->getErrors();
```

## Notification

Notification class is a Notification pattern realisation. It can be initialized in two ways: as an empty list or a list with one error inside

```php
use Skd\Result\Error\Notification;
use Skd\Result\Error\Error;

// empty
$emptyNotification = new Notification();

// with one error
$error = new Error('code', 'message');
$notificationWithOneError = new Notification($error);
```

Use method ``Notification::hasErrors(): bool`` to check if there are error/errors inside the notification object

```php
use Skd\Result\Error\Notification;

$errors = new Notification();
$errors->hasErrors(); // false

$errors = new Notification($error);
$errors->hasErrors(); // true
```

Use method ``Notification::hasError(Error $error): bool`` to check if the specific error is inside the notification object.
It can be useful in Unit tests.

```php
use Skd\Result\Error\Notification;

$errors = new Notification($error);

$errors->hasError($error); // true
$errors->hasError($anotherError); // false
```

### Errors Accumulating

Errors can be accumulated in a Notification object. 

Use ``Notification::addError(Error $error): void`` to add some error in the list

```php
use Skd\Result\Error\Error;
use Skd\Result\Error\Notification;

$errors = new Notification();
// it can be initialized in both ways
$errors = new Notification($someError);

$errors->addError($anotherError);
```

Notification objects can be merged as well. It can be useful when you have to accumulate two or more results with errors.
Note that only left object will be changed.
```php
use Skd\Result\Error\Notification;

$notification = new Notification($error);
$anotherNotification = new Notification($anotherError);

$notification->merge($anotherNotification);

$notification->hasError($error); // true
$notification->hasError($anotherError); // true

// but
$anotherNotification->hasError($error); // false
```

### Example:

#### Some Value Object class
```php
use Skd\Result\Error\Error;
use Skd\Result\Error\Notification;
use Skd\Result\Result;

class SomeClass
{
    private function __construct(public readonly mixed $someField)
    {
    }

    /**
     * @return Result<SomeClass>
     */
    public static function create(mixed $someValue): Result
    {
        $errors = new Notification();
        
        // some validation
        if (...) {
            $errors->addError();
        }
        
        // another validation (multiple errors)
        if (...) {
            $errors->addError(new Error('anotherCode', 'Another error message'));
        }
        
        if ($errors->hasErrors()) {
            return Result::error($errors)
        }
        
        return Result::ok(new self($someValue));
    }
}
```

You can replace ```new Error('code', 'Error message')``` with static factory:
```php
final class SomeErrorsFactory
{
  public static function invalidValue(): DomainError
  {
    return new Error('code', 'Error message');
  }
}

#### Unit tests
```php
public function testInvalidValue(): void
{
  $result = SomeClass::create($invalidValue);

  $this->assertTrue($result->isError());
  $this->assertTrue($result->getErrors()->hasError(SomeErrorsFactory::invalidValue()));
}
```


```