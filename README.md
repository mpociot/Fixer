# StyleCI Fixer ![Analytics](https://ga-beacon.appspot.com/UA-60053271-6/StyleCI/Fixer?pixel)


<a href="https://travis-ci.org/StyleCI/Fixer"><img src="https://img.shields.io/travis/StyleCI/Fixer/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/StyleCI/Fixer/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/StyleCI/Fixer.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/StyleCI/Fixer"><img src="https://img.shields.io/scrutinizer/g/StyleCI/Fixer.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/StyleCI/Fixer/releases"><img src="https://img.shields.io/github/release/StyleCI/Fixer.svg?style=flat-square" alt="Latest Version"></img></a>


## Installation

Either [PHP](https://php.net) 5.6+ or [HHVM](http://hhvm.com) 3.9+ are required.

To get the latest version of StyleCI Fixer, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require styleci/fixer
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "styleci/fixer": "^3.0"
    }
}
```

If you're using Laravel 5, then you can register our service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'StyleCI\Fixer\FixerServiceProvider'`


## Documentation

StyleCI Fixer is a code style report builder.

Feel free to check out the [releases](https://github.com/StyleCI/Fixer/releases), [license](LICENSE), and [contribution guidelines](CONTRIBUTING.md).


## License

StyleCI Fixer is licensed under [The MIT License (MIT)](LICENSE).
