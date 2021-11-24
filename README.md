# protectimus_otp_authentication

This is a plugin for Roundcube Webmail, that allows to integrate Protectimus two-factor authentication solution into your mailing system with no effort.

Protectimus is the most convenient and affordable solution for organizing strong two-factor authentication, based on One-Time Passwords (OTP).
Learn more about Protectimus at https://www.protectimus.com

# Installation instruction:
1. Get registered with the Protectimus Cloud Service. See the detailed instructions here: https://www.protectimus.com/guides/saas-service/
1. Add Resouce, Users, Tokens, and then Assign Tokens with Users to a Resource in the Protectimus Cloud Service. See the instructions in the [Protectimus Administrator Guide](https://www.protectimus.com/wp-content/themes/protectimus/img/pdf/EN_Protectimus_Administrator%27s_guide_SAAS_Service_On_Premise_Platform.pdf)
1. Download the code from github
1. Place the protectimus_otp_authentication dir into the plugins/ folder of your roundcube install
1. Enable the protectimus_otp_authentication plugin in config/main.inc.php:

```$config['plugins'] = array('protectimus_otp_authentication');```

4. Open and adjust plugins/protectimus_otp_authentication/config.inc.php
You have to fill in these parameters:

This is your login name

```$config['protectimus_api_username']```

You can find api key at service.protectimus.com Profile -> API Key

```$config['protectimus_api_key']```

You don't need to change this, if you use our cloud service

```$config['protectimus_api_url'] = 'https://api.protectimus.com/';```

ID of the resource in the Protectimus service

```$config['protectimus_resource_id']```

```diff
- IMPORTANT:
if you use theme skin different from default you need to rename skin folder in this plugin.
For example:
cp -a skins/default skins/elastic
```

# See also
You can also visit official Roundcube Webmail plugin repository to know more about how to install our RoundCube plugin:
https://packagist.org
