<?php
//clearholds.php
//frees baggage that's been marked "hold" for over an hour
require_once('class.BagQueue.php');
$queue = new BagQueue();
$num_updates = $queue->clearHolds();
echo "$num_updates updated \n";