<?php if ($player_no == $board->player_turn) {
    echo "<h2>It is your turn.</h2>";
} else {
    echo "<h2>Waiting for the other player.</h2>";
} ?>
<table class="game-board">
    <tr>
    <?php
    $i = 0;
    foreach ($board->columns as $column) {
        $column_style = count($column) >= 6 ? "full-column" : "";
        echo "<th id='insert-disk-$i' class='$column_style chip-$chip_color'>" . "$chip_color $column_style" . "</th>";
        $i++;
    }    
    ?>
    </tr>
    <tbody>
    <?php
    for ($i = 5; $i >= 0; $i--) {
        echo "<tr>";
        foreach ($board->columns as $column) {
            if (count($column) > $i) {
                echo "<td class='p$column[$i]'>$column[$i]</td>";
            } else {
                echo "<td class='empty-square'>Empty</td>";
            }
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
