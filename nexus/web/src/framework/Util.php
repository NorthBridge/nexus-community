<?php

require_once(dirname(__FILE__) . "/../../../config/config_env.php");
require_once(PHP_ROOT . "/Log.php");
require_once(PHPBB3_ROOT . "/config.php");
require_once(COMPOSER_ROOT . '/autoload.php');
require_once(Utilities::getModulesRoot() . "/error/handlers.php");
require_once(Utilities::getPhpRoot() . "/Validate.php");
require_once(Utilities::getSrcRoot() . "/user/User.php");
require_once(Utilities::getSrcRoot() . "/group/Group.php");
require_once(Utilities::getSrcRoot() . "/organization/Organization.php");
require_once(Utilities::getSrcRoot() . "/schedule/Event.php");
require_once(Utilities::getLibRoot() . "/autoload/autoloader.php");
require_once(Utilities::getLibRoot() . "/rememberme/rememberme/src/Storage/FileStorage.php");
require_once(Utilities::getLibRoot() . "/rememberme/rememberme/src/Authenticator.php");

// set config settings
autoloader(array(array(
      'debug' => true, // turn on debug mode (by default debug mode is off)
      'basepath' => Utilities::getLibRoot(), // basepath is used to define where your project is located
      'extensions' => array('.php'), // allowed class file extensions
      'verbose' => false,
)));

// now we can set class autoload paths
autoloader(array(
      'rememberme/rememberme/src/',
      'rememberme/rememberme/src/Storage',
      'facebook'
));


// useful when in debug mode
//$cached_paths = autoloader();

// print array of class autoload paths:
//print_r($cached_paths); exit(0);

require_once(Utilities::getLibRoot() . "/rememberme/rememberme/src/Storage/FileStorage.php");
require_once(Utilities::getLibRoot() . "/rememberme/rememberme/src/Authenticator.php");

/* Support phpBB3 session integration */
//$request->enable_super_globals();

class Utilities {

	public static function log($message, $level) {
		$conf = array('append' => true, 'mode' => 0644, 'timeFormat' => '%X %x');	
		$fileLogger = Log::singleton("file", Utilities::getLogRoot() ."/web.log", "", $conf, PEAR_LOG_DEBUG);
		$fileLogger->log($message, $level);
	}

	public static function getDbHost() { return DB_HOST; }
	
	public static function getDbUser() { return DB_USER; }
	
	public static function getDbPassword() { return DB_PASSWORD; }

	public static function getDbName() { return DB_NAME; }
	
	public static function getCrmHost() { return CRM_HOST; }
	
	public static function getCrmUser() { return CRM_USER; }
	
	public static function getCrmPassword() { return CRM_PASSWORD; }

	public static function getCrmName() { return CRM_NAME; }
	
	public static function getCrmActivationTable() { return CRM_ACTIVATION_TABLE; }
	
	public static function getCrmActivationTableRow_Oid() { return CRM_ACTIVATION_ROW_OID; }
	
	public static function getCrmActivationTableRow_Enroll() { return CRM_ACTIVATION_ROW_ENROLL; }
	
	public static function getCrmActivationTableRow_IsEnrolled() { return CRM_ACTIVATION_ROW_IS_ENROLLED; }
	
	public static function getSmsServiceUsername() { return SMS_SERVICE_USER; }
	
	public static function getSmsServicePassword() { return SMS_SERVICE_PASSWORD; }
	
	// TODO - namespace below	variables read from phpbb internal config file
	public static function getForumHost() { return FORUM_HOST; }

	public static function getForumUser() { return FORUM_USER; }
	
	public static function getForumPassword() { return FORUM_PASSWORD; }
	
	public static function getForumName() { return FORUM_NAME; }
	
	// Global group id for the phpBB REGISTERED USER group
	public static function getForumRegisteredUserGroup() { return FORUM_REGISTERED_USER_GROUP; }
	
	public static function getNewCfchtForum() { return CFCHT_NEW_GROUP; }
	
	public static function getGoogleApiKey() { return GOOGLE_API_KEY;	}
	
	public static function getLogRoot() { return LOG_ROOT; }
	
	public static function getEventApprovalList() { 
		$emailList = Organization::getNetworkAdminEmailByOrgId($_SESSION['orgId']); 
		$listString = "";
		foreach ($emailList as $address) {
			$listString .= $address . ", ";
		}
		return self::stripTrailingComma($listString);
	}
	
