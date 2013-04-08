<?php
namespace Cogipix\CogimixYoutubeBundle\ViewHooks\Widget;
use Cogipix\CogimixCommonBundle\ViewHooks\Widget\WidgetRendererInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
class WidgetRenderer implements WidgetRendererInterface
{


    public function getWidgetTemplate()
    {
        return 'CogimixYoutubeBundle:Widget:widget.html.twig';
    }

    public function getParameters(){
        return array();
    }

}
