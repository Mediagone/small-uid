<?php declare(strict_types=1);

namespace Tests\Mediagone\SmallUid;

use DateTime;
use InvalidArgumentException;
use Mediagone\Common\Types\Text\Hex;
use Mediagone\SmallUid\SmallUid;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Mediagone\SmallUid\SmallUid
 */
final class SmallUidTest extends TestCase
{
    //========================================================================================================
    // Instantiation
    //========================================================================================================
    
    public function test_can_create_a_nil_uid() : void
    {
        self::assertInstanceOf(SmallUid::class, SmallUid::nil());
    }
    
    
    public function test_can_create_a_random_uid() : void
    {
        self::assertInstanceOf(SmallUid::class, SmallUid::random());
    }
    
    
    public function test_can_be_created_from_a_valid_string() : void
    {
        $uid = SmallUid::fromString('LscmjzUyKLR');
        self::assertSame('LscmjzUyKLR', (string)$uid); //1234567890abcdef
    }
    
    
    public function test_cannot_be_created_from_an_invalid_string() : void
    {
        $this->expectException(InvalidArgumentException::class);
        
        SmallUid::fromString('');
    }
    
    public function test_can_be_created_from_an_Hex() : void
    {
        $hex = Hex::fromString('1234567890abcdef');
        $uid = SmallUid::fromHex($hex);
        
        self::assertInstanceOf(SmallUid::class, $uid);
        self::assertSame('LscmjzUyKLR', (string)$uid);
        self::assertSame((string)$hex, (string)$uid->toHex());
    }
    
    
    public function test_cannot_be_created_from_a_too_short_Hex() : void
    {
        $this->expectException(InvalidArgumentException::class);
        
        SmallUid::fromHex(Hex::fromString('1234567890abc'));
    }
    
    
    public function test_cannot_be_created_from_a_too_long_Hex() : void
    {
        $this->expectException(InvalidArgumentException::class);
        
        SmallUid::fromHex(Hex::fromString('1234567890abcdefFFFF'));
    }
    
    
    
    //========================================================================================================
    // Conversion
    //========================================================================================================
    
    public function test_can_be_encoded_to_json() : void
    {
        $uid = SmallUid::fromString('LscmjzUyKLR');
        
        self::assertSame('"LscmjzUyKLR"', json_encode($uid->jsonSerialize()));
    }
    
    
    public function test_can_be_cast_to_string() : void
    {
        $uid = SmallUid::fromString('LscmjzUyKLR');
        
        self::assertSame('LscmjzUyKLR', (string)$uid);
    }
    
    
    public function test_can_be_returned_as_Hex() : void
    {
        $uid = SmallUid::fromString('LscmjzUyKLR');
        
        self::assertInstanceOf(Hex::class, $uid->toHex());
        self::assertSame('1234567890abcdef', (string)$uid->toHex());
    }
    
    
    public function test_can_get_creation_datetime() : void
    {
        $now = new DateTime();
        $timestamp = str_pad((string)$now->getTimestamp(), 11, '0', STR_PAD_LEFT);
        $timestamp .= '000'; // add milliseconds
        
        $timestampPart = base_convert($timestamp, 10, 16);
        $randomPart = 'AAAAA';
        $uid = SmallUid::fromHex(Hex::fromString($timestampPart . $randomPart));
        
        self::assertSame($now->format('Y-m-d H:i:s'), $uid->getDatetime()->format('Y-m-d H:i:s'));
    }
    
    
    public function test_nil_uid_is_valid() : void
    {
        self::assertSame('0', (string)SmallUid::nil());
        self::assertSame('0000000000000000', (string)SmallUid::nil()->toHex());
    }
    
    
    
    //========================================================================================================
    // Misc
    //========================================================================================================
    
    public function test_can_tell_value_is_valid() : void
    {
        self::assertTrue(SmallUid::isValueValid('LscmjzUyKLR'));
    }
    
    
    public function test_can_tell_too_long_value_is_invalid() : void
    {
        self::assertFalse(SmallUid::isValueValid('LscmjzUyKLRaaaaaaaaaaaaaa'));
    }
    
    
    public function test_can_tell_empty_value_is_invalid() : void
    {
        self::assertFalse(SmallUid::isValueValid(''));
    }
    
    
    public function test_can_tell_non_string_value_is_invalid() : void
    {
        self::assertFalse(SmallUid::isValueValid(100));
        self::assertFalse(SmallUid::isValueValid(true));
    }
    
    
    public function test_can_compare_two_equal_uids() : void
    {
        $uid1 = SmallUid::fromString('LscmjzUyKLR');
        $uid2 = SmallUid::fromString('LscmjzUyKLR');
        self::assertTrue($uid1->isEqualTo($uid2));
        self::assertTrue($uid2->isEqualTo($uid1));
    }
    
    
    public function test_can_compare_two_different_uids() : void
    {
        $uid1 = SmallUid::fromString('LscmjzUyKLR');
        $uid2 = SmallUid::fromString('DifferentId');
        self::assertFalse($uid1->isEqualTo($uid2));
        self::assertFalse($uid2->isEqualTo($uid1));
    }
    
    
    public function test_can_be_extended() : void
    {
        self::assertInstanceOf(SmallUid::class, ExtendedSmallUid::nil());
        self::assertInstanceOf(ExtendedSmallUid::class, ExtendedSmallUid::nil());
    }
    
    
    
}
