<?php

namespace SaraivaDaniel\PHPString\Test;

use SaraivaDaniel\PHPString\Annotations\Numeric;

class NumericAnnotation
{
    /**
     * @Numeric(sequence=1, size=8)
     */
    public $sku;

    /**
     * @Numeric(sequence=2, size=8, decimals=2)
     */
    public $qty;
    
    /**
     * @Numeric(sequence=3, size=8, decimals=2, decimal_separator=".")
     */
    public $price;

    /**
     * @Numeric(sequence=3, size=8, decimals=2, decimal_separator=",")
     */
    public $price_comma;
}
