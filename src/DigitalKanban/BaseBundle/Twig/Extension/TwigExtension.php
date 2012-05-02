<?php

namespace DigitalKanban\BaseBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;

class TwigExtension extends \Twig_Extension
{

    public function getName()
    {
        return 'digitalkanbantwigext';
    }

    public function getFilters()
    {
        return array(
            'sec2duration' => new \Twig_Filter_Method($this, 'formatDuration'),
        );
    }

    public function formatDuration($durationInSec)
    {
        return gmdate("H:i:s", $durationInSec);
    }

}
