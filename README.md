Export Import users
=========

This Extension is able to export or import users. Found user_id's will be updated with the data in the xml file. 

Not found user_id's are inserted and become a registered user in your forum. The xml user file is automatically deleted after insert or save.
Fields that are saved are username, user_password, user_email, user_from, user_website and user_occ.
Be carefull with this extension as it will overwrite your account if it's in the xml file.

[![Build Status](https://travis-ci.org/ForumHulp/Export-Import-Users.svg?branch=master)](https://travis-ci.org/ForumHulp/Export-Import-Users)

## Requirements
* phpBB 3.1-dev or higher
* PHP 5.3 or higher

## Installation
You can install this on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by doing the following:

1. Copy the entire contents of this repo to to `FORUM_DIRECTORY/ext/forumhulp/exportimportusers/`
2. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
3. Click Export Import users => `Enable`.

## Uninstallation
Navigate in the ACP to `Customise -> Extension Management -> Extensions` and click Export Import users => `Disable`.

To permanently uninstall, click `Delete Data` and then you can safely delete the `/ext/forumhulp/exportimportusers/` folder.

We feel sorry as our answers on phpbb sites are removed, so use github or our forum for answers.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2014 - John Peskens (ForumHulp.com)