<?php
namespace Cogipix\CogimixYoutubeBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;

class ResultBuilder{


    public function createFromVideoEntry($videoEntry){

        $item = null;
        if($videoEntry !== null && is_object($videoEntry) ){
            $item = new TrackResult();
            if(is_object($videoEntry->id)){
                $item->setEntryId($videoEntry->id->videoId);
            }elseif(isset($videoEntry->id)){
                $item->setEntryId($videoEntry->id);
            }

            if(strstr($videoEntry->snippet->title,'-' )!==false){
                $artistTitle = explode('-', $videoEntry->snippet->title);
                $item->setArtist(trim($artistTitle[0]));
                $item->setTitle(trim($artistTitle[1]));
            }else{
                $item->setTitle($videoEntry->snippet->title);


            }
            $thumbnails = $videoEntry->snippet->thumbnails->medium->url;
            $item->setThumbnails($thumbnails);
            $item->setTag($this->getResultTag());
            $item->setIcon($this->getDefaultIcon());
            if(isset($videoEntry->contentDetails)){
                if(isset($videoEntry->contentDetails->videoDuration)){
                    $item->setDuration($videoEntry->contentDetails->videoDuration);
                }
            }
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
        return '/bundles/cogimixyoutube/images/yt-icon.png';
    }

    public function getResultTag(){
        return 'yt';
    }
}