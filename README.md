# SMS conversation

SMS conversation is a **CiviCRM extension** that allows you to automate SMS conversations with contacts in [CiviCRM](https://civicrm.org).

You may also be interested in https://civicrm.org/extensions/chatbot.

## Requirements

CiviCRM 4.7.19+

## Installation

1. Download a release from https://github.com/3sd/civicrm-sms-conversation/releases to your extensions directory
2. Browse to **Administer > System Settings > Extensions**
3. Find **SMS Conversation** and click **Install**

Note: for help on installing extensions, consult the [extensions chapter of the CiviCRM system administrator guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions).

## Getting started

See https://docs.civicrm.org/sms-conversation/ for full documentation.

## Help

If you have any questions regarding this extension that are not answered in this README or in [the documentation](https://docs.civicrm.org/sms-conversation/), please check post a question on http://civicrm.stackexchange.com or contact info@thirdsectordesign.org.

## Credits

SMS conversation builds on work originally done in the Chained SMS extension. Development of SMS conversation was funded by the [National Democratic Institute](https://www.ndi.org/). Development of the original Chained SMS extension was funded by [Future First](http://futurefirst.org.uk/).

It was written by Michael McAndrew, [Third Sector Design](https://thirdsectordesign.org/) and Matthew Wire, [MJW Consulting](https://www.mjwconsult.co.uk/) who you can contact for help, support and further development.

## Contributing

Contributions to this repository are very welcome. Feel free to submit a pull request for minor improvements. For larger changes, please create an issue first.

## License

This extension is licensed under [AGPL-3.0](LICENSE.txt).

## Troubleshooting

### Webhook errors

- This might be mostly related to question configuration. Please check if you have configured 'Invalid Text'.
- Check if you have correctly configured 'Match' and 'Match Text' for the answer.
