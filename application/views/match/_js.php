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
		url = "<?= base_url("board/getGameBoard") ?>";
		$("#game-area").load(url);
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

	$('.leave-game').click(function() {
		// TODO: Make use of board/leaveGame and have it also notify the other player.
	    var url = "<?= base_url("arcade/declineInvitation") ?>";
	    $.post(url, null, function(data, status, jqXHR) {
	        window.location.href = "<?= base_url("arcade/index") ?>";
	    });
	});

    <?php for ($i = 0; $i < count($board->columns); $i++) { ?>
        $("#game-area").on("click", "#insert-disk-<?= $i ?>", function() {
	        if (!$(this).hasClass("full-column")) {
	            var url = "<?= base_url("board/dropDisk/$i") ?>";
	            $.post(url, null, function(data, status, jqXHR) {
	                $("#game-area").load("<?= base_url("board/getGameBoard")?>");
	            });
	        }
        });
    <?php } ?>
});