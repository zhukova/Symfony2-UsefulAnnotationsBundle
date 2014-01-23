<?php

namespace Umbrellaweb\Bundle\UsefulAnnotationsBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * OwnershipRequirement annotation
 * 
 * Is used to check an ownership of object
 * before running a controller action
 *
 * @Annotation
 * @Target("METHOD")
 */
class OwnershipRequirement extends Annotation
{
    /**
     * Property by which ownership will be checked
     * 
     * @var string
     */
    public $property = 'owner';
}