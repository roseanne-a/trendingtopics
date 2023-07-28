<?php

namespace Rosey\TrendingTopics\Cron;

class TrendingTopics
{
	public static function runTrendingTopics()
    {
        $option = \XF::options();
        $limit = $option->mostTTLimit;
        $db = \XF::db();

        $timestamp = time() - 3600;

        $new_top_posts = $db->fetchAll("SELECT post_id, sum(number_of_posts) as number_of_posts, sum(number_of_reactions) as number_of_reactions from ((select post_id, count(*) as number_of_posts, 0 as number_of_reactions from xf_post WHERE post_date >= ? group by post_id ) union (select content_id as post_id, 0 as number_of_posts, count(*) as number_of_reactions from xf_reaction_content WHERE reaction_date >= ? AND content_type = 'post' group by post_id ) ) t12 group by post_id;", [$timestamp, $timestamp]);

        $old_top_posts = $db->fetchAll("SELECT * from xf_rosey_trending_topics");

        $newTotals = [];
        
        $newArr = [];
        $allkeys = [];
        $restrictedForums = [30, 31, 120, 56, 57, 58, 59, 60, 79, 91, 128];

        if(isset($new_top_posts)){
            foreach ($new_top_posts as $post){
                $threadFinder = \XF::finder('XF:Post')->where('post_id', $post["post_id"])->fetchOne();
                $thread = \XF::finder('XF:Thread')->where('thread_id', $threadFinder["thread_id"])->fetchOne();
                
                if(in_array($thread["node_id"], $restrictedForums)){
                    continue;
                }
                else if(isset($newTotals[$threadFinder["thread_id"]])){
                    //$newTotals[$threadFinder["thread_id"]]["parent_node_id"] = $categoryFinder->node_id;
                    $newTotals[$threadFinder["thread_id"]]["number_of_posts"] = $post["number_of_posts"];
                    $newTotals[$threadFinder["thread_id"]]["number_of_reactions"] = $post["number_of_reactions"];
                }
                else{
                    
                    $newTotals[$threadFinder["thread_id"]]["thread_id"] = $threadFinder["thread_id"];
                    $newTotals[$threadFinder["thread_id"]]["number_of_posts"] = $post["number_of_posts"];
                    $newTotals[$threadFinder["thread_id"]]["number_of_reactions"] = $post["number_of_reactions"];
                        
    
                }
    
            }
    
            $newresult = [];

            foreach($newTotals as $threadID => $result){
                $key = array_search($threadID, array_column($old_top_posts, 'thread_id')); 
                
                if($key !== false) {
                    $old_top_posts[$key] = $result;
                }
                else{
                    $newresult[] = $result;
          
                }

                $newArr = array_merge($newresult, $old_top_posts);
            }

            foreach($newArr as $key => $post){
                $newArr[$key]["total"] = $post["number_of_posts"] + $post["number_of_reactions"];
            }

            $total = array_column($newArr, 'total');
            array_multisort($total, SORT_DESC, $newArr);
            $output = array_slice($newArr, 0, $limit, true);
            $db->query("TRUNCATE TABLE xf_rosey_trending_topics");

            foreach($output as $key => $post){

                $db->query("INSERT INTO xf_rosey_trending_topics (thread_id, number_of_reactions, number_of_posts, total) VALUES (?, ?, ?, ?)", [$post["thread_id"], $post["number_of_reactions"], $post["number_of_posts"], $post["total"]]);
            }
        }
        else $output = $old_top_posts;
    }
}