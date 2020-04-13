<?PHP
$sender = 'demo@muslimghanchisamaj.in';
$recipient = 'gnp.patel1988@gmail.com';

$subject = "php mail test";
$message = "php test message";
$headers = 'From:' . $sender;

if (mail($recipient, $subject, $message, $headers))
{
    echo "Message accepted";
}
else
{
    echo "Error: Message not accepted";
}