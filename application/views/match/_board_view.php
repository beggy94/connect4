<?php if ($player_no == $board->player_turn) {
    echo "<h3>It is your turn.</h3>";
} else {
    echo "<h3>Waiting for the other player.</h3>";
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
                $color = $column[$i] == 1 ? "black" : "red";
                echo "<td class='p$column[$i]'><div class='chip-$color'></div></td>";
            } else {
                echo "<td><div class='empty-square'></div></td>";
            }
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
