<?php

namespace Umbrellaweb\Bundle\UsefulAnnotationsBundle\Listener;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Umbrellaweb\Bundle\UsefulAnnotationsBundle\Annotation\OwnershipRequirement;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Umbrellaweb\Bundle\UsefulAnnotationsBundle\Exception\MethodNotFoundException;

/**
 * Controller listener
 *  
 * Listen each method of controller on @OwnershipRequirement annotation
 * and check the ownership of the current user to the object
 * 
 * If object isn't owned by the current user 404 error will be returned
 */
class OwnershipRequirementListener
{

    private $reader;
    private $security;

    public function __construct(Reader $reader, SecurityContextInterface $security)
    {
        $this->reader = $reader;
        $this->security = $security;
    }

    /**
     * This event will fire during any controller event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        //controller object and method string should be present in the $event->getController()
        if (!is_array($controller = $event->getController()))
        {
            return;
        }

        $request = $event->getRequest();

        $method = new \ReflectionMethod($controller[0], $controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $configuration)
        {
            if ($configuration instanceof OwnershipRequirement)
            {
                // create property method, e.g. getOwner(), get
                $propery_method = 'get' . ucfirst($configuration->property);
                
                if (method_exists($request->attributes->get($configuration->value), $propery_method))
                {
                    //check an ownership. Object MUST have an getOwner() method          
                    if ($request->attributes->get($configuration->value)->$propery_method() != $this->security->getToken()->getUser())
                    {
                        throw new NotFoundHttpException('Not Found');
                    }
                }
                else
                {
                    throw new MethodNotFoundException('Requested method '.  get_class($request->attributes->get($configuration->value)) . '::' . $propery_method . ' does not exists.');
                }
                
            }
        }
    }

}
