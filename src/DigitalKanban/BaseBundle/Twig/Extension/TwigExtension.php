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
                'sec2duration' => new \Twig_Filter_Method($this,
                        'formatDuration'),
                'splitgroup' => new \Twig_Filter_Method($this, 'splitGroup'),
        );
    }

    public function formatDuration($durationInSec)
    {
        return gmdate("H:i:s", $durationInSec);
    }

    public function splitGroup($text)
    {
        //manage groups based on # separator
        $tabstr = explode('#', $text);

        if (count($tabstr) > 1) {
            $ret = '';
            if (array_key_exists(1, $tabstr)) {
                $ret .= '<div class="group1">' . $tabstr[0] . '</div>';
                if (array_key_exists(2, $tabstr)) {
                    $ret .= '<div class="group2">' . $tabstr[1] . '</div>';
                    if (array_key_exists(3, $tabstr)) {
                        $ret .= '<div class="group3">' . $tabstr[2] . '</div>';
                    }
                }
            }
            $ret .= '<div class="group0">' . $tabstr[count($tabstr) - 1]
                    . '</div>';
        } else {
            $ret = $text;
        }

        return $ret;
    }
}