	public static function getDemoUidpk() { return DEMO_UIDPK; }		
	
	public static function getDemoPassword() { return DEMO_PASSWORD; }	
	
	private static function getDemoUsername() { return DEMO_USERNAME; }
	
	public static function getDemoNowEvent() { return DEMO_EVENT_NOW; }
	
	public static function getDemoFutureEvent() { return DEMO_EVENT_FUTURE; }
	
	public static function getPublicGroupId() { return PUBLIC_GROUP; }
	
	private static $web_path = "/web";
	
	private static $config_path = "/config";
	
	private static $include_path = "/include";

	public static function getEnvHost() { return ENV_HOST; }
	
	public static function getHttpPath() { return ENV_PROTOCOL . ENV_HOST . APP_NAME . self::$web_path; }
	
	public static function getPluginPath() { return self::getHttpPath() . "/plugin"; }
	
	public static function getPilotPath() { return ENV_PROTOCOL . ENV_HOST . PILOT_NAME; }
	
	public static function getConfigPath() { return ENV_PROTOCOL . ENV_HOST . APP_NAME . self::$config_path; }
	
	public static function getPartnerHttpPath() { return ENV_PROTOCOL . ENV_HOST . APP_NAME . PTR_STC_ROOT; }
	
	public static function getWebRoot() {	return WEB_ROOT; }
	
	public static function getLocaleRoot() { return LCL_ROOT; }
	
	public static function getModulesRoot() {	return self::getWebRoot() . "/modules"; }
	
	public static function getIncludeRoot() { return INC_ROOT; }
	
	public static function getPhpRoot() {	return PHP_ROOT; }
	
	public static function getPhpBbRoot() { return PHPBB3_ROOT . "/";	}

	public static function getSrcRoot() {	return self::getWebRoot() . "/src"; }
	
	public static function getLibRoot() {	return LIB_ROOT; }
	
	public static function getComposerRoot() { return COMPOSER_ROOT; }
	
	public static function getTokenRoot() {
		if(!is_writable(TKN_ROOT) || !is_dir(TKN_ROOT)) {
			self::log(TKN_ROOT . " does not exist or is not writable by the web server.", PEAR_LOG_ERR);
			return false;
		} 
		return TKN_ROOT; 
	}
	
	public static function getPartnerFileRoot() { return PTR_ROOT . "/file"; }

	public static function getPartnerCustomRoot() { return PTR_ROOT . "/custom"; }
	
	public static function getHttpImagePath() { return self::getHttpPath() . "/image"; }
	
	public static function getForumHttpPath() { return ENV_PROTOCOL . FORUM_URL; }

	public static function getPartnerImageRoot() { return self::getPartnerHttpPath() . "/image/"; }
	
	public static function getPartnerFileUrl() { return self::getPartnerHttpPath() . "/file"; }
	
	public static function getSupportUrl() { return SUPPORT_URL; }
	
	public static function getMemberUrl() { return MEMBER_URL; }
	
	public static function getEnvName() { return ENV_NAME; }
	
	//public static function getBbbDomain() { return CONFIG_SERVER_BASE_URL; }
	
	private static function getSessionTimeout() { return SSN_TIMEOUT; }
	
	public static function getOpenMeetingMargin() {return time() + 15*60; }
	
	public static function getTwitterHandle() {	return "NorthbridgeNFP"; }
	
	public static function isUaSession() { return FALSE; }
	
	public static function getAdminPassword() { return ADMIN_PASSWORD; }
	
	public static function getFbAppId() { return FB_APP_ID; }
	
	public static function getFbAppSecret() { return FB_APP_SECRET; }
	
	public static function getLiAppId() { return LI_APP_ID; }
	
	public static function getLiAppSecret() { return LI_APP_SECRET; }

	public static function getJwtSecret() { return JWT_SECRET; }
	
	private static $supportedLangs = array(
		'en' => 'en_US.utf8',
		'es' => 'es_ES.utf8',
		'ta' => 'ta_IN.utf8',
		'te' => 'te_IN.utf8',
		'hi' => 'hi_IN.utf8'
	);
	
