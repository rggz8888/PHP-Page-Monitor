# PHP Page Monitor

This is a quick & dirty PHP Page Monitor (done in 1 hour..), working with a cron job.

It can send notifications by SMS (SoapClient) or Mail.

It requires PHP >= 5.3, MySQL.

Feel free to improve it.

Create your cron job with curl, it will be something like that : 

     */5 * * * * /usr/bin/curl "mywebsite.com/cron.php"

License MIT.
