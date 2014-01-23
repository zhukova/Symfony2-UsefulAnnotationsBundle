<?php
namespace Umbrellaweb\Bundle\UsefulAnnotationsBundle\Listener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Umbrellaweb\Bundle\UsefulAnnotationsBundle\Annotation\CsrfProtector;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class CsrfProtectorListener {

    private $reader;
    private $csrfProvider;

    public function __construct(Reader $reader,CsrfProviderInterface $csrfProvider)
    {
        $this->reader = $reader;
        $this->csrfProvider = $csrfProvider;
    }

    /**
     * This event will fire during any controller call
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController()))
        { 
            return;
        }
        
        $method = new \ReflectionMethod($controller[0], $controller[1]);

        $request = $event->getRequest();
        
        foreach ($this->reader->getMethodAnnotations($method) as $configuration)
        {
        	/**
        	 * If controller method marked as @CsrfProtector 
        	 * then validate the token using intention and name
        	 */
            if ($configuration instanceof CsrfProtector)
            {
		    	//validate the CSRF token
		    	if (FALSE === $this->csrfProvider->isCsrfTokenValid($configuration->intention, $request->get($configuration->name)))
		    	{
		    		throw new \RuntimeException('CSRF attack detected.');
		    	}
            }
        }
    }
}
