
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
<script>

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		var board = [];
		
		$(function(){
			$('body').everyTime(2000,function(){
				if (status == 'waiting') {
					$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
    					if (data && data.status=='rejected') {
    						alert("Sorry, your invitation to play was declined!");
    						window.location.href = '<?= base_url() ?>arcade/index';
    					}
    					if (data && data.status=='accepted') {
    						status = 'playing';
    						$('#status').html('Playing ' + otherUser);
    					}
							
					});
				}
				var url = "<?= base_url() ?>board/getMsg";
				$.getJSON(url, function (data,text,jqXHR){
					if (data && data.status=='success') {
						var conversation = $('[name=conversation]').val();
						var msg = data.message;
						if (msg.length > 0)
							$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
					}
				});
			}); 

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
			});	
		});
	
	</script>
</head> 
<body>  
	<h1>Game Area</h1>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
	</div>
	
	<div id='status'> 
	<?php 
		if ($status == "playing")
			echo "Playing " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div>
	
	<table class="game-board">
	    <tr>
	    <?php
	    $chip_color = ($player_no == 1 ? "red" : "black");
	    foreach ($board as $column) {
            $column_style = count($column) >= 6 ? "full-column" : "";
            echo "<th class='$column_style chip-$chip_color'>$chip_color $column_style</th>";
        }
        ?>
	    </tr>
	    <?php
	    for ($i = 5; $i >= 0; $i--) {
            echo "<tr>";
            foreach ($board as $column) {
                if (count($column) > $i) {
                    echo "<td class='p$column[$i]'>$column[$i]</td>";
                } else {
                    echo "<td class='empty-square'>Empty</td>";
                }
            }
            echo "</tr>";
        }
	    ?>
	</table>
	
<?php 
	
	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
	
	
	
	
</body>

</html>

