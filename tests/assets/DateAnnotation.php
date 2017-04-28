<?php

namespace SaraivaDaniel\PHPString\Test;

use SaraivaDaniel\PHPString\Annotations\Date;

class DateAnnotation
{
    /**
     * @var \Carbon\Carbon
     * @Date(sequence=1, size=8, format="Ymd")
     */
    public $date;

}
