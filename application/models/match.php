<?php
class Match {
	const ACTIVE = 1;
	const U1WON = 2;
	const U2WON = 3;
	
	public $id;
	
	public $user1_id;  
	public $user2_id;
	
	public $match_status_id = self::ACTIVE;
	
	// Represent the game board as a list of stacks of ints. 0s belong to p1,
	// 1s belong to p2.
	public $board_state;
	
	/**
	 * Drop the corresponding player's disk into the indicated column, and 
	 * return whether this move has caused the player to win the game. 
	 * @param int $player
	 * @param int $column
	 */
	public function drop_disk(int $player, int $column) {
		if ($column < 0 or $column >= 7) {
			return false;
		} 
		
		if ($this->match_status_id == self::ACTIVE) {
			$board = unserialize(base64_decode($this->board_state));
			
			if (count($board[$column]) < 6) {
				// Can only fit 6 disks per column.
				$board[$column]->push($player);
				
				$win = base::check_victory_state($board, $column);
			}
			
			$this->board_state = base64_encode(serialize($board_state));
		}
	}
	
	/**
	 * Return whether the disk at the top of the presented column in the given
	 * board is part of a winning board configuration.
	 * @param unknown $board
	 * @param unknown $column
	 */
	private static function check_victory_state($board, $column) {
		$row = count($board[$column]) - 1;
		$player = $board[$column][$row];
		
		// Determine if at least 3 other disks are adjacent to this one.
		return ((self::count_consecutive_disks($board, $column, $row, -1, 0, $player) 
				+ self::count_consecutive_disks($board, $column, $row, 1, 0, $player) >= 3)
			  or (self::count_consecutive_disks($board, $column, $row, 0, -1, $player)
			  	+ self::count_consecutive_disks($board, $column, $row, 0, 1, $player) >= 3)
			  or (self::count_consecutive_disks($board, $column, $row, -1, -1, $player)
			  	+ self::count_consecutive_disks($board, $column, $row, 1, 1, $player) >= 3)
			  or (self::count_consecutive_disks($board, $column, $row, -1, 1, $player)
			  	+ self::count_consecutive_disks($board, $column, $row, 1, -1, $player) >= 3));
	}
	
	private static function count_consecutive_disks($board, $col, $row, $dx, $dy, $player) {
		if ($board[$col][$row] != $player) {
			return 0;
		} else if ($col + $dx >= 0 and $col + $dx <= 6) {
			$i = count($board[$col + $dx]);
			if ($row + $dy >= 0 and $row + $dy < $i) {
				return 1 + count_consecutive_disks($board, $col + $dx, $row + $dy, $dx, $dy, $player);
			}
		}
		return 1;
	}
		
}