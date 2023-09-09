<?php

namespace slackbot\Test_Task;

use slackbot\Test_Task;

use WHMCS\Module\AbstractWidget;

use Illuminate\Support\Facades\App;

class Widget extends AbstractWidget {
	/**
	 * @type string
	 */
	protected $title = 'Test Task Stats';
	/**
	 * @type string
	 */
	protected $description = 'An overview of Test Task Stats.';
	/**
	 * @type int
	 */
	protected $weight = 10;
	/**
	 * @type int
	 */
	protected $columns = 2;
	/**
	 * @type bool
	 */
	protected $cache = true;
	/**
	 * @type int
	 */
	protected $cacheExpiry = 3600;
	/**
	 * @type bool
	 */
	protected $cachePerUser = false;
	/**
	 * @type string
	 */
	protected $requiredPermission = '';

	public function getId() {
        return str_replace("\\", "", get_class($this));
    }

	public function getData() {

		$test_task = Test_Task::get_instance();

		return [
			'tasks_queued'   => 123,
			'tasks_executed' => 321,
		];
	}

	public function generateOutput($options) {
		$data = $options['data'];
		ob_start();

		?>
		<div class="icon-stats">
			<div class="row">
				<div class="col-sm-6">
					<div class="item">
						<div class="icon-holder text-center">
							<i class="pe-7s-clock"></i>
						</div>
						<div class="data">
							<div class="note">
								Tasks Queued
							</div>
							<div class="number">
								123
							</div>
						</div>
					</div>
				</div>
                ...
    		</div>
        </div>
		<?php

		return ob_get_clean();
	}
}
