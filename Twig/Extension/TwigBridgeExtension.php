<?php

namespace FullSIX\Bundle\WordPressBundle\Twig\Extension;

class TwigBridgeExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'twigbridge';
    }

    public function getGlobals()
    {
        return array('wp' => new TwigBridge());
    }
}

class TwigBridge
{
    public function __call($function, $arguments)
    {
        if (!function_exists($function)) {
            trigger_error('Call to unexisting function ' . $function, E_USER_ERROR);
            return null;
        }
        return call_user_func_array($function, $arguments);
    }
}
