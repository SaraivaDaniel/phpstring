<?php

namespace SaraivaDaniel\PHPString;

use Carbon\Carbon;
use SaraivaDaniel\PHPString\Test\Event;
use SaraivaDaniel\PHPString\Test\NumericAnnotation;
use SaraivaDaniel\PHPString\Test\DateAnnotation;
use PHPUnit_Framework_TestCase;

class PHPStringTest extends PHPUnit_Framework_TestCase
{
    public function testDateAnnotationToObject()
    {
        $parser = new PHPString('SaraivaDaniel\PHPString\Test\DateAnnotation');
        
        /* @var $date Test\DateAnnotation */

        // case 1: date exists
        $date = $parser->toObject('20160621');
        $this->assertInstanceOf('Carbon\\Carbon', $date->date);
        $this->assertEquals('20160621', $date->date->format('Ymd'));

        // case2: date empty
        $date = $parser->toObject('        ');
        $this->assertSame(NULL, $date->date);

        // case3: date invalid
        $date = $parser->toObject('00  00  ');
        $this->assertSame(NULL, $date->date);
    }
    
    public function testDateAnnotationToString() {
        $parser = new PHPString('SaraivaDaniel\PHPString\Test\DateAnnotation');
        
        $now = Carbon::now();
        
        $date = new DateAnnotation();
        $date->date = $now;
        
        $this->assertEquals($now->format('Ymd'), $parser->toString($date));
    }
    
    public function testNumericAnnotationToObject() 
    {
        $parser = new PHPString('SaraivaDaniel\PHPString\Test\NumericAnnotation');
        
        /* @var $numeric Test\NumericAnnotation */
        
        $numeric = $parser->toObject('000021210000123401200.11');
        $this->assertSame('00002121', $numeric->sku);
        $this->assertSame(12.34, $numeric->qty);
        $this->assertSame(1200.11, $numeric->price);
    }
    
    public function testNumericAnnotationToString() {
        $parser = new PHPString('SaraivaDaniel\PHPString\Test\NumericAnnotation');
        
        $numeric = new NumericAnnotation;
        $numeric->sku = '025';
        $numeric->qty = '100.02';
        $numeric->price = '123';
        $this->assertEquals('000000250001000200123.00', $parser->toString($numeric));
        
        $numeric->sku = 25;
        $numeric->qty = 100.02;
        $numeric->price = 123.45;
        $this->assertEquals('000000250001000200123.45', $parser->toString($numeric));
        
        $numeric->sku = 25;
        $numeric->qty = 100;
        $numeric->price = 123;
        $this->assertEquals('000000250001000000123.00', $parser->toString($numeric));
    }

    public function testToObject()
    {
        $parser = new PHPString('SaraivaDaniel\PHPString\Test\Event');
        $event = $parser->toObject("BH Bike Show        20160621002000Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.");

        $this->assertEquals('BH Bike Show', $event->name);
        $this->assertEquals('20160621', $event->date->format('Ymd'));
        $this->assertSame(20., $event->price);
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.', $event->description);
    }

    public function testToString()
    {
        $event = new Event();
        $event->name = 'Motocross Adventure';
        $event->description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.';
        $event->date = Carbon::createFromFormat('Y-m-d', '2016-06-21');
        $event->price = 1200.98;

        $parser = new PHPString('SaraivaDaniel\PHPString\Test\Event');
        $this->assertEquals('Motocross Adventure 20160621120098Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce consequat augue at hendrerit posuere.', $parser->toString($event));
    }
}