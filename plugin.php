<?php
// Copyright 2009 Raphael Michel <webmaster@raphaelmichel.de>
//
// SMTP/plugin.php
// Replaces the forum's e-mail functions with the SMTP functions of PHPMailer.
 
if (!defined("IN_ESO")) exit;
 
class SMTP extends Plugin {
 
	var $id = "SMTP";
	var $name = "SMTP";
	var $version = "0.5";
	var $description = "Replaces the forum's e-mail functions with the SMTP functions of PHPMailer";
	var $author = "Raphael Michel, grntbg";
	var $defaultConfig = array(
		"server" => null,
		"username" => null,
		"password" => null,
		"port" => 25,
		"auth" => false
	);

	function init()
	{
		parent::init();
 
		$phpmailer = dirname(__FILE__).'/class.phpmailer.php';
 
		require($phpmailer);
 
		$this->eso->controller->addHook("sendEmail", array($this, "sendEmailNow"));
	}
	
	function settings()
	{
		global $config, $language;
 
		// Add language definitions.
		$this->eso->addLanguage("Server", "Server");
		$this->eso->addLanguage("Username", "Username");
		$this->eso->addLanguage("Password", "Password");
		$this->eso->addLanguage("Port", "Port");
		$this->eso->addLanguage("Authentication", "Authentication");
		$this->eso->addLanguage("normal", "normal");

		// Generate settings panel HTML.
		$settingsHTML = "<ul class='form'>
		<li><label>{$language["Server"]}</label> <input name='SMTP[server]' type='text' class='text' value='{$config["SMTP"]["server"]}'/></li>
		<li><label>{$language["Username"]}</label> <input name='SMTP[username]' type='text' class='text' value='{$config["SMTP"]["username"]}'/></li>
		<li><label>{$language["Password"]}</label> <input name='SMTP[password]' type='password' class='text' value='{$config["SMTP"]["password"]}'/></li>
		<li><label>{$language["Port"]}</label> <input name='SMTP[port]' type='text' class='text' value='{$config["SMTP"]["port"]}'/></li>
		<li><label>{$language["Authentication"]}</label> <select name='SMTP[auth]'>".
		"<option".(($config["SMTP"]["auth"] == false) ? ' selected="selected"' : '')." value='false'>{$language["normal"]}</option>".
		"<option".(($config["SMTP"]["auth"] == "tls") ? ' selected="selected"' : '')." value='tls'>TLS</option>".
		"<option".(($config["SMTP"]["auth"] == "ssl") ? ' selected="selected"' : '')." value='ssl'>SSL</option>".
		"</select></li>
		<li><label></label> " . $this->eso->skin->button(array("value" => $language["Save changes"], "name" => "saveSettings")) . "</li>
		</ul>";

		return $settingsHTML;
	}

	// Save the plugin settings
	function saveSettings()
	{
		global $config;

		$config["SMTP"]["server"] = @$_POST["SMTP"]["server"];
		$config["SMTP"]["username"] = @$_POST["SMTP"]["username"];
		$config["SMTP"]["password"] = @$_POST["SMTP"]["password"];
		$config["SMTP"]["port"] = (int)@$_POST["SMTP"]["port"];
		$config["SMTP"]["auth"] = (@$_POST["SMTP"]["auth"] == "tls") ? 'tls' : (@$_POST["SMTP"]["auth"] == "ssl") ? 'ssl' : false;
		writeConfigFile("config/SMTP.php", '$config["SMTP"]', $config["SMTP"]);
		$this->eso->message("changesSaved");
	}

	function sendEmailNow($to, $subject, $body)
	{
		global $config;
		if(!$config["SMTP"]["server"])
		{
			return mail(sanitizeForHTTP($to), sanitizeForHTTP(desanitize($subject)), desanitize($body), "From: " . sanitizeForHTTP(desanitize($config["forumTitle"]) . " <{$config["emailFrom"]}>") . "\nContent-Type: text/plain; charset={$language["charset"]}; format=flowed");
		}
		$mail = new PHPMailer(true);
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		if($config["SMTP"]["auth"]) $mail->SMTPSecure = $config["SMTP"]["auth"];
		$mail->Host       = $config["SMTP"]["server"];
		$mail->Port       = $config["SMTP"]["port"];
		$mail->Username   = $config["SMTP"]["username"];
		$mail->Password   = $config["SMTP"]["password"];
		$mail->AddAddress($to);
		$mail->SetFrom($config["emailFrom"], sanitizeForHTTP(desanitize($config["forumTitle"])));
		$mail->Subject = sanitizeForHTTP(desanitize($subject));
		$mail->Body = $body;
		return $mail->Send();
	}

}
?>
