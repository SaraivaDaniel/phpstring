<?php

namespace SaraivaDaniel\PHPString\Test;

use SaraivaDaniel\PHPString\Annotations\Text;
use SaraivaDaniel\PHPString\Annotations\Date;
use SaraivaDaniel\PHPString\Annotations\Numeric;

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