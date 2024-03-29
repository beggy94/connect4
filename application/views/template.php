<!DOCTYPE html>

<html lang="en">
	<head>
        <title><?= $title ?></title>
        <link href="<?= base_url("css/template.css") ?>" rel="stylesheet" />
    	<script src="http://code.jquery.com/jquery-latest.js"></script>
    	<script src="<?= base_url("js/jquery.timers.js") ?>"></script>
    	<?php if (isset($script)) { ?><script><?php $this->load->view($script, $data) ?></script> <?php } ?>
	</head>
	<body>
	    <header>
	        <?php $this->load->view("header"); ?>
	    </header>
	    <div class="container">
	        <?= anchor(base_url(), "Home") ?>
	        <div id="main">
	            <?php $this->load->view($main); ?>
	        </div>
	    </div>
	    <footer>
	        <?php $this->load->view("footer"); ?>
	    </footer>
	</body>
</html>