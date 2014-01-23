<?php

namespace Umbrellaweb\Bundle\UsefulAnnotationsBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * CsrfProtector annotation
 * 
 * Is used to validate a CSRF token 
 * before running the controller method
 *
 * @Annotation
 * @Target("METHOD")
 */
final class CsrfProtector extends Annotation
{
	/**
	 * CSRF token intention
	 * 
	 * @var string
	 */
	public $intention = 'unknown';
	
	/**
	 * name of field in request
	 * 
	 * @var request
	 */
	public $name = '_token';
}