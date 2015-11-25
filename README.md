PBKDF2_4_Yii
============

>**Deprecated**: Please do not use this project even in development stage! Use PHP build-in bcrypt feature / build-in Security features in Yii2 instead!

A PBKDF2 implementation for Yii framework put at wherever you want, you may use it for PBKDF2 hashing.
No huge difference with other implementation.

Install
-------------
###Easy Way:###

Put HashHelper class file in ``protected/extensions`` folder. And put ``'ext.pbkdf2.HashHelper'`` inside config/main.php, like following:

     'import' => array(
            'application.models.*',
            'application.components.*',
            /**
             * HashHelper
             */
            'ext.pbkdf2.HashHelper'
        ),

###Expert Way:###
Put wherever you want, and import the class manually whenever you like.
By using

    Yii::import('alias.folderAlias.HeshHelper');

like

    Yii::import('ext.pbkdf2.HashHelper');

Use
-------------
###To Hash Password###
    HashHelper::hashPassword($raw_text);
###To verify Password###
    HashHelper::verifyPassword($raw_text, $good_hash));

Format
-------------
we need a char/varchar field for at least 65 column long.It contains 32-digit salt and 32-digit Hash code and a ':' as separator
Yes we store salt and password in the same field, it is easy to handle. And by removing storage of algorithm and iteration, it become simpler than what havoc do.

Config
-------------
###Easy Way###
Don't change any thing.
###Expert Way###
Check out the source page. And my edition as well, you will notice what I simplified.

[PBKDF2 Password Hashing for PHP - Defuse Security](https://defuse.ca/php-pbkdf2.htm)

Advantage
-------------
Nothing special but FREEDOM.

It is easy to use and simple to understand.

It is a stand alone file does not have to extends anything or put in some specific location.
Under LGPL v3 license, you can feel free to modify this code. I don't know which license shall I choose, I think this license is freedom enough, so I picked it.
However, I am not a lawyer, you know. If you have better ideas, please let me know.(∩_∩)

Requirement
-------------
Require PHP5.3++ and MCRYPT extension support.

