# Small UID

⚠️ _This project is in experimental phase, the API may change any time._

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

Small UIDs are short unique identifiers especially designed to be used as efficient database _Primary Key_.

- Lexicographically sortable
- Half smaller than UUID / ULID (64-bit only)
- Encodable as a short user-friendly and URL safe string (base 62)
- User-friendly strings are generated in a way to be always very different (no shared prefix due to similar timestamps)


| |Small UID|ULID|UUID v4|
|---|:---:|:---:|:---:|
|Size|64 bits|128 bits|128 bits|
|Monotonic sort order|Yes&ast;|Yes|No|
|Random bits| 20 | 80 |122|
|Collision odds&ast;&ast;| 1,048,576 _/ ms_ | 1.099e+12 / ms| 2.305e+18 |

&ast; _monotonic sort order, but random when generated at the same millisecond._\
&ast;&ast; _theorical number of generated uids before the first expected collision._



## Installation

This package requires **PHP (64-bit) 7.4+** and **GMP extension**.

Add it as Composer dependency:
```sh
$ composer require mediagone/small-uid
```

If you're using Doctrine ORM, you'll probably want to install also appropriate custom types:
```sh
$ composer require mediagone/small-uid-doctrine
```



## Introduction

UUIDs are frequently used as database _Primary Key_ in software development. However, they aren't the best choice mainly due to their random sorting and the resulting fragmentation in databases indexes. Using [ULID](https://github.com/ulid/spec) is generally a very good alternative, solving most of UUID flaws.

_SmallUIDs_ are also an ideal alternative **when you do not need as much uniqueness and require shorter "user-friendly" encoded strings**. They are _64-bit_ integers (_44-bit_ timestamp followed by _20 random bits_):

    |-----------------------|  |------------|
            Timestamp            Randomness
             44 bits               20 bits


The random number suffix still guarantees a decent amount of uniqueness when many ids are created in the same millisecond (up to 1,048,576 different values).


## Examples of usage

### Generating SMUID

```php
$uid = SmallUid::random();  // e.g. 01AN4Z07BY79KA1307SR9X4MV3
$uid = SmallUid::fromString($base62String);
```



## Sorting

Because of the sequential timestamp, _SmallUIDs_ are naturally sorted chronologically. It **improves indexing** when inserting values in databases, new ids being appended to the end of the table without reshuffling existing data (see more [in this article](https://www.codeproject.com/Articles/388157/GUIDs-as-fast-primary-keys-under-multiple-database)).

However, **sort order within the same millisecond is not guaranteed** because of the random suffix.


## License

_SmallUID_ is licensed under MIT license. See LICENSE file.


[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-version]: https://img.shields.io/packagist/v/mediagone/small-uid.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/small-uid.svg

[link-packagist]: https://packagist.org/packages/mediagone/small-uid
[link-downloads]: https://packagist.org/packages/mediagone/small-uid
