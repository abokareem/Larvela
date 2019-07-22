# Larvela Jobs

Larvela uses Laravel Jobs to perform many tasks to offload processing from Controllers and for scheduled background tasks. Most Jobs are configured to support being run using Queuing via Redis as it has a built in Queuing system.

In the course of developing Larvela, many Jobs were previously used to email customers and the store admins using a crude templating system, but this has mostly been replaced using the Mailable interface and so the Jobs have provided an ideal place to put additonal Business Logic for store Automation and Marketing purposes.

## Current Status ##

Job Classes are being refactored so that all support the Queueable interface and a consistent logging.
All older email sending code using the /templates directory are also being refactored to use the Mailable interface.
See /Mail directory for relevant classes and resources/views/Mail/<store_code>/ for the actual templates.
/templates will be removed in sue course.

## Why use Jobs

- If a task needs to be done twice or it can be done asynchronously to the main code then it should be put into a Job.
- By using Jobs for background tasks, the offloading of these to a secondary server using a queue based engine is possible in later versions of the code. This allows for some degree of scalability in future relases, this is a design goal of Larvela.
- In the present release of code, scheduled tasks called from the Kernel.php file which is located in the app/Console directory are implemented as Jobs.

## Historical Notes

Please note, **not all the Job files have yet been release**, a refactor is occuring at present to make the code more user friendly and neater.

**Updated 2019-07-22**

- A small refactor is being made to align log file names to larvela-XXX and include support for Queueing.
**Updated 2018-07-24**

- A number of Job files which only sent emails are now free to be used as "hook" files for additional Business Logic.
- The email code that was in the Job file has been converted to use the Mailable interface and placed in app/Mail with the corresponding "blade" put in resources/views/Mailable/<store_code>/ directory structure.
- Controllers are being modified to call both the Job and the Mail::() interfaces.
- This will be documented in the final release candidate.


**Updated 2017-10-31**

- Refactoring of Templates to Mailabel interface started.

