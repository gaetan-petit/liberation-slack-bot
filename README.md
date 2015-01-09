# Le Monde Live Slack Bot

### Requirements

  - PHP >= 5.3
  - You need a token from Slack:
    - Jump at https://api.slack.com/#auth (you have to login)
    - and you will find your token.

### Installation

  - Clone this repo
  - Set up a cron to run every minute:

  ````
  * * * * * cd /path/to/folder && php LeMondeLiveNotifier.php >> LeMondeLiveNotifier.log
  ````

### Side notes

The code is ugly but it works ©
