<h1>Connect 4 Lobby</h1>

<div>
Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
</div>
	
<?php 
	if (isset($errmsg)) 
		echo "<p>$errmsg</p>";
?>
<h2>Available Users</h2>
<div id="availableUsers">
</div>