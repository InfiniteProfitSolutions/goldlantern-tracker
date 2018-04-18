<?php
define('NOTIFICATION_DIR', 'notifications');

if ($_GET['n'] && $_GET['n'] != ''){
	unlink(NOTIFICATION_DIR.'/'.urldecode($_GET['n']));
	header('Location: '.$_SERVER['PHP_SELF']);
	die;
}
$files = scandir ( NOTIFICATION_DIR, SCANDIR_SORT_DESCENDING);
?>
<html>
<head>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-2.2.3.min.js" integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Action</th>
				<th>Date</th>
				<th>File Name</th>
				<th>Content</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $files as $i=>$f ) :
			if (! is_dir ( $f )) : ?>
			<tr>
				<td><a class="btn btn-danger" href="<?=$_SERVER['PHP_SELF'].'?n='.$f?>">Delete</a></td>
				<td><?=date ("Y-m-d H:i:s.", filectime(NOTIFICATION_DIR.'/'.$f))?></td>
				<td><?=$f?></td>
				<td>
					<div class="container">
						  <button type="button" class="btn btn-warning" data-toggle="collapse" data-target="#content<?=$i?>">Show</button>
						  <div id="content<?=$i?>" class="collapse out">
						   	<?=file_get_contents(NOTIFICATION_DIR.'/'.$f)?>
						  </div>
					</div>
				</td>
			</tr>
		<?php 
			endif;
		endforeach;
		?>
		</tbody>
	</table>
</body>
</html>