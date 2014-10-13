<?
include '../control/profilePreProcessor.php';

$error = $testEmail = $testSms = "";

if(isset($_GET['error'])) {
	$error = $_GET['error'];
}

if (!strcmp($sms_status, "checked")) {
		$testSms = $_SESSION['sms'];
}

if (!strcmp($email_status, "checked")) {
		$testEmail = $_SESSION['email'];
}

?>

<form action="../control/profileProcessor.php" method="post" id="profileForm">

<div class="leftColumn">
	<p><b>You</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#bf6030;"><? echo $error; ?></span><p>
	<div style="margin-left:20px;margin-top:5px;">
			<table cellpadding="5">
				<tr><td>Username:</td><td><? echo($_SESSION['username']); ?></td></tr>
 			<tr><td><a href="#" class="tooltip" title="1-25 characters. May contain letters, numbers, accents, &apos; or _">First</a></td><td>
	 			<input type="text" size="22" name="fname" value="<? echo($_SESSION['fname']);?>" />
	 		</td></tr>
 			<tr><td><a href="#" class="tooltip" title="Optional. 1-25 characters. May contain letters, numbers, accents, &apos; or _">Last</a></td><td>	 		
 				<input type="text" size="22" name="lname" value="<? echo($_SESSION['lname']); ?>"/>
 			</td></tr>
 			<tr><td valign="top"><a href="#" class="tooltip" title="Optional. 7-25 characters. Must contain at least 1 digit.">Change Password</a></td><td><input type="password" size="22" name="password1" value="" /></td></tr>
 			<tr><td valign="top">Confirm Password:</td><td><input type="password" size="22" name="password2" value="" /></td></tr>
		</table>
	</div>

	<p><b>Your Messaging</b><p>
	<div style="margin-left:20px;">
		<table cellpadding="5">
			<tr>
				<td colspan="2">	
					<a id="testMessageLink" href="javascript:post('../control/messageProcessor.php',{testMessage:'true',phone:'<? echo($testSms); ?>',email:'<? echo($testEmail); ?>'})">
					Send yourself a test message
					</a>
				</td>
 				<td>Enabled?</td>
 			</tr>
 			<tr>
	 			<td>Text:</td>
 				<td><input type="text" size="22" name="sms" value="<? echo($_SESSION['sms']);?>" onchange="disableTestMessageLink()" /></td>
 				<td><input type="checkbox" name="sms_status" <? echo($sms_status); ?> onchange="disableTestMessageLink()" /></td>
 			</tr>
 			<tr>
	 			<td>Email:</td>
 				<td><input type="text" size="22" name="email" value="<? echo($_SESSION['email']);?>" onchange="disableTestMessageLink()" /></td>
 				<td><input type="checkbox" name="email_status" <? echo($email_status); ?>  onchange="disableTestMessageLink()" /></td>
 			</tr>
		</table>			
		<p><input style="float: right" type="submit" value="Update Your Profile"/></p>
	</div>
	</form>
</div> <!-- leftColumn -->

<div class="rightColumnPlain">

	<p style="margin-top:20px;"><b>Your Network</b><p>
	<!-- TODO - $count-1 is there because of a bug -->
	<p style="margin-left:20px;margin-top:5px;"><? echo $_SESSION['networkName']; ?> (<? echo $count-1; ?> affiliates)</br><p>
		
	<p><b>Your Organizations</b><p>
	<p style="margin-left:20px;margin-top:5px;"><? echo $_SESSION['orgName'];?></p>
		
	<p style="margin-top:20px;"><b>Your Groups</b><p>
	<p style="margin-left:20px;margin-top:5px;">
	<? 
		// TODO - won't work well with > 1 groups
		foreach ($_SESSION['groups'] as $key=>$val) {
    			echo $val . "</br>";
    }
  ?> 
  </br><p>
	
	<p style="margin-top:20px;"><b>Your Collaborations</b><p>
	<div style="margin-left:20px;margin-top:5px;font-size:12px;border: 1px solid #4b5b6e;border-radius:15px;padding:10px;">
		<img src="image/comingSoon.jpg" width="57" height="57" style="float:right;"/>
		<p><b>For authorized Nexus pilot users</b></p>
		<p>Collaboration tracking will be available <b>November 2014</b></p>
	</div>
</div> <!-- rightColumn -->