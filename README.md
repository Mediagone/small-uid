# Small UID

⚠️ _This project is in experimental phase, the API may may be subject to change._

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)


UUIDs are frequently used as database _Primary Key_ in software development. However, they aren't the best choice mainly due to their random sorting and the resulting fragmentation in databases indexes.

Using [ULIDs](https://github.com/ulid/spec) is generally a very good alternative, solving most of UUID flaws.

**Small UIDs** are also an ideal alternative **when you do not need as much uniqueness** and want **shorter "user-friendly" encoded strings**.


Summary:
1. [Installation](#install)
2. Usages
   1. [Creation](#create)
   2. [Short-string representation](#shortstring)
   3. [Serialization](#serialize)
   4. [Retrieving creation DateTime](#datetime)
3. [License](#licence)


## Introduction

Small UIDs are short unique identifiers especially designed to be used as efficient database _Primary Key_:

- Half smaller than UUID / ULID (64-bit)
- Lexicographically sortable
- Encodable as a short user-friendly and URL-safe base-62 string (`a-zA-Z0-9`)
- User-friendly strings are generated in a way to be always very different (no shared prefix due to similar timestamps)


| |Small UID|ULID|UUID v4|
|---|:---:|:---:|:---:|
|Size|64 bits|128 bits|128 bits|
|Monotonic sort order|Yes &ast;&ast;|Yes|No|
|Random bits| 20 | 80 |122|
|Collision odds &ast;&ast;&ast;| 1,024 _/ ms &ast;_ | 1.099e+12 _/ ms &ast;_| 2.305e+18 |

&ast; _the Uid includes a timestamp, so collisions may occur only during the same millisecond._ \
&ast;&ast; _monotonic sort order, but random order when generated at the same millisecond._ \
&ast;&ast;&ast; _theorical number of generated Uids before the first expected collision._




They are internally stored as _64-bit_ integers (_44-bit_ timestamp followed by _20 random bits_):

    |-----------------------|  |------------|
            Timestamp            Randomness
             44 bits               20 bits


The random number suffix still guarantees a decent amount of uniqueness when many ids are created in the same millisecond (up to 1,048,576 different values) and you may only expect collision if you're generating more than 1024 random ids during the same millisecond.


### Sorting

Because of the sequential timestamp, _Small UIDs_ are naturally sorted chronologically. It **improves indexing** when inserting values in databases, new ids being appended to the end of the table without reshuffling existing data (read more [in this article](https://www.codeproject.com/Articles/388157/GUIDs-as-fast-primary-keys-under-multiple-database)).

However, **sort order within the same millisecond is not guaranteed** because of the random bits suffix.


## # <a name="install"></a>Installation

This package requires **PHP (64-bit) 7.4+** and **GMP extension**.

Add it as Composer dependency:
```sh
$ composer require mediagone/small-uid
```

If you're using Doctrine ORM, you'll probably want to install also appropriate custom types:
```sh
$ composer require mediagone/small-uid-doctrine
```


## Usages


### <a name="create"></a>Creation
Most useful way to generate an Uid is the `random()` static factory method, which creates a new random Uid using the current timestamp and a random suffix:
```php
$uid = SmallUid::random();
```

You can also generate an Uid from a 16-chars long hexadecimal string:
```php
$uid = SmallUid::fromHex('1234567890abcdef');
```

You can also generate an Uid from a base62-encoded string using `fromString` method, but this will detailed in the next section.
```php
$uid = SmallUid::fromString('LscmjzUyKLR');
```

In some cases, you may need a _null-object_ Uid, therefore there is a special static factory method:
```php
$uid = SmallUid::nil();
```


### <a name="shortstring"></a>Short-string representation
Using the hexadecimal representation is not always convenient (eg. for use in URLs), hopefully it can be safely converted back and forth to a base-62 string, which is only 10 or 11-chars long (depending on the Uid's internal value). 

To get an uid's short-string representation, just cast it to a string:
```php
$uid = SmallUid::fromHex('1234567890abcdef');
(string)$uid; // string(11) "LscmjzUyKLR"
```

To convert back the short-string to an Uid instance, use the `fromString()` static factory method:
```php
$uid = SmallUid::fromString('LscmjzUyKLR');
```


### <a name="serialize"></a>Serialization

You serialize your Uids using their hexadecimal representation (which is always a 16-chars long hexadecimal string):
```php
$uid = SmallUid::fromString('LscmjzUyKLR');
(string)$uid->getHex(); // string(16) "1234567890abcdef"
```

Uids can also be converted to an 8-bit binary string (_eg. for database persistence_):
```php
$uid = SmallUid::fromString('LscmjzUyKLR');

(string)$uid->toHex()->toHex()->toBinary(); // string(8) "4Vx" (binary string)
```


### <a name="datetime"></a>Retrieving creation DateTime

Every Uid embeds a timestamp reflecting its creation datetime:
```php
$uid = SmallUid::fromHex('1234567890abcdef');

$creationDatetime = $uid->getDatetime(); // retrieve Uid's creation datetime
(string)$creationDatetime; // "2009-08-23T03:58:16+00:00"
```



## <a name="licence"></a>License

_Small UID_ is licensed under MIT license. See LICENSE file.


[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-version]: https://img.shields.io/packagist/v/mediagone/small-uid.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/small-uid.svg

[link-packagist]: https://packagist.org/packages/mediagone/small-uid
[link-downloads]: https://packagist.org/packages/mediagone/small-uid
