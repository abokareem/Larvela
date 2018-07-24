# Theory of Operation - Jobs

**Updated 2018-07-24**

- A number of Job files which only sent emails are now free to be used as "hook" files for additional Business Logic.
- The email code that was in the Job file has been converted to use the Mailable interface and placed in app/Mail with the corresponding "blade" put in resources/views/Mailable/<store_code>/ directory structure.
- Controllers are being modified to call both the Job and the Mail::() interfaces.
- This will be documented in the final release candidate.


**Updated 2017-10-31**

Larvela uses Laravel Jobs to perform many tasks, particularly scheduled background tasks.

If a task needs to be done twice or it can be done asynchronously to the main code then it should be put into a Job.

In the present release of code, most Jobs are run synchronously unless they are scheduled from the Kernel.php file which is located in the app/Console directory.

By using Jobs for background tasks, the offloading of these to a secondary server using a queue based engine is possible in later versions of the code. This allows for some degree of scalability in future relases, this is a design goal of Larvela.

Many Jobs are also a hook into the business logic of the controller. It is preferred that you hook additonal logic into the Jobs files rather than into the Controller code.

# Notes

Please note, **not all the Job files have yet been release**, a refactor is occuring at present to make the code more user friendly and neater.

