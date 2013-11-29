<?php
class Match {
    const ACTIVE = 1;
    const U1WON = 2;
    const U2WON = 3;

    // Dimensions of the game board, and number of consecutive pieces required for a win.
    const BOARD_WIDTH = 7;
    const BOARD_HEIGHT = 6;
    const NUM_CONSECUTIVE = 4;

    public $id;

    public $user1_id;
    public $user2_id;

    public $match_status_id = self::ACTIVE;

    // Represent the game board as a list of stacks of ints. 0s belong to p1,
    // 1s belong to p2.
    public $board_state;

    public function __construct() {
        $this->initialize_board();
    }

    public function initialize_board() {
        $board = array();
        // Initialize the board as array of 7 stacks, representing the columns.
        for ($i = 0; $i < self::BOARD_WIDTH; $i++) {
            $board[] = array();
        }

        $board_state = base64_encode(serialize($board));
    }

    /**
     * Drop the corresponding player's disk into the indicated column, and
     * return whether this move has caused the player to win the game.
     * @param int $player
     * @param int $column
     */
    public function drop_disk(int $player, int $column) {
        if ($column < 0 or $column >= self::BOARD_WIDTH) {
            return false;
        }

        $win = false;

        if ($this->match_status_id == self::ACTIVE) {
            $board = unserialize(base64_decode($this->board_state));
            	
            if (count($board[$column]) < self::BOARD_HEIGHT) {
                // Can only fit 6 disks per column.
                $board[$column]->push($player);

                $win = base::check_victory_state($board, $column);
            }
            	
            $this->board_state = base64_encode(serialize($board_state));
        }

        return $win;
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
                + self::count_consecutive_disks($board, $column, $row, 1, 0, $player) >= self::NUM_CONSECUTIVE - 1)
                or (self::count_consecutive_disks($board, $column, $row, 0, -1, $player)
                        + self::count_consecutive_disks($board, $column, $row, 0, 1, $player) >= self::NUM_CONSECUTIVE - 1)
                or (self::count_consecutive_disks($board, $column, $row, -1, -1, $player)
                        + self::count_consecutive_disks($board, $column, $row, 1, 1, $player) >= self::NUM_CONSECUTIVE - 1)
                or (self::count_consecutive_disks($board, $column, $row, -1, 1, $player)
                        + self::count_consecutive_disks($board, $column, $row, 1, -1, $player) >= self::NUM_CONSECUTIVE - 1));
    }

    private static function count_consecutive_disks($board, $col, $row, $dx, $dy, $player) {
        if ($board[$col][$row] != $player) {
            return 0;
        } else if ($col + $dx >= 0 and $col + $dx < self::BOARD_WIDTH) {
            // Get the height of the specified column. Should always be <= BOARD_HEIGHT
            $i = count($board[$col + $dx]);
            if ($row + $dy >= 0 and $row + $dy < $i) {
                return 1 + count_consecutive_disks($board, $col + $dx, $row + $dy, $dx, $dy, $player);
            }
        }
        return 1;
    }

}