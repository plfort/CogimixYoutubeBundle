<?php
namespace Cogipix\CogimixYoutubeBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;
use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;
use Madcoda\Youtube;

class YoutubeMusicSearch extends AbstractMusicSearch
{

    private $youtubeService;

    private $videoQuery;

    private $resultBuilder;

    public function __construct(ResultBuilder $resultBuilder,$youtubeApiKey)
    {
        $this->resultBuilder = $resultBuilder;
        $this->youtubeService = new Youtube(array('key'=>$youtubeApiKey));
    }

    protected function parseResponse($feeds)
    {
        $result = array();
        foreach ($feeds as $feed) {
            $item = $this->resultBuilder->createFromVideoEntry($feed);
            $result[] = $item;
        }
        $this->logger->info('Youtube return ' . count($result) . ' results');
        return $result;
    }

    protected function executeQuery()
    {
        $this->logger->info('Youtube executeQuery');
        $videos = array();
        try {
            $params = array(
                'q' => $this->searchQuery->getSongQuery(),
                'type' => 'video',
                'part' => 'id, snippet',
                'maxResults' => 30,
            );

            $params['order'] = 'relevance';


            $videos = $this->youtubeService->searchAdvanced($params);

        } catch (\Exception $ex) {
            $this->logger->err($ex);
            return array();
        }

        return $this->parseResponse($videos);
    }

    protected function executePopularQuery()
    {
        $feeds = array();
//         $videoQuery = new \Zend_Gdata_YouTube_VideoQuery();

//         $videoQuery->setCategory('Music');
//         $videoQuery->setFeedType('most viewed');
//         $videoQuery->setMaxResults(50);

//         $this->logger->info('Youtube executePopularQuery');
//         try {
//             $feeds = $this->youtubeService->getMostViewedVideoFeed($videoQuery);
//         } catch (\Exception $ex) {
//             $this->logger->err($ex);
//             return array();
//         }

        return $this->parseResponse($feeds);
    }

    protected function buildQuery()
    {
       // $this->videoQuery = new \Zend_Gdata_YouTube_VideoQuery();

        //$this->videoQuery->setCategory('music');
      //  $this->videoQuery->setOrderBy('relevance');
       // $this->videoQuery->setVideoQuery();
    }

    public function getName()
    {
        return 'Youtube';
    }

    public function getAlias()
    {
        return 'ytservice';
    }

    public function getDefaultIcon()
    {
        return '/bundles/cogimixyoutube/images/yt-icon.png';
    }

    public function getResultTag()
    {
        return 'yt';
    }
}

?>