	const VALIDATION_FNAME_ERROR = "Please enter a valid first name."; 
	const VALIDATION_LNAME_ERROR = "Please enter a valid last name (or none)."; 
	const VALIDATION_SMS_ERROR = "Please enter a valid text number (or none).";
	const VALIDATION_PHONE_ERROR = "Please enter a valid phone number (or none).";
	const VALIDATION_PASSWORD_ERROR = "Please enter valid matching passwords.";
	const VALIDATION_PASSWORD_USERNAME_ERROR = "Username and password cannot be the same.";
	const VALIDATION_USERNAME_FORMAT_ERROR = "Please enter a valid username.";
	const VALIDATION_USERNAME_DUPE_ERROR = "This username already exists. Please select a different username.";
	const VALIDATION_ORGNAME_ERROR = "Please enter the valid organization name that you represent.";
	const VALIDATION_DESCR_ERROR = "Please enter a valid description (or none).";
	const AUTHENTICATION_ERROR = "Your account is not located.";
	const ENROLLMENT_ERROR = "An error has occurred with your enrollment.";
	const RESET_ERROR = 'Your reset password link is not valid. Click "Forgot Pasword" to generate a new one.';
	
	const NAME_MAX = 25;
	const NAME_MIN = 1;
	// http://en.wikipedia.org/wiki/E.164
	const PHONE_MAX = 20;
	const PHONE_MIN = 6;
	const PASSWORD_MAX = 25;
	const PASSWORD_MIN = 7;
	const USERNAME_MAX = 25;
	const USERNAME_MIN = 7;
	const DESCR_MIN = 0;
	const DESCR_MAX = 250;
	
	public static function getMeetingTypes() {
		return array("1" => "video chat", "2" => "collaboration", "3" => "webinar", "4" => "video tether");	
	}
	
	public static function getHelloString($lang) {
		$LANGUAGE_MAP = array('en' => 'Hello',	'es' => 'Hola', 'ta' => "Hello");
		return $LANGUAGE_MAP[$lang];
	}

