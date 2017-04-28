# PHP String
[![Travis](https://travis-ci.org/danielslcosta/phpstring.svg?branch=1.0)](https://travis-ci.org/danielslcosta/phpstring)
[![Latest Stable Version](https://poser.pugx.org/saraivadaniel/phpstring/v/stable)](https://packagist.org/packages/saraivadaniel/phpstring) 
[![Total Downloads](https://poser.pugx.org/saraivadaniel/phpstring/downloads)](https://packagist.org/packages/saraivadaniel/phpstring) 
[![Latest Unstable Version](https://poser.pugx.org/saraivadaniel/phpstring/v/unstable)](https://packagist.org/packages/saraivadaniel/phpstring)
[![MIT license](https://poser.pugx.org/saraivadaniel/phpstring/license)](http://opensource.org/licenses/MIT)

Create objects filling attribute classes with string data (fork of jansenfelipe/phpstring)

## How to use

Add library

```sh
$ composer require saraivadaniel/phpstring
```

Add autoload.php in your file:

```php
require_once 'vendor/autoload.php';  
```

Add annotations
```php
<?php

class Event
{
    /**
     * @Text(sequence=1, size=20)
     */
    public $name;

    /**
     * @Date(sequence=2, size=8, format="Ymd")
     */
    public $date;

    /**
     * @Numeric(sequence=3, size=6, decimals=2, decimal_separator="")
     */
    public $price;

    /**
     * @Text(sequence=4, size=100)
     */
    public $description;

}
```

#### String -> Object

```php
$parser = new PHPString(Event::class);
$event = $parser->toObject("BH Bike Show        20160621002000Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.");

echo $parser->getSize(); //output: 134
```

#### String <- Object

```php
$parser = new PHPString(Event::class);

$event = new Event();
$event->name = 'Motocross Adventure';
$event->description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.';
$event->date = Carbon::createFromFormat('Y-m-d', '2016-06-21');
$event->price = 1200.98;

$string = $parser->toString($event);
```

### License

The MIT License (MIT)
