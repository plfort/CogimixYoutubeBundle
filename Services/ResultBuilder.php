<?php
namespace Cogipix\CogimixYoutubeBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;

class ResultBuilder{


    public function createFromVideoEntry($videoEntry){

        $item = null;
        if($videoEntry !== null ){
            $item = new TrackResult();
            $item->setEntryId($videoEntry->getVideoId());
            if(strstr($videoEntry->getVideoTitle(),'-' )!==false){
                $artistTitle = explode('-', $videoEntry->getVideoTitle());
                $item->setArtist(trim($artistTitle[0]));
                $item->setTitle(trim($artistTitle[1]));
            }else{
                $item->setTitle($videoEntry->getVideoTitle());

            }
            $videoThumbnails = $videoEntry->getVideoThumbnails();
            $thumbnails=array();
            if(!empty($videoThumbnails) && array_key_exists(0, $videoThumbnails)){
                $thumbnails=$videoThumbnails[0]['url'];
            }else{
                $thumbnails=null;
            }
            $item->setThumbnails($thumbnails);
            $item->setTag($this->getResultTag());
            $item->setIcon($this->getDefaultIcon());
        }

        return $item;
    }

    public function createFromVideoEntries($videosEntries){

        $result = array();
        foreach($videosEntries as $videoEntry){
            $item = $this->createFromVideoEntry($videoEntry);
            if($item !== null ){
                $result[] = $item;
            }
        }

        return $result;
    }


    public function getDefaultIcon(){
        return 'bundles/cogimixyoutube/images/yt-icon.png';
    }

    public function getResultTag(){
        return 'yt';
    }
}