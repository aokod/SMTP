### SMTP
Replaces the forum software's e-mail functions with the SMTP functions of PHPMailer, the classic e-mail sending library for PHP.

eso (esoTalk) only supports PHP mail by default and relies on plugins in order to hook into its `sendEmail()` function.

Originally created by Raphael Michel (@raphaelm) as a plugin for esoTalk beta.  Ported to the eso forum software.

#### Configuring the plugin
The SMTP plugin lets you configure the following fields for your SMTP server:

```
<?php
$config["SMTP"] = array(
"server" => "smtp.example.com,
"username" => "user@example.com",
"password" => "******",
"port" => 465,
"auth" => "ssl", // false = no authentication | "tls" | "ssl"
);
?>
```

These settings can be configured in the plugin's settings panel or stored in your forum's `config/custom.php` if so desired.
