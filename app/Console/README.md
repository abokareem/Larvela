# Larvela Scheduled Tasks

In order to implement the Business Automarion Features in Larvela, scheduled tasks are called using the native Laravel Framework scheduling system. This is done inside the Kernel.php file.

In order to get scheduling working you need to add a Linux OS cronatab entry to call the artisan schedule:run command every minute.


##CRONTAB File Entry

To use the Laravel scheduling system, set up a cron job that runs every minute and calls the artisan command shown in the following snippet:

```
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```
