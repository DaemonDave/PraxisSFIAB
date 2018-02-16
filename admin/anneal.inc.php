<?php


class annealer {

	var $num_buckets;
	var $bucket;
	var $bucket_cost;
	var $bucket_cost_new;
	var $cost;
	var $start_temp, $start_moves;
	var $cost_function_callback;
	var $pick_move_callback;
	var $update_callback;
	var $delta_cost_bucket_ids_callback;
	var $iterations;
	var $items_per_bucket;
	var $max_items_per_bucket;
	var $rate;
	var $move_bucket_ids;
	
	function annealer($num_buckets, $start_temp, $start_moves, $rate,
				$cost_function_cb, $items)
	{
		$this->num_buckets = $num_buckets;
		$this->start_temp = $start_temp;
		$this->start_moves = $start_moves;
		$this->cost_function_callback = $cost_function_cb;
		unset($this->pick_move_callback);
		unset($this->update_callback);
		unset($this->delta_cost_bucket_ids_callback);
	
		$this->bucket_cost = array();
		$this->bucket_cost_old = array();
		$this->bucket = array();
		$this->iterations = 0;
		$this->items_per_bucket = ceil(count($items) / $num_buckets);
		$this->rate = $rate;
		$this->max_items_per_bucket = 0;
		$ipb = ceil($this->items_per_bucket);
		$i=0;
		$this->cost = 0;
		/* Assign to buckets */
		for($x=0; $x<$num_buckets; $x++) {
			unset($b);
			$b = array();
			for($y=0;$y<$ipb; $y++) {
				if($i == count($items)) break;
				$b[] = $items[$i];
				$i++;
			}
			$this->bucket[] = $b;
		}

		/* Then do costs after all bucket assignments are done */
		for($x=0; $x<$num_buckets; $x++) {
			$c = $this->cost_function($x);
			$this->bucket_cost[] = $c;
			$this->cost += $c;
		}
		TRACE("Annealer setup: T={$this->start_temp}, ".
				"M={$this->start_moves}, Bkts={$this->num_buckets}, ".
				"Cost={$this->cost}\n");
	}

	function set_pick_move($func)
	{
		$this->pick_move_callback = $func;
	}
	function set_update_callback($func)
	{
		$this->update_callback = $func;
	}
	function set_delta_cost_bucket_ids_callback($func)
	{
		$this->delta_cost_bucket_ids_callback = $func;
	}
	function set_max_items_per_bucket($num) 
	{
		$this->max_items_per_bucket = $num;
	}



	function pick_move()
	{
		/* Pick a bucket and item */
		while(1) {
			$b1 = rand(0, $this->num_buckets - 1);
			if(count($this->bucket[$b1]) > 0) break;
		}
		$i1 = rand(0, count($this->bucket[$b1]) -1);

		/* Pick a second bucket that is different than the first */
		$b2 = rand(0, $this->num_buckets - 2);
		if($b2 >= $b1) $b2++;

		
		if($this->max_items_per_bucket > 0 && count($this->bucket[$b2]) >= $this->max_items_per_bucket) {
			/* Can't move b1 into b2, it would exceed the max items per bucket, pick an
			 * item to swap with */
			$i2 = rand(0, count($this->bucket[$b2])-1);
		} else {
			/* Pick an item, or a blank, in the second bucket */
			$i2 = rand(0, count($this->bucket[$b2]));
			if($i2 == count($this->bucket[$b2])) $i2 = -1;
		}
//		TRACE("Move ($b1,$i1)<->($b2,$i2)\n");
		return array($b1, $i1, $b2, $i2);
	}
	
	function cost_function($b)
	{
		$bkt = $this->bucket[$b];
		$cb = $this->cost_function_callback;
		$c = $cb($this, $b, $bkt);
//		$this->print_bucket($b);
//		print("Computed cost to be: $c\n");
		return $c;
	}
	
	function compute_delta_cost($move) 
	{
		list($b1, $i1, $b2, $i2) = $move;

		if($b1 == $b2) {
			echo "Called on same bucket, not supported!\n";
			exit;
//			return $this->compute_delta_cost_same_bucket($move);
		}

		$cost = 0;

		/* Save the old lists for easy restore */
		$b1_old = $this->bucket[$b1];
		$b2_old = $this->bucket[$b2];

		/* Setup new costs */
		$this->bucket_cost_new = $this->bucket_cost;
		
		/* Compute new lists with swapped elements */
		if($i2 != -1) { /* Swap */
			array_splice($this->bucket[$b1], $i1, 1, $b2_old[$i2]);
			array_splice($this->bucket[$b2], $i2, 1, $b1_old[$i1]);
		} else { /* Move one to other */
			array_splice($this->bucket[$b1], $i1, 1);
			$this->bucket[$b2][] = $b1_old[$i1];
		}

		/* Get the lists of buckets we need to recompute, by default
		 * just b1 and b2 */
		if(isset ($this->delta_cost_bucket_ids_callback)) {
			$cb = $this->delta_cost_bucket_ids_callback;
			$ids = $cb($this, $b1);
			$ids = array_unique(array_merge($ids, $cb($this, $b2)), SORT_NUMERIC );
		} else {
			$ids = array($b1, $b2);
		}

//		TRACE("Recompute IDs:\n");
//		TRACE_R($ids);
		

		/* Save that list */
		$this->move_bucket_ids = $ids;

		/* Compute a delta cost, recompute all costs for all buckets */
		foreach($ids as $bucket_id) {
			/* Compute costs */
			$cost -= $this->bucket_cost[$bucket_id];
			$this->bucket_cost_new[$bucket_id] = $this->cost_function($bucket_id);
			$cost += $this->bucket_cost_new[$bucket_id];
		}


		/* Save the new lists */
		$b1_new = $this->bucket[$b1];
		$b2_new = $this->bucket[$b2];

		/* Return to the original bucket lists */
		$this->bucket[$b1] = $b1_old;
		$this->bucket[$b2] = $b2_old;
	
		return array($cost, array($b1_new, $b2_new));
	}
/*
	function compute_delta_cost_same_bucket($move)
	{
		list($b1, $i1, $b2, $i2) = $move;

		$cost = 0;

		$b_old = $this->bucket[$b1];
		
		$b_new = array();
		/* Make a new bucket list 
		for($x=0; $x<count($b_old); $x++) {
			if($x == $i1) {
				/* Swap or remove this index 
				if($i2 != -1) $b_new[] = $b_old[$i2];
			} else if($x == $i2) {
				$b_new[] = $b_old[$i1];
			} else {
				$b_new[] = $b_old[$x];
			}
		}

		/* Assign the new item lists to the buckets 
		$this->bucket[$b1] = $b_new;

		/* Compute costs 
		$cost -= $this->bucket_cost[$b1];

		$c1 = $this->cost_function($b1);
		$cost += $c1;

		/* Return to the original bucket lists 
		$this->bucket[$b1] = $b_old;
	
		return array($cost, array($c1, $b_new, 0, array()));
	}
*/

