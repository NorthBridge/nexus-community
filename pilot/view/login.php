<? 
session_start();

include("../model/pgDb.php");

$error = "";

if(isset($_GET['logout'])) {
	// Logout from Forum
	require_once '../control/forum_sso_functions.php';
	forumSignout();
	
	// Logout from NorthBridge
	$_SESSION = array();
	// TODO: Also delete session cookie? See http://php.net/manual/en/function.session-destroy.php
	session_destroy();
}

if(isset($_GET['error'])) {
	$error = $_GET['error'];
} else if(isset($_GET['logout'])) { 
	$error = "You have signed out succesfully.";
}

$cursor = pgDb::getOrganizationById('18');
$row = pg_fetch_array($cursor);
$logo = $row['logo'];
$networkName = $row['name'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	
  <head>
  	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="style/style.css" type="text/css" />
    <script src="script/script.js" language="javascript"></script>
    <link rel="shortcut icon" href="image/northbridge-ico.png" />
    <title>Nexus | Login</title>
  </head>
  
  <body>
    <div class="container">
    	<!--<img style="float:left;vertical-align:top;margin:20px;" src="http://chicagofaithandhealth.org/imgs/logo.png" height="88" width="365" border="0" alt="Logo: The Center for Faith and Community Health Transformations"/>-->
   		<img style="float:left;vertical-align:top;margin:20px;" <? echo $logo; ?>  border="0" alt=""/>
   		<p style="text-align:right;color:#4b5b6e;font-size:34px;margin-right:20px;"><b>Nexus</b><br/>
   		<i><span style="text-align:right;color:#4b5b6e;font-size:16px;margin-right:20px;">Building communities that build communities</span></i></p>

      <div class="shell">

			<div class="projectsContent">      	
      	<div class="leftColumn"> 
							<p><b>Login</b></p>
							<div class="formLogin" style="border:thin solid #4b5b6e;padding:10px;border-radius:15px;">
								<form action="../control/loginProcessor.php" method="post" id="loginForm">
									<table cellpadding="5">
								  <tr><td colspan="2"><? echo $error; ?></td></tr>
										<tr><td>User Id:</td><td><input class="passed" type="text" size="15" name="uid" value=""/></td></tr>
										<tr><td>Password:</td><td><input class="passed" type="password" size="15" name="password" value=""/></td></tr>
										<tr><td colspan="2"><input type="submit" style="float:right;" value="Login"/></td></tr>
									</table>
										<p><a href="javascript:void(0)" onclick="document.getElementById('light_user').style.display='block';document.getElementById('fade').style.display='block'">Forgot your user id?</a> | <a href = "javascript:void(0)" onclick = "document.getElementById('light_password').style.display='block';document.getElementById('fade').style.display='block'">Forgot your password? </a></p>
										<input type="hidden" name="action" value="authenticate" />
								</form>
							</div>						
							
				</div>
							
     		<div class="rightColumn" style="border:0px;background-color:#e6ebf0;padding:10px;top:50px;height:200px;width:350px;">
									<p><b>Welcome to Nexus!</b></p>
									<p style="margin:10px;">This is your collaboration space, secure to your community of practice: <b><? echo $networkName; ?></b>.<br/>&nbsp;<br/>This space supports the work you do every day to strengthen communities.</p>
      	</div>
      </div>
      
      <div class="footer" style="position:absolute;top:400px;left:600px;right;color:#4b5b6e;font-size:18px;margin:20px;">
        <i>powered by</i>
    		<a href="http://northbridgetech.org/index.php"><img style="vertical-align:bottom;" src="http://northbridgetech.org/images/northbridge-logo.png" height="57" width="143" border="0" alt="NorthBridge Technology Alliance"/></a>
		</div>
	
      </div><!--shell-->
    </div><!-- container -->       
       
    <!-- lightboxes -->
		<div id="light_password" class="white_content">
			<a href="javascript:void(0)" onclick="document.getElementById('light_password').style.display='none';document.getElementById('fade').style.display='none'" style="float:right">Close</a>
			<form action="../control/forgotPasswordProcessor.php" method="post">
				<table cellpadding="5">
					<tr><td colspan="2"><p>No problem!</p><p>Please enter your user id so we can email you a password reset link.</p></td></tr>
					<tr><td>User Id:</td><td><input class="passed" type="text" size="15" name="uid" value=""/></td></tr>
					<tr><td><a href="javascript:void(0)" onclick="document.getElementById('light_user').style.display='block';document.getElementById('light_password').style.display='none';document.getElementById('fade').style.display='block'">Forgot your user id?</a></td><td><input type="submit" style="float:right;" value="Reset Password"/></td></tr>
				</table>
			</form>
		</div>
		
		<div id="light_user" class="white_content">
			<a href="javascript:void(0)" onclick="document.getElementById('light_user').style.display='none';document.getElementById('fade').style.display='none'" style="float:right">Close</a>
			<form action="../control/resendEnrollmentProcessor.php" method="post">
				<table cellpadding="5">
					<tr><td colspan="2"><p>Your User ID can be found in your original enrollment confirmation email. Can't find it?</p><p>Please enter your email address and we will resend your enrollment package.</p></td></tr>
					<tr><td>Email:</td><td><input class="passed" type="text" size="15" name="email" value=""/></td></tr>
					<tr><td colspan="2"><input type="submit" style="float:right;" value="Resend Enrollment Package"/></td></tr>
				</table>
			</form>
		</div>
		<div id="fade" class="black_overlay"></div>
		<div id="fade" class="black_overlay"></div>
  	
	</body>
	
</html>




