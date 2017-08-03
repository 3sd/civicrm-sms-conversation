# Installation

The installation process for SMS conversation is similar to most CiviCRM extensions.

## Requirements

To send and receive SMS you will need to set up an SMS gateway with a third party SMS provider. It is important that you use a provider that allows two way communication, i.e. the ability to send and receive SMS.

Availablity of two way SMS services varies considerably depending on the country you are in.  If you are having trouble finding a two way SMS provider in your country consider using the [Telerivet extension](https://github.com/nditech/org.ndi.sms.telerivet), which enables you to turn any Android or IOS phone into an SMS gateway.

See https://docs.civicrm.org/user/en/stable/sms-text-messaging/what-is-sms/ for more details

CiviRules is required to install SMS conversation. Ensure that you have CiviRules enabled before installing the extension.


See https://docs.civicrm.org/user/en/stable/introduction/extensions for more information on installing extensions.

## Download

Until SMS conversation becomes approved for automated download, you need to manually download it and extract it to your extensions directory.

You can find the latest stable release here: https://github.com/3sd/civicrm-sms-conversation/releases.

## Install

Once you have downloaded and extracted the extension to your extensions directory, it should appear on the manage extensions page. Enable it and you should be ready to define and start SMS conversations.

## Post install configuration

Upon install, the extension should automatically enable the **Schedule SMS Conversations** scheduled job. You may want to check that this has happened and make any adjustments to the scheduling that you see fit.
