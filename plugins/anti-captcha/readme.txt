=== Anti-Captcha (anti-spam botblocker) ===
Contributors: filiw
Donate link: http://blog.fili.nl/wordpress-anti-captcha-plugin/
Tags: spam, filter, blocker, anti-spam, anti-captcha, captcha, comments, register, botblocker, bot, bots, attack, protect 
Requires at least: 2.8.4
Tested up to: 4.0.0
Stable tag: 20141103

Anti-Captcha is a transparent spam solution that does not require any end-user interaction.

== Description ==

Anti-Captcha is a transparent spam solution that does not require any end-user interaction.
It is based on a nonce key, which is dynamically inserted using randomly generated (and obfuscated) javascript.

The aim of this plugin is to prevent automated attacks (by bots) on the following WordPress actions:

* Posting comments
* Registering for a new account
* Requesting a lost password

When a comment is posted without a valid Anti-Captcha token, it shall be *instantly marked as spam*. This way, you can always manually approve this comment in hindsight if it appeared to be sincere.

== Installation ==

To install simply:

1. Upload the 'anti-captcha' folder to the /wp-content/plugins/ directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Is javascript required by the user? =
Yes, however this sounds worse then it is.
Generally, it's frowned upon if you don't write javascript in an unobstructive way.
The reason for this is that some visitors don't support javascript but should still be able to get around your website.

AFAIK there are four types of user-agents not supporting javascript:

* Search-engine spider bots
* Users of a command-line browser (like Lynx)
* Users who actively disabled javascript in their browser
* Mischievous bots trying to spam or hack into your blog

Obviously, search-engines don't need to comment, register or login so they can be ruled out.
Lynx users and users with javascript disabled are likely to be a *very small* percentage of the internet population, who have actively excluded themselves from certain webfeatures. Finally, badly behaving bots, is what the Anti-Captcha plugin is trying to block.


== Screenshots ==

Anti-Captcha is works it's magic in the background and invisible to you and your users. 

== Changelog ==

= 20141103 =
* Fixed a tagging mistake causing a "The plugin does not have a valid header" error for new installations
* Added a plugin icon

= 20140908 =
* Tested on WordPress version 4.0

= 20140129 =
* Fixed a bug that always marked legitimate comments to be moderated

= 20140128 =
* Fixed a bug that broke wordpress discussion settings
* Improved code compatibility
* PhantomJs headless browser detection
* Tested on WordPress version 3.8.1

= 20140102 =
* Tested on WordPress version 3.8

= 20130927 =
* Fixed a bug in which the 'An administrator must always approve the comment' settings was ignored

= 20130504 =
* Fixed a bug in which legitimate comments where always flagged for moderation
* Added a check on the format of the supplied mailaddress and it's MX-records (on fail, a comment will be held for moderation instead of being approved)

= 20130429 =
* Updated anti-captcha to version 0.3 which introduces a new DOMReady loading method
* This version also prevents a 'alreadyrunflag is not defined' javascript error

= 20130421 =
* Tested plugin on WordPress 3.5.1 install, everything works as expected
* Linked to new blog article at http://blog.fili.nl/wordpress-anti-captcha-plugin/
* Version bump to remove WordPress 'Out of date' alert

= 20110129 =
* Fixed regression bug that prevented anti-captcha to work on registration and lost-password form

= 20110125 =
* Tested on WordPress version 3.0.4
* Removed anti-captcha from login procedure

= 20100708 =
* Tested on WordPress version 3.0

= 20100426 =
* Changed error message to be more descriptive
* Changed cookie mechanism to not rely on PHP sessions
* Added 'Back/Forward Cache' prevention
* Removed jQuery dependency
* Tested on WordPress version 2.9.2

= 20090821 =
* First release 
