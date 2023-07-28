<?php

namespace Rosey\TrendingTopics\Widget;

use \XF\Widget\AbstractWidget;

class TrendingTopics extends AbstractWidget
{
    public function render()
    {
	

        $threads = [];

		$db = \XF::db();

		$topPosts = $db->fetchAll("SELECT * from xf_rosey_trending_topics");


        foreach($topPosts as $post){
            $thread = \XF::finder('XF:Thread')->where('thread_id', $post["thread_id"])->fetchOne();
            $threads[] = $thread;

        }

		// prepare viewParams
		$viewParams = [
			'topPosts' => $threads,
		];
		
		// send to widget
		return $this->renderer('trending_topics_test', $viewParams);
    }

	public function getOptionsTemplate()
	{
	   return null;
	}
}