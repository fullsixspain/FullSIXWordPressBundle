<?php

namespace FullSIX\Bundle\WordPressBundle;

use FullSIX\Bundle\WordPressBundle\WordPressResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Used to have wordpress handle the default page (a page not handled by symfony)
 */
class WordPress404Listener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $event->setResponse(new WordPressResponse());
    }
}
