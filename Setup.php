<?php

namespace Rosey\TrendingTopics;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;
	
	public function install(array $stepParams = [])
	{
	    $this->schemaManager()->createTable('xf_rosey_trending_topics', function(Create $table)
	    {
	        $table->addColumn('order_id', 'int')->autoIncrement(true);
	        $table->addColumn('thread_id', 'int');
	        $table->addColumn('number_of_reactions', 'int');
	        $table->addColumn('number_of_posts', 'int');	
	        $table->addColumn('total', 'int');

	        $table->addPrimaryKey('order_id');
	        
	    });
		
		$this->createWidget(
		    'trendingtopics',
            'trendingtopics',
            [
                'positions' => [

                    'forum_list_sidebar' => 10

                ]
            ]
        );
	
	}

	public function upgrade(array $stepParams = [])
	{
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array $stepParams = [])
	{
		$this->schemaManager()->dropTable('xf_rosey_trending_topics');
	    $this->deleteWidget('trendingtopics');
	}
}