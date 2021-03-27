<?php

namespace SaraivaDaniel\PHPString\Annotations;

/**
 * @Annotation
 */
class Numeric extends Layout
{
    public $decimals = 0;
    public $ignore_left_zeroes = false;
    public $decimal_separator = '';
}