<?php

namespace App\Domain\Model;

use ReflectionClass;
use InvalidArgumentException;
use LogicException;

/***
 * This class was modified by Bookie developers to allow creating
 * personalized ENUM for dynamic messages creating during runtime
 *
 * This was achieved allowing the constructor's usage in the inherited
 * classed (change private to protected constructor).
 *
 *  Package marc-mabe/php-enum
 *  Version": v3.0.0
 */
abstract class EnumBookie
{
    /**
     * The selected enumerator value
     *
     * @var null|bool|int|float|string
     */
    private $value;

    /**
     * The ordinal number of the enumerator
     *
     * @var null|int
     */
    private $ordinal;

    /**
     * A map of enumerator names and values by enumeration class
     *
     * @var array ["$class" => ["$name" => $value, ...], ...]
     */
    private static $constants = [];

    /**
     * A List of available enumerator names by enumeration class
     *
     * @var array ["$class" => ["$name0", ...], ...]
     */
    private static $names = [];

    /**
     * Already instantiated enumerators
     *
     * @var array ["$class" => ["$name" => $instance, ...], ...]
     */
    private static $instances = [];

    /**
     * @var string
     */
    private static $personalized;


    /**
     * Constructor
     *
     * @param null|bool|int|float|string $value   The value of the enumerator
     * @param int|null                   $ordinal The ordinal number of the enumerator
     */
    final protected function __construct($value, $ordinal = null)
    {
        $this->value   = $value;
        $this->ordinal = $ordinal;
    }

    /**
     * Get the name of the enumerator
     *
     * @return string
     * @see getName()
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @throws LogicException Enums are not cloneable
     *                        because instances are implemented as singletons
     */
    final private function __clone()
    {
        throw new LogicException('Enums are not cloneable');
    }

    /**
     * @throws LogicException Enums are not serializable
     *                        because instances are implemented as singletons
     */
    final public function __sleep()
    {
        throw new LogicException('Enums are not serializable');
    }

    /**
     * @throws LogicException Enums are not serializable
     *                        because instances are implemented as singletons
     */
    final public function __wakeup()
    {
        throw new LogicException('Enums are not serializable');
    }

    /**
     * Get the value of the enumerator
     *
     * @return null|bool|int|float|string
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the name of the enumerator
     *
     * @return string
     */
    final public function getName()
    {
        $ordinal = $this->ordinal !== null ? $this->ordinal : $this->getOrdinal();

        if ((self::isPersonalizedEnum()) && isset(self::$personalized)) {
            return self::$personalized;
        }

        return self::$names[static::class][$ordinal];
    }

    /**
     * Get the ordinal number of the enumerator
     *
     * @return int
     */
    final public function getOrdinal()
    {
        if ($this->ordinal === null) {
            $ordinal = 0;
            $value   = $this->value;
            foreach (self::detectConstants(static::class) as $constValue) {
                if ($value === $constValue) {
                    break;
                }
                ++$ordinal;
            }

            $this->ordinal = $ordinal;
        }

        return $this->ordinal;
    }

    /**
     * Compare this enumerator against another and check if it's the same.
     *
     * @param mixed $enumerator
     * @return bool
     */
    final public function is($enumerator)
    {
        return $this === $enumerator || $this->value === $enumerator

            // The following additional conditions are required only because of the issue of serializable singletons
            || ($enumerator instanceof static
                && \get_class($enumerator) === static::class
                && $enumerator->value === $this->value
            );
    }

    /**
     * Get an enumerator instance of the given enumerator value or instance
     *
     * @param static|null|bool|int|float|string $enumerator
     * @return static
     * @throws InvalidArgumentException On an unknwon or invalid value
     * @throws LogicException           On ambiguous constant values
     */
    final public static function get($enumerator)
    {
        if ($enumerator instanceof static && \get_class($enumerator) === static::class) {
            return $enumerator;
        }

        return static::byValue($enumerator);
    }

    /**
     * Get an enumerator instance by the given value
     *
     * @param mixed $value
     * @return static
     * @throws InvalidArgumentException On an unknown or invalid value
     * @throws LogicException           On ambiguous constant values
     */
    final public static function byValue($value)
    {
        $constants = self::detectConstants(static::class);
        $name      = \array_search($value, $constants, true);
        if ($name === false) {
            throw new InvalidArgumentException(sprintf(
                'Unknown value %s for enumeration %s',
                \is_scalar($value)
                    ? \var_export($value, true)
                    : 'of type ' . (\is_object($value) ? \get_class($value) : \gettype($value)),
                static::class
            ));
        }

        if (!isset(self::$instances[static::class][$name])) {
            self::$instances[static::class][$name] = new static($constants[$name]);
        }

        return self::$instances[static::class][$name];
    }

    /**
     * Get an enumerator instance by the given name
     *
     * @param string $name The name of the enumerator
     * @return static
     * @throws InvalidArgumentException On an invalid or unknown name
     * @throws LogicException           On ambiguous values
     */
    final public static function byName($name)
    {
        $name = (string) $name;
        if (isset(self::$instances[static::class][$name])) {
            return self::$instances[static::class][$name];
        }

        $const = static::class . '::' . $name;

        if (self::isPersonalizedEnum() && isset(self::$personalized)) {
            self::$personalized = $name;
            $constant = uniqid();
            define($constant, $name);
            return new static(\constant($constant));
        }

        if (!\defined($const)) {
            throw new InvalidArgumentException($const . ' not defined');
        }

        return self::$instances[static::class][$name] = new static(\constant($const));
    }

