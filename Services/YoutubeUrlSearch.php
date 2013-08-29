<?php
namespace Cogipix\CogimixYoutubeBundle\Services;


use Cogipix\CogimixCommonBundle\Model\ParsedUrl;

use Cogipix\CogimixYoutubeBundle\Services\ResultBuilder;
use Cogipix\CogimixCommonBundle\MusicSearch\UrlSearcherInterface;

class YoutubeUrlSearch implements UrlSearcherInterface
{
    private $regexHost = '#^(?:www\.)?(?:youtu\.be|youtube\.com)#';
    private $resultBuilder;

    public function __construct(ResultBuilder $resultBuilder){
        $this->resultBuilder = $resultBuilder;
    }


    public function canParse($url)
    {
      if(is_array($url->path) && !in_array('playlist', $url->path) ){
          preg_match($this->regexHost, $url->host,$matches);
          return isset($matches[0]) ? $matches[0] : false;
      }
       return false;

    }

    public function searchByUrl(ParsedUrl $url)
    {

        if( ($match = $this->canParse($url)) !== false){

            $youtubeService = new \Zend_Gdata_YouTube();
            $youtubeService->getHttpClient()->setAdapter('Zend_Http_Client_Adapter_Curl');
            $videoEntry = null;
            if($match == 'youtu.be' && isset($url->path[0])){

                $videoEntry = $youtubeService->getVideoEntry($url->path[0]);
            }else{
                if($url->path[0] == 'embed'){

                    $videoEntry=$youtubeService->getVideoEntry(end($url->path));
                }else{
                    if(isset($url->query['v'])){
                        $videoEntry = $youtubeService->getVideoEntry($url->query['v']);
                    }
                }

            }
            return  $this->resultBuilder->createFromVideoEntry($videoEntry);
        }else{
            return null;
        }


    }

}