	function accept_move($move, $movedata)
	{
		list($b1, $i1, $b2, $i2) = $move;
		list($b1_new, $b2_new) = $movedata;

		$this->bucket[$b1] = $b1_new;
		if($b1 != $b2) $this->bucket[$b2] = $b2_new;

		$this->bucket_cost = $this->bucket_cost_new;
	}

	function anneal()
	{
		$temperature = $this->start_temp;
		$current_cost = $this->cost;
		$last_cost = 0;
		$last_cost_count = 0;

		if($this->num_buckets <= 1) {
			TRACE("Only one Bucket, nothing to anneal.\n");
			return;
		}
//		$this->print_buckets();
		$estimated_iterations = ceil(log(0.1 / $this->start_temp, $this->rate));

//		print_r($this);
		$iterations = 0;
		while(1) {
			$moves = $this->start_moves;
			for($m = 0; $m<$moves; $m++) {
//				$this->print_buckets();
				/* Pick 2 moves at random */
				if(isset ($this->pick_move_callback)) {
					$cb = $this->pick_move_callback;
					$move = $cb($this);
				} else {
					$move = $this->pick_move();
				}
				/* See what the new cost is compared to the old */
				list($delta_c, $movedata) = 
					$this->compute_delta_cost($move);


				$r = floatval(rand()) / floatval(getrandmax());
				/* Decide if we want to keep it */
				$e = exp(-$delta_c / $temperature);
//				TRACE("r=$r, exp=$e, delta=$delta_c\n");
				if($r < exp(-$delta_c / $temperature)) {
					/* Yes, we do, record the move */
					$this->accept_move($move, $movedata);
					$current_cost += $delta_c;
					$n_accepted++;
				//	if($current_cost < $this->cost)
					$this->cost = $current_cost;

//					TRACE("Move accepted, cost=$current_cost\n");
				} else {
//					TRACE("Move rejected\n");
				}
				$this->iterations++;
				if($this->iterations % 10000 == 0) {
					TRACE("   {$this->iterations} iterations, cost={$this->cost}, temperature=$temperature\n");
//					$this->print_buckets();
				}

				if($this->cost == 0) {
					/* If we manage to get to a 0 cost
					 * solution, don't look any more */
					break;
				}
			}
			$iterations++;

			if(isset ($this->update_callback)) {
				$cb = $this->update_callback;
				$cb($iterations, $estimated_iterations);
			}
			if($this->cost == 0) break;

			if($this->cost == $last_cost) {
				$last_cost_count ++;
			} else {
				$last_cost = $this->cost;
				$last_cost_count=0;
			}
			
			if($temperature < 0.1 && $last_cost_count > 10) 
				break;
//			TRACE("Cost is {$this->cost}\n");
			$temperature *= $this->rate;
            /*
FIXME: README: NOTE: TODO:
From Kris, 2009-03-24
Dave do you think we should consider something like this?

<Kris_School_1> here's the schedule i use in my academic annealer:
                 if( _params._useVPRTempSchedule ) {
                         // This is VPR's temperature schedule...
                         if( successRate > 0.96 ) {
                                 _temp *= 0.5;
                         } else if( successRate > 0.8 ) {
                                 _temp *= 0.9;
                         } else if( successRate > 0.15 || !windowsSized ) {
                                 _temp *= 0.95;
                         } else {
                                 _temp *= 0.8;
                         }
                 } else {
                         // This is identical to Aaarts and Van Laarhaven.
                         real64  kappa = _params._tempReduction;         // 1.0 == slow, 10 = reasonable, 100 == fast
                         real64  sqrvar = std::sqrt( variance );
                         if( variance <= EPSNEG || sqrvar <= EPSNEG ) {
                                 _temp = 0.;
                         } else {
                                 _temp = _temp * ( sqrvar / ( sqrvar + kappa * _temp ) );
                         }
                 }
            */
		}
		TRACE("Annealing complete.  {$this->iterations} iterations.  Final cost is {$this->cost}\n");
	}

	function print_bucket($x)
	{
		$b = $this->bucket[$x];
		print("Bucket $x: (cost: {$this->bucket_cost[$x]})\n");
		print("   ");
		for($y=0;$y<count($b); $y++) {
			print("{$b[$y]} ");
		}
		print("\n");
	}
	function print_buckets()
	{
		for($x=0; $x<$this->num_buckets; $x++) {
			$this->print_bucket($x);
		}
	}
}
?>