	public static function validateEmail($in) {
		if (filter_var($in, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		return false;
	}
	
	public static function validatePhone($in) {
		if(Validate::string($in, array(
    	'format' => VALIDATE_NUM . VALIDATE_SPACE . "." . "(" . ")" . "-",
    	'min_length' => self::PHONE_MIN,
    	'max_length' => self::PHONE_MAX))) {
			return TRUE;
		}
		return FALSE;
	}
	
	public static function validateUuid($in) {
		if(Validate::string($in, array(
			'format' => VALIDATE_ALPHA_LOWER . VALIDATE_NUM . "-", 
			'min_length' => 36, 
			'max_length' => 36))) {
			return TRUE;
		}
		return FALSE;
	}
	
	public static function validateNetworkIdFormat($in) {
		if(Validate::string($in, array(
			'format' => VALIDATE_NUM, 
			'min_length' => 1, 
			'max_length' => 8))) {
				return TRUE;
		}
		return FALSE;
	}
	
	public static function validateOrganizationUidFormat($in) {
		if(Validate::string($in, array(
			'format' => VALIDATE_NUM . VALIDATE_ALPHA,
			'min_length' => 8, 
			'max_length' => 8))) {
				return TRUE;
		}
		return FALSE;
	}
	
	public static function validateUserId($in) {
		if(self::validateUserIdFormat($in)) {
				return TRUE;
		}
		return FALSE;
	}
	
	public static function validateGroupId($in) {
		if(self::validateUserIdFormat($in)) {
			if (Group::groupIdExists($in)) {	
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public static function validateUserIdFormat($in) {
		if(Validate::string($in, array(
			'format' => VALIDATE_NUM, 
			'min_length' => 1, 
			'max_length' => 8))) {
				return TRUE;
		}
		return FALSE;
	}	
	
	public static function getRoleName($in) {
		if(!isset($in)) return "user";
		return (($in < 5) ? "admin" : "user");	
	}
		
	// TODO - check for change impact
	public static function validateUsernameFormat($in) {
		 
		if (preg_match("/[ ]+/", $in)) { 
			return FALSE; 
		}
		if(isset($in) && strlen($in) >= 7 && strlen($in) <= 25) { 
    	return TRUE;
    } else {
    	return FALSE;
    }
  }
  
  public static function validateUsername($in) {
		if(self::validateUsernameFormat($in)) {
			if (User::userNameExists($in)) {	
				return TRUE;
			}
		}
		return FALSE;
  }
  
	public static function isValidPassword($in) {
		if (isset($in) 
				&& strlen($in) >= self::PASSWORD_MIN
				&& strlen($in) <= self::PASSWORD_MAX
    		&& preg_match("/[a-zA-Z]+/", $in)
    		&& preg_match("/[0-9]+/", $in)
    		&& !preg_match("/[ ]+/", $in)) { 
					return TRUE;			
			} 
			return FALSE;
	}
	
	public static function stripTrailingComma($in) {
		if (!strcmp(substr($in, -2), ", ")) {
			return substr($in, 0, -2);
		}
		return $in;
	}
	
	public static function stripTrailingSpace($in) {
		if (!strcmp(substr($in, -1), " ")) {
			return substr($in, 0, -1);
		}
		return $in;
	}
	
	public static function validateUserProfile($input, $pwRequired) {
		$result = array('good' => array(), 'error' => array());
		
		// EMAIL ADDRESS
		if (isset($input['email']) && self::validateEmail($input['email'])) {
			$result['good']['email'] = $input['email'];
		} else {
			$result['error']['email'] = "Please enter a valid email address.";
		}
		
		// FIRST NAME
		if (isset($input['fname'])) {
			if (Validate::string($input['fname'], array(
    				'min_length' => self::NAME_MIN,
    				'max_length' => self::NAME_MAX))) {
				$result['good']['fname'] = Utilities::sanitize($input['fname']);			
			} else {
				$result['error']['fname'] = self::VALIDATION_FNAME_ERROR;
			}
 		} else {
			$result['error']['fname'] = self::VALIDATION_FNAME_ERROR;
		}	
		
		// LAST NAME
		if (isset($input['lname']) && strlen($input['lname']) > 0) {
			if (Validate::string($input['lname'], array(
    				//'format' => VALIDATE_EALPHA . VALIDATE_NUM . VALIDATE_SPACE . "'" . "_" . "-",
    				'min_length' => self::NAME_MIN,
    				'max_length' => self::NAME_MAX))) {
				$result['good']['lname'] = Utilities::sanitize($input['lname']);			
			} else {
				$result['error']['lname'] = self::VALIDATION_LNAME_ERROR;
			}
 		} else {
			$result['good']['lname'] = "";
		}	
		
		// DESCRIPTION
		if (isset($input['descr']) && strlen($input['descr']) > 0) {
			if (Validate::string($input['descr'], array(
    				'format' => VALIDATE_EALPHA . VALIDATE_NUM . VALIDATE_PUNCTUATION . "-",
    				'max_length' => self::DESCR_MAX))) {
				$result['good']['descr'] = htmlspecialchars($input['descr']);			
			} else {
				$result['error']['descr'] = self::VALIDATION_DESCR_ERROR;
			}
 		} else {
			$result['good']['descr'] = "";
		}	
		
		// SMS
		if (isset($input['sms']) && strlen($input['sms']) > 0) {
			if (self::validatePhone($input['sms'])) {
				$result['good']['sms'] = self::stripPhone($input['sms']);
			} else {
				$result['error']['sms'] = self::VALIDATION_SMS_ERROR;
			}
		} else {
			$result['good']['sms'] = "";
		}
		
		// PHONE
		if (isset($input['phone']) && strlen($input['phone']) > 0) {
			if (self::validatePhone($input['phone'])) {
				$result['good']['phone'] = self::stripPhone($input['phone']);
			} else {
				$result['error']['phone'] = self::VALIDATION_PHONE_ERROR;
			}
		} else {
			$result['good']['phone'] = "";
		}
		
		// PASSWORD
		if (isset($input['password1']) && isset($input['password2']) && strlen($input['password1']) > 0) {
			if (strlen($input['password1']) >= self::PASSWORD_MIN
				&& strlen($input['password1']) <= self::PASSWORD_MAX
    		&& preg_match("/[a-zA-Z]+/", $input['password1'])
    		&& preg_match("/[0-9]+/", $input['password1'])
    		&& !preg_match("/[ ]+/", $input['password1'])
    		&& !strcmp($input['password1'], $input['password2'])) { 
				$result['good']['password'] = $input['password1'];			
			} else {
				$result['error']['password'] = self::VALIDATION_PASSWORD_ERROR;
			}
 		} else {
	 		if ($pwRequired) {
				$result['error']['password'] = self::VALIDATION_PASSWORD_ERROR;
			}
		}
		
		// USERNAME
		if (isset($input['username'])) {
			if (self::validateUsernameFormat($input['username'])) {
    		if (User::userNameExists($input['username'])) {
					$result['error']['username'] = self::VALIDATION_USERNAME_DUPE_ERROR;
				}	else {	 					
					$result['good']['username'] = $input['username'];
				}
			} else {
				$result['error']['username'] = self::VALIDATION_USERNAME_FORMAT_ERROR;
			}
 		}
		
		// PREVENT MATCHING USERNAME, PASSWORD
		// Since the allowed char lists for these fields aren't compatible, theoretically these conditions can never both be true
		if (isset($result['good']['password']) && isset($result['good']['username']) && !strcmp($result['good']['password'], $result['good']['username'])) {
			$result['error']['password'] =  self::VALIDATION_PASSWORD_USERNAME_ERROR;
		}

		return $result;
		
	}
	
	public static function validateUserCredentials($input) {
		
	}

	public static function escapeforRegex($in) {
		$out = strtr($in, array('.' => "\.",
									        	'/' => "\/",
									        	'(' => "\(",
									        	')' => "\)",
									        	'+' => "\+",
									        	'?' => "\?"));
		return $out;
	}
	
	public static function isSafeCharacterSet($in) {
		if (isset($in) && strlen($in) > 0) {
			if (preg_match("/[<>%*&=\/\\!]/", $in)) {
				return false;
			}
		}
	return true;
	}
	
	public static function encodeLineBreaks($in) {
		if ($in) {
			return str_replace(["\r\n", "\r", "\n"], "~", $in);	
		}
		return false;
	}

	public static function sanitize($in) {
		$out = strtr($in, array('(' => '&#40;',
                          	')' => '&#41;',
									        	'"' => '&#34;',
									        	'<' => '&#60;',
									        	'>' => '&#62;',
									        	'&' => '&#38;',
									        	'*' => '&#42;',
									        	'%' => '&#37;',
									        	'=' => '&#61;',
									        	'!' => '&#33;',
									        	'\'' => '&#39;',
									        	'\/' => '&#47;',
									        	'\\' => '&#92;'));
		return $out;
	}
	
	// TODO - this special character situation is chaotic!
	public static function strip2($in) {
		$out = strtr($in, array('(' => '',
                          	')' => '',
									        	'<' => '',
									        	'>' => '',
									        	"'" => '&apos;'));
		$out = trim($out);
		return $out;
	}
	
	static function stripPhone($num) {
		return preg_replace('/[^0-9]/', '', $num);
	}
	
	static function prettyPrintPhone($numIn) {
		$num = self::stripPhone($numIn);
	 
		$len = strlen($num);
		if($len == 7)
			$num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $num);
		elseif($len == 10)
			$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $num);
		 
		return $num;
	}
	
	public static function newUuid() { 
    $s = strtolower(md5(uniqid(rand(),true))); 
    $guidText = 
        substr($s,0,8) . '-' . 
        substr($s,8,4) . '-' . 
        substr($s,12,4). '-' . 
        substr($s,16,4). '-' . 
        substr($s,20); 
    return $guidText;
	}
	
	public static function newId() {
		return substr(self::newUuid(), 0, 8);
	}
	
	public static function hideInEnvironment() {
		//if (!strcmp($_SESSION['environment'], "dev")) {
		//	return true;
		//}
		return false;		
	}
	
	public static function setLogin($uid) {
		if (isset($_SERVER['REMOTE_ADDR'])) {
			User::setLoginByIp($_SERVER['REMOTE_ADDR'], $uid);
		}
		return;
	}

	public static function authenticate($uid, $pass) {	
		$coreAuthenticated = false;
		$hash = self::getPasswordHashByUser($uid, $pass);
		if ($hash && User::countActiveUsers($uid, $hash) == 1) {
			$coreAuthenticated = true;
		}
    return $coreAuthenticated;
	}
	
	public static function getUserLangagePreference() {
		if (isset($_SESSION['language']) && strlen($_SESSION['language']) > 0) {
			return $_SESSION['language'];
		}
		$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach($languages as $lang) {
			$languageSlice = substr($lang, 0, 2);
			// If English is supported at all, cause an English implementation
			// TODO - take browser priority settings
			if ($languageSlice == 'en') { break; }
    	if(array_key_exists($languageSlice, self::$supportedLangs)) {
        // Return the first supported language found (that is not English)
        return $languageSlice;
			}
		}
		return "en";
	}
	
	public static function setUserLanguageEnv() {
		$displayLang = Utilities::getUserLangagePreference();
		$domain = "messages";
		putenv("LANGUAGE=" . $displayLang);
		$results = setlocale(LC_ALL, self::$supportedLangs[$displayLang]);
		if (!$results) {
			// TODO - log proper, and trigger error
			echo "setlocale() failed: locale function is not available on this platform environment</br>";
			echo "Looking for: " . self::$supportedLangs[$displayLang] . "</br>";
			echo "Tip: run 'locale -a' on the runtime machine, It must contain your locale value</br>";
			echo "Tip: if your locale is missing, run 'sudo locale-gen &lt;name of locale here&gt;.UTF-8' then 'sudo update-locale'</br>";
		}
		bindtextdomain($domain, Utilities::getLocaleRoot());
		textdomain($domain);
	} 
	
	public static function setUserLanguagePreference($lang) {
		$_SESSION['language'] = $lang;
		self::setUserLanguageEnv();
	}
	
	public static function setUserEmailConfirm() {
		$_SESSION['emailConfirmed'] = "true";
		User::updateUserEmailConfirmedById($_SESSION['uidpk']);
	}
	
	public static function isSessionPublic() {
		if (strcasecmp($_SESSION['nexusContext'], "PUB") == 0) {
			return TRUE;
		}
		return FALSE;
	}
	
	public static function isSessionAdmin() {
		if (isset($_SESSION['role']) && !self::isSessionPublic()) {
			if ($_SESSION['role'] === 'user') {
				return FALSE;
			}
			return TRUE;
		} 
		return FALSE;
	}		

	public static function isSessionValid() {
		if (isset($_SESSION['nexusContext']) && 
				strlen($_SESSION['nexusContext']) == 3 && 
				isset($_SESSION['orgUid']) && 
				self::validateOrganizationUidFormat($_SESSION['orgUid']) && 
				isset($_SESSION['username']) &&
				!self::isSessionExpired() ) {
			return true;
		}
		return false;
	}
	
	public static function setSessionTimezone($zone) {
		unset($_SESSION['timezone']);
		$_SESSION['timezone'] = (Event::isValidTimeZone($zone) ? $zone : "undefined");
		return true;
	}
	
	public static function removeSessionOrg($orgId) {
		for ($i = 0; $i < count($_SESSION['orgs']); $i++) {
			if (isset($_SESSION['orgs'][$i]['id']) && $_SESSION['orgs'][$i]['id'] === $orgId) {
				array_splice($_SESSION['orgs'],$i,1);
				break;
			}
		}
	}
	
	public static function setSessionOrgs($username) {
		$_SESSION['orgs'] = Organization::getOrganizationsByUsername($username);		
		// Filtering out networks and at the same time snagging the network role, if existent
		for ($i = 0; $i < count($_SESSION['orgs']); $i++) {
			if ($_SESSION['networkId'] === $_SESSION['orgs'][$i]['id']) {
				if (isset($_SESSION['orgs'][$i]['role'])) {
					$_SESSION['role'] = self::getRoleName($_SESSION['orgs'][$i]['role']);
				}
				array_splice($_SESSION['orgs'],$i,1);
				break;
			}
		}
	}
	
	public static function setSessionGroups($username) {
		$_SESSION['groups'] = Group::getUserGroupsByUsername($username);
	}
	
	public static function setSmSessionValues($email, $provider='Social Media') {
		$_SESSION['sm_email'] = $email;
  	$_SESSION['sm_provider'] = $provider;
  }

	
	public static function setSession($username, $remember, $zone = "undefined", $password = "undefined") {

		// TODO - loading a PUBLIC context will wipe this one out and then screw up Nexus context
		
		// TODO - Session password is not updating on profile update because not sure what we are doing with the forum password yet
		
		session_regenerate_id(TRUE);
		
		unset($_SESSION['invitation']);
		unset($_SESSION['username']);
		$_SESSION['tmp-editorgid'] = "";
		$_SESSION['appRoot'] = self::getWebRoot();
		$_SESSION['environment'] = self::getEnvName();
		$_SESSION['username'] = $username;
		self::setSessionLastActivity();
		// Pretty sure this SESSION value is deprecated since cookies are working now
		//$_SESSION['remember'] = ($remember ? "true" : "false");
		$_SESSION['remember'] = "true";
		$_SESSION['remembered'] = $username;
		self::setSessionTimezone($zone);
		$_SESSION['language'] = self::getUserLangagePreference();
		//$_SESSION['defaultSearchId'] = self::newUuid();
		// NOTE - This password in session is not reliable because certain session can initiate without one. Why collecting???
 		$_SESSION['password'] = $password;
		
		$cursor = User::getActiveUserByUsername($_SESSION['username']);
		while ($row = pg_fetch_array($cursor)) {
  		$_SESSION['uidpk'] = $row['id'];
			$_SESSION['fname'] = $row['fname'];
  		$_SESSION['lname'] = $row['lname'];
  		$_SESSION['email'] = $row['email'];
			$_SESSION['profile'] = $row['profile'];
			$_SESSION['emailConfirmed'] = ($row['email_confirmed'] == "t" ? "true" : "false");
		}
		
		$_SESSION['firstLogin'] = User::isFirstLogin($_SESSION['uidpk']);

		$networks = Organization::getNetworksByUsername($_SESSION['username']);
		// Limiting to one for now on query
		if ($networks) {
			$_SESSION['nexusContext'] = $networks[0]['account_type'];
			if ($_SESSION['nexusContext'] === "ADV") {
	  		$_SESSION['networkName'] = $networks[0]['name'];
  			$_SESSION['networkId'] = $networks[0]['id'];
  			$_SESSION['defaultForumId'] = $networks[0]['forumid'] ? $networks[0]['forumid'] : "0";
  			$_SESSION['logo'] = $networks[0]['logo'];	
				$_SESSION['orgUid'] = $networks[0]['uid'];
			}
		}
			
		self::setSessionOrgs($_SESSION['username']);
		// Below assumes Nexus user is only associated with one org.
		if ($_SESSION['nexusContext'] === "NWM") {
			$_SESSION['orgId'] = $_SESSION['orgs'][0]['id'];
			$_SESSION['orgUid'] = $_SESSION['orgs'][0]['uid'];
			$_SESSION['orgName'] = $_SESSION['orgs'][0]['name'];
			$_SESSION['logo'] = $_SESSION['orgs'][0]['logo'];	
			$_SESSION['role'] = self::getRoleName($_SESSION['orgs'][0]['role']);
		}
	
		self::setSessionGroups($_SESSION['username']);
	
		$returnArray = Group::getPublicSystemGroupByOrgId($_SESSION['networkId']);
		$_SESSION['pgpk'] = $returnArray[0]['id'];
		
		$returnArray = Group::getNetworkSystemGroupByOrgId($_SESSION['networkId']);
		$_SESSION['ngpk'] = $returnArray[0]['id'];
	}
	
	public static function setPublicSession($oid, $zone = "undefined", $fname = "Anonymous", $uuid = false, $email = "undefined") {

		$_SESSION['nexusContext'] = "PUB";
		$_SESSION['environment'] = self::getEnvName();
		$_SESSION['orgUid'] = $oid;
		$_SESSION['username'] = "pUser-" . substr($oid, 0, 8);
		if (!$uuid) {
			$_SESSION['groups'] = Group::getUserGroupsByUsername($_SESSION['username']);
		} else {
			$_SESSION['groups'] = Group::getGroupByEventUuid($uuid);	
		}
		$_SESSION['pgpk'] = $_SESSION['groups'][0]['id'];
		$row = pg_fetch_row(User::getActiveUserByUsername($_SESSION['username']));
		$_SESSION['uidpk'] = $row[0];
		self::setSessionTimezone($zone);
		$_SESSION['fname'] = $fname;
		$_SESSION['lname'] = "";
		$_SESSION['email'] = $email;

		$org = pg_fetch_array(Organization::getOrganizationByUid($oid));
	
		if (isset($org['id'])) {
			$_SESSION['orgId'] = $org['id'];
			$cursor = Organization::getNetworkByOrgId($org['id']);
			while ($row = pg_fetch_array($cursor)) {
		  	$_SESSION['networkName'] = $row['name'];
  			$_SESSION['networkId'] = $row['networkid'];
  			$_SESSION['publicForumId'] = $row['pforumid'] ? $row['pforumid'] : "0";
  			$_SESSION['logo'] = $row['logo'];				
			}
		}
			
		$enrollUuid = Organization::getPublicEnrollUuidByOrgUid($_SESSION['orgUid']);
		if ($enrollUuid) {
			$_SESSION['publicEnrollUuid'] = $enrollUuid;
		}
		
	}
	
	public static function setDemoSession($username, $remember, $zone = "undefined") {
		self::setSession(self::getDemoUsername(), false, $zone);
		$pos = strpos($username, " ");
		if ($pos) {
  		$_SESSION['fname'] = substr($username, 0, $pos);
  		$_SESSION['lname'] = substr($username, $pos+1);
  	} else {
  		$_SESSION['fname'] = $username;
  		$_SESSION['lname'] = " ";
  	}
  	$_SESSION['demo'] = "true";
	}
	
	public static function setSessionLastActivity() {
		$_SESSION['lastActivity'] = time();
	}
	
	public static function isSessionExpired() {
		if (isset($_SESSION['nexusContext']) && $_SESSION['nexusContext'] != "NWM") {
			// Nexus Advantage and public sessions don't time out (yet)
			return false;
		}
		if (Utilities::getSessionTimeout() == "-1") {
			return false;
		}
		if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > self::getSessionTimeout())) {
			return true;
		}
		return false;
	}
	
	public static function loginForum($user, $auth) {
			// Login the session user to the forum if there is not already a forum session matching this username
			if ($user->data['username'] !== $_SESSION['username']) {
				// TODO - how to manage existing and unknown passwords in prod? must auto-enroll at login...
				//$forumPassword = ($_SESSION['environment'] === "prod") ? $_SESSION['username'] : $_SESSION['password'];
				$forumPassword = $_SESSION['username'];
				$result = $auth->login($_SESSION['username'], $forumPassword);
				$auth->acl($user->data);
				$user->setup();	
				// TODO - why isn't this timezone transfer working? Thought I saw it working once...
				if (Event::isValidTimeZone($_SESSION['timezone'])) {
					$user->data['user_timezone'] = $_SESSION['timezone'];
				}
			}
	}
	
	public static function destroySession() {
		$_SESSION = array();
		/* ====================================================================================================================================
		| It is typically recommended to delete the session cookie on the client side in order to fully kill a PHP session. (Like following code).
		|
		| We do NOT do this, and this is why:
		|
		| In login.php we set SESSION variables for different purposes when the page loads, even before a user actually logs in to the app.
		| This is how we securely inform server processors of certain usage contexts (like this being a demo session, for instance)
		| We need those SESSION variables to be useful even after a user initiates a logout (which calls this Utilities::destroySession() method).
		| If we kill the session cookie then we can't use this technique. For instance, if we delete the SESSION cookie when a demo user logs out
		| then we lose track of the fact that this session context is demo session and a subsequent log in fails. Without the cookie, there is no
		| place to store this info and communicate it back to the server.
		|
		| Instead, we leave the old SESSION cookie in place on the client side even though the SESSION array is emptied on the server. This way we
		| still have a place to hang this context state information. Then, we regenerate the session id after a succesful login. This should
		| answer to any sesson hijacking concerns but I am documenting carefully for the next person who wanders over here and wonders about this!
		======================================================================================================================================= */
		//if (ini_get("session.use_cookies")) {
    //	$params = session_get_cookie_params();
    //	setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		//}
		session_destroy();
	}
	
	public static function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	// TODO - this security stuff should not be inside Util class
	public static function storeSecurePasswordImplA($plaintextPassword, $userId) {
		$salt = self::generateRandomString(32);
		$securePassword = self::systemHashImplA($salt . $plaintextPassword);
		return User::setSecurePasswordImplA($userId, '[[ENC]]' . $securePassword, $salt);
	}
	
	public static function getPasswordHashByUser($userId, $plaintextPassword) {
		$cursor = User::getUserPasswordByUser($userId);	
		if (pg_num_rows($cursor) < 1) {
			return FALSE;
		}
		// greater than one returned, we ignore
		$row = pg_fetch_array($cursor, 0);
		$encrypted = $row['password'];
	
		if (substr($encrypted, 0, 7) === "[[ENC]]") {
			// Return the salted hash for the plaintext password (using user's unique salt)
			$hash = "[[ENC]]" . self::systemHashImplA($row['salt'] . $plaintextPassword);
		} else {
			// If what is stored for this user has not been hashed OR there is no password at all for this user, return plaintext.
			$hash = $plaintextPassword;
		}
		return $hash;
	}
	
	public static function systemHashImplA($input) {
		return hash('sha256', $input);
	}
		
	public static function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
	}
		
}

?>
