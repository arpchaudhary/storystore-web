<html>
    <head>
        <title>Test page with boostrap</title>
        <link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.4/flatly/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
	<?php
		require_once('../helpers.php');
		init();
		$mysql_conn = get_mysql_conn('stories');

		if($mysql_conn == NULL){
			?><div class="alert alert-dismissible alert-warning">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <h4>Warning!</h4>
  <p>Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
</div>
			<?php
		} else {
		?>
		<div class="alert alert-dismissible alert-info">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
</div>
		<?php
	}
	?>
        <h1> This is a heading </h1>
        <h4> This is another heading </h4>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>

    </body>
</html>

