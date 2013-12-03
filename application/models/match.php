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

    public function initialize_board() {
        $board = new Board_model();
        $board->initialize_columns(self::BOARD_WIDTH);

        $this->board_state = base64_encode(serialize($board));
    }

    /**
     * Drop the corresponding player's disk into the indicated column, and
     * return whether this move was successfully completed.
     * @param int $player
     * @param int $column
     */
    public function drop_disk($player, $column) {
        if ($column < 0 or $column >= self::BOARD_WIDTH) {
            return FALSE;
        }

        if ($this->match_status_id == self::ACTIVE) {
            $board = unserialize(base64_decode($this->board_state));
            
            if ($board->player_turn != $player) {
                return FALSE;
            }           
             	
            if (count($board->columns[$column]) < self::BOARD_HEIGHT) {
                // Can only fit 6 disks per column.
                $board->columns[$column][] = $player;
            }
            
            // Alternate player's turn.
            $board->player_turn = ($board->player_turn == Board_model::P1) ? Board_model::P2 : Board_model::P1;
            	
            $this->board_state = base64_encode(serialize($board));
        }

        return TRUE;
    }

    /**
     * Return whether the disk at the top of the presented column in the given
     * board is part of a winning board configuration.
     * @param unknown $board
     * @param unknown $column
     */
    public function check_victory_state($column) {
        $board = unserialize(base64_decode($this->board_state))->columns;
        
        $row = count($board[$column]) - 1;
        $player = $board[$column][$row];

        // Determine if at least 3 other disks are adjacent to this one.
        return ((self::count_consecutive_disks($board, $column - 1, $row, -1, 0, $player) // Horizontal check.
                + self::count_consecutive_disks($board, $column + 1, $row, 1, 0, $player) >= self::NUM_CONSECUTIVE - 1)
              or (self::count_consecutive_disks($board, $column, $row - 1, 0, -1, $player) // Vertical check.
                + self::count_consecutive_disks($board, $column, $row + 1, 0, 1, $player) >= self::NUM_CONSECUTIVE - 1)
              or (self::count_consecutive_disks($board, $column - 1, $row - 1, -1, -1, $player) // Upwards (/) check.
                + self::count_consecutive_disks($board, $column + 1, $row + 1, 1, 1, $player) >= self::NUM_CONSECUTIVE - 1)
              or (self::count_consecutive_disks($board, $column - 1, $row + 1, -1, 1, $player) // Downwards (\) check.
                + self::count_consecutive_disks($board, $column + 1, $row - 1, 1, -1, $player) >= self::NUM_CONSECUTIVE - 1));
    }

    private static function count_consecutive_disks($board, $col, $row, $dx, $dy, $player) {
        // No consecutive disks if $col, $row out of bounds or the piece on the board is not the player's
        if ($col < 0 or $col >= self::BOARD_WIDTH
                or $row < 0 or $row >= count($board[$col])
                or $board[$col][$row] != $player) {
            return 0;
        }
        
        if ($col + $dx >= 0 and $col + $dx < self::BOARD_WIDTH) {
            // Get the height of the specified column. Should always be <= BOARD_HEIGHT
            $i = count($board[$col + $dx]);
            if ($row + $dy >= 0 and $row + $dy < $i) {
                return 1 + self::count_consecutive_disks($board, $col + $dx, $row + $dy, $dx, $dy, $player);
            }
        }
        // If the next adjacent piece going in this direction goes out of bounds, return only one.
        return 1;
    }

}