# Mailhook

[![License](https://poser.pugx.org/ingenerator/mailhook/license.svg)](https://packagist.org/packages/ingenerator/mailhook)
[![Master Build Status](https://travis-ci.org/ingenerator/mailhook.png?branch=master)](https://travis-ci.org/ingenerator/mailhook)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ingenerator/mailhook/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ingenerator/mailhook/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ingenerator/mailhook/v/stable.svg)](https://packagist.org/packages/ingenerator/mailhook)
[![Total Downloads](https://poser.pugx.org/ingenerator/mailhook/downloads.svg)](https://packagist.org/packages/ingenerator/mailhook)
[![Latest Unstable Version](https://poser.pugx.org/ingenerator/mailhook/v/unstable.svg)](https://packagist.org/packages/ingenerator/mailhook)

A php library that collects email from a local postfix server so you can inspect, assert and otherwise mess around with
emails sent during development.

## Installing

Add mailhook to your development dependencies in composer.json:

```json
{
  "require-dev": {
    "ingenerator/mailhook" : "~0.1@dev"
  }
}
```

You will also need to configure postfix to deliver all outbound mail to a local file. If you're using chef, see our
[postfix-relay](https://github.com/ingenerator/chef-postfix-relay) cookbook, and configure the
"postfix_relay.allow_live_email" attribute to false. To install manually, `apt-get install postfix` and then append
to your postfix configuration as follows.

```
# /etc/postfix/main.cf
default_transport = fs_mail
```

```
# /etc/postfix/master.cf

#
# fs_mail sends all outgoing mail to a single local file
#
fs_mail    unix  -       n       n       -       -       pipe
   flags=FB user=ubuntu argv=tee -a /tmp/outgoing_mail.dump
```

## Using mailhook to inspect messages

Obviously you'd usually use mailhook inside a test framework of some kind (Behat, for example). But this very simple
example should give you an idea of how you can use it:

```php
$mailhook = new \Ingenerator\Mailhook\Mailhook('/tmp/outgoing_mail.dump');
// You'll usually want to purge the file before your tests, to ensure you have a clean state
$mailhook->purge();

run_my_code_that_should_send_emails();

$mailhook->refresh();

$mails = $mailhook->getEmails();
assert(count($mails) === 1, 'An email was sent');
```

### Getting more detail

You probably want to know more than just that an email with some content was sent to some user. For example, you might
want to assert that an email was sent to a specific user. For this, you can use the MailhookAsserter class, which 
provides a number of common helper methods:

```php
$asserter = new MailhookAsserter($mailhook);
$mail = $asserter->emailSentTo('test@ingenerator.com');
```

These assertions throw an exception if they fail, or return the matching email(s) if they succeed. You can use this 
returned email object to run further assertions, or to capture content from the body to use in your scenario. For
example, if you were testing the common "password reset email" feature you could do something like:

```php
$mailhook = new \Ingenerator\Mailhook\Mailhook('/tmp/outgoing_mail.dump');
$mailhook->purge();

submit_my_password_reset_form();

$assert = new MailhookAsserter($mailhook);
$mail   = $asserter->emailSentTo('test@ingenerator.com');
$links  = $mail->getLinksMatching('/reset/');

visit_page_and_reset_password($links->getUrl());
```


## Testing and developing

mailhook has a suite of [PhpSpec](http://phpspec.net) specifications. Run them with `bin/phpspec run`. Contributions
will only be accepted if they are accompanied by well structured specs. Installing with composer should get you
everything you need to work on the project.

## License

mailhook is copyright 2012-2014 inGenerator Ltd and released under the [BSD license](LICENSE).
