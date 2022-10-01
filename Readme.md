# TYPO3 Extension: Mail Logger

As an administrator i need to see, what mails are sent by TYPO3.

This extension logs mails to sys_log table, so you can see them in the backend log module.

A log entry looks like this:

![An example log entry](https://user-images.githubusercontent.com/4953689/193420212-0b738f75-53cd-4fc2-88cd-538959285c3b.png)

The mail content is not logged.

## Development

In TYPO3 12.x use the new AfterMailerSentMessageEvent instead XClassing the Mailer.