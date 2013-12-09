PBKDF2_4_Yii
============

A PBKDF2 implementation for Yii framework put at wherever you want, you may use it for PBKDF2 hashing.
No huge difference with other implementation.

Install
-------------
#Easy Way:#
Put in
    protected/extensions
folder. And put
    'ext.pbkdf2.HashHelper'
inside config/main.php, like following:
     'import' => array(
            'application.models.*',
            'application.components.*',
            /**
             * HashHelper
             */
            'ext.pbkdf2.HashHelper'
        ),

#Expert Way:#
Put wherever you want, and import the class manually whenever you like.
By using
    Yii::import('alias.folderAlias.HeshHelper');
like
    Yii::import('ext.pbkdf2.HashHelper');

Use
-------------
#To Hash Password#
    HashHelper::hashPassword($raw_text);
#To verify Password#
    HashHelper::verifyPassword($rawPassword, $good_hash));

Format
-------------
we need a char/varchar field for at least 65 column long for 32-digit long salt and 32-digit long Hash and a ':' as separator
Yes we store salt and password in the same field, it is easy to handle, simpler than what havoc do.

Config
-------------
#Easy Way#
Don't change any thing.
#Expert Way#
Check out the source page. And my edition as well, you will notice what i simplified.
[PBKDF2 Password Hashing for PHP - Defuse Security](https://defuse.ca/php-pbkdf2.htm)

Advantage
-------------
Nothing special. But it is easy to use and simple to understand.

Requirement
-------------
Require PHP5.3++,MCRYPT extension support.