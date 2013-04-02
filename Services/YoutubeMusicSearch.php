<?php
namespace Cogipix\CogimixYoutubeBundle\Services;

use Cogipix\CogimixBundle\Entity\TrackResult;
use Cogipix\CogimixBundle\Services\AbstractMusicSearch;

class YoutubeMusicSearch extends AbstractMusicSearch{

    private $youtubeService;
    private $videoQuery;

    public function __construct(){

        $this->youtubeService=new \Zend_Gdata_YouTube();
        $this->youtubeService->getHttpClient()->setAdapter('Zend_Http_Client_Adapter_Curl');

    }

    protected function parseResponse($feeds){

        $result = array();
        foreach($feeds as $feed){
            $item = new TrackResult();

            $item->setEntryId($feed->getVideoId());
            if(strstr($feed->getVideoTitle(),'-' )!==false){
                $artistTitle = explode('-', $feed->getVideoTitle());
                $item->setArtist(trim($artistTitle[0]));
                $item->setTitle(trim($artistTitle[1]));
            }else{
                $item->setTitle($feed->getVideoTitle());

            }

            //$item->setFlashUrl($feed->getFlashPlayerUrl());
            $videoThumbnails = $feed->getVideoThumbnails();
            $thumbnails=array();
            if(!empty($videoThumbnails) && array_key_exists(0, $videoThumbnails)){
                $thumbnails=$videoThumbnails[0]['url'];
            }else{
                $thumbnails=null;
            }
            $item->setThumbnails($thumbnails);
            $item->setTag($this->getResultTag());
            $item->setIcon($this->getDefaultIcon());
            $result[]=$item;
        }
        $this->logger->info('Youtube return '.count($result).' results');
        return $result;
    }

    protected function executeQuery(){
        $this->logger->info('Youtube executeQuery');
        try{
        $feeds = $this->youtubeService->getVideoFeed($this->videoQuery);

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
        return 'bundles/cogimixyoutube/images/yt-icon.png';
    }

    public function getResultTag(){
        return 'yt';
    }
}

?>