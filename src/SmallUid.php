<?php declare(strict_types=1);

namespace Mediagone\SmallUid;

use DateTimeImmutable;
use InvalidArgumentException;
use Mediagone\Common\Types\Text\Hex;
use Mediagone\Common\Types\ValueObject;
use function base_convert;
use function explode;
use function gmp_init;
use function gmp_strval;
use function is_string;
use function microtime;
use function preg_match;
use function str_pad;
use function strrev;
use function substr;


class SmallUid implements ValueObject
{
    //========================================================================================================
    // Constants
    //========================================================================================================
    
    public const TOTAL_CHARS_LENGTH = 16;
    
    public const RANDOM_CHARS_LENGTH = 5;
    
    
    
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private Hex $hex;
    
    private string $value;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private function __construct(Hex $hex)
    {
        if (! self::isHexValid($hex)) {
            throw new InvalidArgumentException(static::class . ' can only be created from a '. self::TOTAL_CHARS_LENGTH .' chars long Hex (got ' . $hex->getLength() . ' chars).');
        }
        
        $this->hex = $hex;
        $this->value = self::hexToBase62($hex->toString());
    }
    
    
    /**
     * Creates a new instance with nil value.
     */
    final public static function nil()
    {
        return new static(Hex::fromString('0000000000000000'));
    }
    
    
    /**
     * Creates a new instance with a sequential random value.
     */
    final public static function random()
    {
        $hex = self::generateHexTimestamp() . self::generateHexRandomBytes();
        
        return new static(Hex::fromString($hex));
    }
    
    
    /**
     * Creates a new instance from the given Hex object.
     */
    final public static function fromHex(Hex $hex)
    {
        return new static($hex);
    }
    
    
    /**
     * Creates a new instance from the given base62 string representation.
     */
    final public static function fromString(string $base62string)
    {
        if (! self::isValueValid($base62string)) {
            throw new InvalidArgumentException('Invalid base62 string (' . $base62string . ') for ' . static::class . ', it must only contains a-z or 0-9 chars."');
        }
        
        return new static(Hex::fromString(self::base62ToHex($base62string)));
    }
    
    
    
    //========================================================================================================
    // Static methods
    //========================================================================================================
    
    /**
     * Returns whether the specified value is a valid base62 string respresentation.
     *
     * @var string $value
     */
    public static function isValueValid($value) : bool
    {
        if (! is_string($value)) {
            return false;
        }
        
        if (preg_match('#^[a-z0-9]+$#i', $value) !== 1) {
            return false;
        }
        
        $hex = Hex::fromString(self::base62ToHex($value));
        
        return self::isHexValid($hex);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function jsonSerialize()
    {
        return $this->value;
    }
    
    
    /**
     * Return the uid's base62 string representation.
     */
    final public function toString() : string
    {
        return $this->value;
    }
    
    
    /**
     * Returns the uid's underlying Hex string.
     */
    final public function toHex() : Hex
    {
        return $this->hex;
    }
    
    
    
    /**
     * Returns the uid's timestamp as a date time object (without milliseconds).
     */
    final public function getDatetime() : DateTimeImmutable
    {
        $timestampLength = self::TOTAL_CHARS_LENGTH - self::RANDOM_CHARS_LENGTH;
        $baseTimestamp = (string)hexdec(substr($this->hex->toString(), 0, $timestampLength));
        
        $seconds = (int)substr($baseTimestamp, 0, - 3);
        
        return (new DateTimeImmutable())->setTimestamp($seconds);
    }
    
    
    /**
     * Compares the uid to an other uid, the result is TRUE if both contain the same value.
     */
    final public function isEqualTo(SmallUid $other) : bool
    {
        return $this->value === $other->value;
    }
    
    
    
    //========================================================================================================
    // Helpers
    //========================================================================================================
    
    private static function isHexValid(Hex $hex) : bool
    {
        return $hex->getLength() === self::TOTAL_CHARS_LENGTH;
    }
    
    
    /**
     * Returns the current timestamp as a 11 chars long hexadecimal string, with a 3 decimals precision (10^-3 s).
     * Note: "seconds" will overflow in 2038 if ran on 32bit PHP, then ever deploy on a 64bit platform!
     */
    private static function generateHexTimestamp() : string
    {
        $time = microtime(false);
        [$microseconds, $seconds] = explode(' ', $time);
        
        // Keep only millisecond digits.
        $microseconds = substr($microseconds, 2,3);
        
        $hex = base_convert($seconds . $microseconds, 10, 16);
        
        return str_pad($hex, self::TOTAL_CHARS_LENGTH - self::RANDOM_CHARS_LENGTH, '0', STR_PAD_LEFT);
    }
    
    
    private static function generateHexRandomBytes() : string
    {
        $randomHex = Hex::random(self::RANDOM_CHARS_LENGTH);
        
        return str_pad($randomHex->toString(), self::RANDOM_CHARS_LENGTH, '0', STR_PAD_LEFT);
    }
    
    
    private static function hexToBase62(string $hex) : string
    {
        // Reverse the hex string to maximize output's randomness (prevent similar prefixes).
        $hex = strrev($hex);
        
        return gmp_strval(gmp_init($hex, 16), 62);
    }
    
    
    private static function base62ToHex(string $base62string) : string
    {
        $hex = gmp_strval(gmp_init($base62string, 62), 16);
        
        // Reverse back the hex string, and pad it to restore terminal zeros.
        $hex = str_pad(strrev($hex), self::TOTAL_CHARS_LENGTH, '0', STR_PAD_RIGHT);
        
        return $hex;
    }
    
    
    
}
