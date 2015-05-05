<?php
namespace Cogipix\CogimixYoutubeBundle\Services;


use Cogipix\CogimixCommonBundle\Model\ParsedUrl;

use Cogipix\CogimixYoutubeBundle\Services\ResultBuilder;
use Cogipix\CogimixCommonBundle\MusicSearch\UrlSearcherInterface;
use Madcoda\Youtube;

class YoutubeUrlSearch implements UrlSearcherInterface
{
    private $regexHost = '#^(?:www\.)?(?:youtu\.be|youtube\.com)#';
    private $resultBuilder;
    private $youtubeService;

    public function __construct(ResultBuilder $resultBuilder,$youtubeApiKey){
        $this->resultBuilder = $resultBuilder;
        $this->youtubeService = new Youtube(array('key'=>$youtubeApiKey));
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

            $videoEntry = null;
            if($match == 'youtu.be' && isset($url->path[0])){

                $videoEntry = $this->youtubeService->getVideoInfo($url->path[0]);
            }else{
                if($url->path[0] == 'embed'){

                    $videoEntry=$this->youtubeService->getVideoInfo(end($url->path));
                }else{
                    if(isset($url->query['v'])){
                        $videoEntry = $this->youtubeService->getVideoInfo($url->query['v']);
                    }
                }

            }
            return  $this->resultBuilder->createFromVideoEntry($videoEntry);
        }else{
            return null;
        }


    }

}
