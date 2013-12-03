<?php

/**
 * Data structure representing a Connect4 board.
 * @author g0tsimid
 *
 */
class Board_model {
    const P1 = 0;
    const P2 = 1;
    
    // Indicate which player's turn it is.
    public $player_turn = self::P1;
    // Indicate whether player 1 has seen an updated version of this board.
    public $p1_updated = TRUE;
    // Indicate whether player 2 has seen an updated version of this board.
    public $p2_updated = TRUE;
    // Keep an array of stacks, with each item representing a disk dropped on the board.
    public $columns = array();
    
    public function initialize_columns($num_columns) {
        for ($i = 0; $i < $num_columns; $i++) {
            $this->columns[] = array();
        }
    }
}