    /**
     * Get an enumeration instance by the given ordinal number
     *
     * @param int $ordinal The ordinal number or the enumerator
     * @return static
     * @throws InvalidArgumentException On an invalid ordinal number
     * @throws LogicException           On ambiguous values
     */
    final public static function byOrdinal($ordinal)
    {
        $ordinal = (int) $ordinal;

        if (!isset(self::$names[static::class])) {
            self::detectConstants(static::class);
        }

        if (!isset(self::$names[static::class][$ordinal])) {
            throw new InvalidArgumentException(\sprintf(
                'Invalid ordinal number, must between 0 and %s',
                \count(self::$names[static::class]) - 1
            ));
        }

        $name = self::$names[static::class][$ordinal];
        if (isset(self::$instances[static::class][$name])) {
            return self::$instances[static::class][$name];
        }

        $const = static::class . '::' . $name;
        return self::$instances[static::class][$name] = new static(\constant($const), $ordinal);
    }

    /**
     * Get a list of enumerator instances ordered by ordinal number
     *
     * @return static[]
     */
    final public static function getEnumerators()
    {
        if (!isset(self::$names[static::class])) {
            self::detectConstants(static::class);
        }
        return \array_map([static::class, 'byName'], self::$names[static::class]);
    }

    /**
     * Get a list of enumerator values ordered by ordinal number
     *
     * @return mixed[]
     */
    final public static function getValues()
    {
        return \array_values(self::detectConstants(static::class));
    }

    /**
     * Get a list of enumerator names ordered by ordinal number
     *
     * @return string[]
     */
    final public static function getNames()
    {
        if (!isset(self::$names[static::class])) {
            self::detectConstants(static::class);
        }
        return self::$names[static::class];
    }
    /*
     * Get a list of enumerator ordinal numbers
     *
     * @return int[]
     */
    final public static function getOrdinals()
    {
        $count = \count(self::detectConstants(static::class));
        return $count === 0 ? [] : \range(0, $count - 1);
    }

    /**
     * Get all available constants of the called class
     *
     * @return array
     * @throws LogicException On ambiguous constant values
     */
    final public static function getConstants()
    {
        return self::detectConstants(static::class);
    }

    /**
     * Is the given enumerator part of this enumeration
     *
     * @param static|null|bool|int|float|string $value
     * @return bool
     */
    final public static function has($value)
    {
        if ($value instanceof static && \get_class($value) === static::class) {
            return true;
        }

        $constants = self::detectConstants(static::class);
        return \in_array($value, $constants, true);
    }

    /**
     * Detect all public available constants of given enumeration class
     *
     * @param string $class
     * @return array
     */
    private static function detectConstants($class)
    {
        if (!isset(self::$constants[$class])) {
            $reflection = new ReflectionClass($class);
            $publicConstants  = [];

            do {
                $scopeConstants = [];
                if (\PHP_VERSION_ID >= 70100) {
                    // Since PHP-7.1 visibility modifiers are allowed for class constants
                    // for enumerations we are only interested in public once.
                    foreach ($reflection->getReflectionConstants() as $reflConstant) {
                        if ($reflConstant->isPublic()) {
                            $scopeConstants[ $reflConstant->getName() ] = $reflConstant->getValue();
                        }
                    }
                } else {
                    // In PHP < 7.1 all class constants were public by definition
                    $scopeConstants = $reflection->getConstants();
                }

                $publicConstants = $scopeConstants + $publicConstants;
            } while (($reflection = $reflection->getParentClass()) && $reflection->name !== __CLASS__);

            assert(self::noAmbiguousValues($publicConstants));

            self::$constants[$class] = $publicConstants;
            self::$names[$class] = \array_keys($publicConstants);
        }

        return self::$constants[$class];
    }

    /**
     * Assert that the given enumeration class doesn't define ambiguous enumerator values
     * @param array $constants
     * @return bool
     */
    private static function noAmbiguousValues(array $constants)
    {
        foreach ($constants as $value) {
            $names = \array_keys($constants, $value, true);
            if (\count($names) > 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an enumerator instance by the given name.
     *
     * This will be called automatically on calling a method
     * with the same name of a defined enumerator.
     *
     * @param string $method The name of the enumerator (called as method)
     * @param array  $args   There should be no arguments
     * @return static
     * @throws InvalidArgumentException On an invalid or unknown name
     * @throws LogicException           On ambiguous constant values
     */
    final public static function __callStatic($method, array $args)
    {
        return self::byName($method);
    }

    final protected static function __personalized($message)
    {
        self::$personalized = $message;
        $constant = uniqid();
        define($constant, $message);
        return new static(\constant($constant));
    }

    final private static function isPersonalizedEnum(): bool
    {
        return method_exists(get_called_class(), 'personalized');
    }
}
