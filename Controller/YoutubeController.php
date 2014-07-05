<?php
namespace Cogipix\CogimixYoutubeBundle\Controller;



use Symfony\Component\HttpFoundation\Response;


use JMS\SecurityExtraBundle\Annotation\Secure;


use Symfony\Component\HttpFoundation\Session\Session;

use Cogipix\CogimixCommonBundle\Utils\AjaxResult;

use Symfony\Component\HttpFoundation\Request;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Cookie;
/**
 * @Route("/youtube")
 * @author plfort - Cogipix
 *
 */
class YoutubeController extends Controller
{
    /**
     * @Route("/quality/{quality}",name="_youtube_set_quality",options={"expose"=true})
     */
    public function getSongAction(Request $request, $quality)
    {
       $response = new Response();
       $cookie = new Cookie('ytquality',$quality);
       $response->headers->setCookie($cookie);
       return $response;
    }

}
