<?php
namespace Cogipix\CogimixYoutubeBundle\ViewHooks\Menu;
use Cogipix\CogimixCommonBundle\ViewHooks\Menu\MenuItemInterface;
use Cogipix\CogimixCommonBundle\ViewHooks\Menu\AbstractMenuItem;

/**
 *
 * @author plfort - Cogipix
 *
 */
class MenuItem extends AbstractMenuItem
{

    public function getMenuItemTemplate()
    {
          return 'CogimixYoutubeBundle:Menu:menu.html.twig';

    }

	 /* (non-PHPdoc)
	  * @see \Cogipix\CogimixCommonBundle\ViewHooks\Menu\MenuItemInterface::getName()
	  */
	 public function getName() {
	 	return 'youtube';

	 }

}