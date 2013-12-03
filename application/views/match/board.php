<h2>Game Area</h2>

<div>
Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
</div>

<div id='status'> 
<?php 
	if ($status == "playing")
		echo "Playing " . $otherUser->login;
	else
		echo "Waiting on " . $otherUser->login;
?>
</div>

<?php
if (isset($board)) {
    echo "<section id='game-area'>";
    $this->load->view("match/_board_view", array("board" => $board));
    if ($status == 'waiting') {
        echo "<p>Your game will begin once " . $otherUser->fullName() . " has accepted.</p>";
    }
    echo "</section>";
}
?>
	
<?php 
	
	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	echo anchor(current_url() . "#", "Leave Game", "class='leave-game'");
	
?>