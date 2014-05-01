<?php
namespace Cogipix\CogimixYoutubeBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;
use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;

class YoutubeMusicSearch extends AbstractMusicSearch{

    private $youtubeService;
    private $videoQuery;
    private $resultBuilder;

    public function __construct(ResultBuilder $resultBuilder){
        $this->resultBuilder=$resultBuilder;
        $this->youtubeService=new \Zend_Gdata_YouTube();
        $this->youtubeService->getHttpClient()->setAdapter('Zend_Http_Client_Adapter_Curl');

    }

    protected function parseResponse($feeds){

        $result = array();
        foreach($feeds as $feed){
             $item= $this->resultBuilder->createFromVideoEntry($feed);
             $result[]=$item;
        }
        $this->logger->info('Youtube return '.count($result).' results');
        return $result;
    }

    protected function executeQuery(){
        $this->logger->info('Youtube executeQuery');
        $feeds = array();
        try{
            $feeds = $this->youtubeService->getVideoFeed($this->videoQuery);

        }catch(\Exception $ex){
            $this->logger->err($ex);
            return array();
        }

        return $this->parseResponse($feeds);

    }

    protected function executePopularQuery(){

        $videoQuery = new \Zend_Gdata_YouTube_VideoQuery();
       
        $videoQuery->setCategory('music');
        $videoQuery->setFeedType('most viewed');
        $this->logger->info('Youtube executePopularQuery');
        try{
             $feeds= $this->youtubeService->getVideoFeed($videoQuery);

        }catch(\Exception $ex){
            $this->logger->err($ex);
            return array();
        }

        return $this->parseResponse($feeds);

    }

    protected function buildQuery(){

        $this->videoQuery = new \Zend_Gdata_YouTube_VideoQuery();
      
        $this->videoQuery->setCategory('music');
        $this->videoQuery->setVideoQuery($this->searchQuery->getSongQuery());

    }

    public function  getName(){
        return 'Youtube';
    }

    public function  getAlias(){
        return 'ytservice';
    }

    public function getDefaultIcon(){
        return '/bundles/cogimixyoutube/images/yt-icon.png';
    }

    public function getResultTag(){
        return 'yt';
    }


}

?>