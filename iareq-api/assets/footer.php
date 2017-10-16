  <div class='col-xs-12' id='footer'>
     &#0169; UW-Whitewater
  </div>
</div>
<?php 
	$uid = '-1';
	if (isset($_SESSION['indID']))
		$uid = $_SESSION['indID'];
?>
<input type='hidden' name='uid' id='uid' value='<?php echo $uid; ?>' />
</body>
</html>
