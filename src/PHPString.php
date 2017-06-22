<?php

namespace SaraivaDaniel\PHPString;

use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Exception;
use SaraivaDaniel\PHPString\Annotations\Date;
use SaraivaDaniel\PHPString\Annotations\Layout;
use SaraivaDaniel\PHPString\Annotations\Numeric;
use SaraivaDaniel\PHPString\Annotations\Text;
use ReflectionClass;

class PHPString
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * Constructor.
     */
    public function __construct($class)
    {
        if(!class_exists($class))
            throw new Exception('Class not exits');

        $this->class = $class;

        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Date.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Numeric.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Text.php');

        $this->annotationReader = new AnnotationReader();

        $this->reflectionClass = new ReflectionClass($this->class);
    }

    /**
     * Convert string to object
     *
     * @param $string
     * @return object
     */
    public function toObject($string)
    {
        $object = new $this->class;

        $i = 0;

        foreach($this->reflectionClass->getProperties() as $reflectionProperty)
        {
            /* @var $reflectionProperty \ReflectionProperty */
            foreach($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation)
            {
                if ($propertyAnnotation instanceof Layout)
                {
                    $value = substr($string, $i, $propertyAnnotation->size);

                    /*
                     * Date
                     */
                    if ($propertyAnnotation instanceof Date && strlen(trim($value)) > 0)
                    {
                        try
                        {
                            if (trim($value, '0') == '')
                            {
                                throw new \Exception("Data zerada");
                            }

                            $date = Carbon::createFromFormat(trim($propertyAnnotation->format), trim($value));

                            if ($date->format($propertyAnnotation->format) !== $value)
                            {
                                throw new \Exception("Erro na interpretação da data");
                            }
                        } catch (\Exception $ex)
                        {
                            // catches both exception that may be thrown above, as well Carbon own exceptions
                            $date = null;
                        }

                        $reflectionProperty->setValue($object, $date);
                    }

                    /*
                     * Text
                     */
                    if ($propertyAnnotation instanceof Text)
                        $reflectionProperty->setValue($object, trim($value));

                    /*
                     * Numeric
                     */
                    if ($propertyAnnotation instanceof Numeric)
                    {
                        // numeric type may be a number (as in int/float) but may also be a string, in which case 
                        // leading zeros have to be preserved
                        // we expect only digits [0-9], spaces may be tolerated
                        do
                        {
                            if (trim($value) == '')
                            {
                                $value = NULL;
                                break;
                            }

                            // we expect only digits [0-9] and decimal separator
                            if ($propertyAnnotation->decimal_separator == '')
                            {
                                $pattern = '/[^0-9]/';
                            } elseif ($propertyAnnotation->decimal_separator == '.')
                            {
                                $pattern = '/[^0-9\.]/';
                            } elseif ($propertyAnnotation->decimal_separator == ',')
                            {
                                $pattern = '/[^0-9,]/';
                            } else
                            {
                                throw new \Exception("Invalid decimal separator");
                            }
                            if (preg_match($pattern, $value) !== 0)
                            {
                                throw new Exception("[$value] is not numeric [{$this->class}::{$reflectionProperty->name}]");
                            }

                            // if annotation defines decimal separator, then we assume this value is a number, so we convert it to float
                            if ($propertyAnnotation->decimals > 0)
                            {
                                $value = floatval($value);
                                if ($propertyAnnotation->decimal_separator == "")
                                {
                                    $value /= (pow(10, $propertyAnnotation->decimals));
                                }
                                break;
                            }

                            // if we reach here, $value contains the original value, just trimmed
                            // we can't trim for zeroes because they might be significant here (like in CNPJ/CPF)
                        } while (0);

                        $reflectionProperty->setValue($object, $value);
                    }



                    //Increment.
                    $i += $propertyAnnotation->size;
                }
            }
        }

        return $object;
    }

    /**
     * Convert object to string
     *
     * @param $object
     * @return string
     */
    public function toString($object)
    {
        if(get_class($object) != $this->class)
            throw new Exception("The object is not an instance of $this->class");

        $string = "";

        foreach($this->reflectionClass->getProperties() as $reflectionProperty)
        {
            /* @var $reflectionProperty \ReflectionProperty */
            foreach($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation)
            {
                if ($propertyAnnotation instanceof Layout)
                {
                    $value = $reflectionProperty->getValue($object);

                    if(is_null($value))
                    {
                        $filler = ($propertyAnnotation instanceof Numeric) ? '0' : ' ';
                        $string .= str_pad('', $propertyAnnotation->size, $filler, STR_PAD_RIGHT);
                        break;
                    }

                    /*
                     * Date
                     */
                    if ($propertyAnnotation instanceof Date)
                    {
                        if(!($value instanceof Carbon)) {
                            throw new Exception("$value is not an instance of Carbon");
                        }
                        
                        $valuestr = $value->format($propertyAnnotation->format);
                        
                        // datas tem valores definidos, caso o valor formatado não coincida com o tamanho do campo há um erro na definição ou na data informada
                        if (strlen($valuestr) !== $propertyAnnotation->size) {
                            throw new \Exception("O valor '{$valuestr}' tem comprimento inválido para o campo {$reflectionProperty->getName()}");
                        }

                        $string .= $valuestr;
                    }

                    /*
                     * Text
                     */
                    if ($propertyAnnotation instanceof Text) {
                        // limita string ao tamanho do campo, caso seja maior, e caso seja menor, faz pad com espaços à direita
                        $value = substr($value, 0, $propertyAnnotation->size);
                        $string .= str_pad($value, $propertyAnnotation->size, ' ', STR_PAD_RIGHT);
                    }

                    /*
                     * Numeric
                     */
                    if ($propertyAnnotation instanceof Numeric)
                    {
                        if (!is_numeric($value))
                            throw new Exception("$value is not numeric");

                        if (is_string($value))
                            $value *= 1.0;

                        if($propertyAnnotation->decimals > 0)
                            $value = number_format($value, $propertyAnnotation->decimals, $propertyAnnotation->decimal_separator, '');
                        
                        // valores numericos não podem ser truncados
                        if (strlen($value) > $propertyAnnotation->size) {
                            throw new \Exception("O valor '{$value}' é muito longo para o campo {$reflectionProperty->getName()}");
                        }

                        $string .= str_pad($value, $propertyAnnotation->size, '0', STR_PAD_LEFT);
                    }
                }
            }
        }

        return $string;
    }

    /**
     * Get size layout
     *
     * @return int
     */
    public function getSize()
    {
        $i = 0;

        foreach($this->reflectionClass->getProperties() as $reflectionProperty)
        {
            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation)
            {
                if ($propertyAnnotation instanceof Layout)
                {
                    $i += $propertyAnnotation->size;
                }
            }
        }

        return $i;
    }
}