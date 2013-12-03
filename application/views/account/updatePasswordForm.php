<h2>Change Password</h2>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo form_open('account/updatePassword');
	echo form_label('Current Password'); 
	echo form_error('oldPassword');
	echo form_password('oldPassword',set_value('oldPassword'),"required");
	echo form_label('New Password'); 
	echo form_error('newPassword');
	echo form_password('newPassword','',"id='pass1' required");
	echo form_label('Password Confirmation'); 
	echo form_error('passconf');
	echo form_password('passconf','',"id='pass2' required oninput='checkPassword();'");
	echo form_submit('submit', 'Change Password');
	echo form_close();
?>