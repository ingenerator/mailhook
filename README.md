# Mailhook

[![License](https://poser.pugx.org/ingenerator/mailhook/license.svg)](https://packagist.org/packages/ingenerator/mailhook)
[![Build status](https://github.com/ingenerator/mailhook/actions/workflows/test.yaml/badge.svg)](https://github.com/ingenerator/mailhook/actions/workflows/test.yaml)
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
want to assert that an email was sent to a specific user. For this, you can use the matching/assertion framework built
into the package:

```php
$mail   = $mailhook->assert()->firstEmailMatching(new EmailSentToMatcher('test@ingenerator.com'));
$emails = $mailhook->assert()->emailsMatching(new EmailSentToMatcher('test@ingenerator.com'));
$mailhook->assert()->noEmailMatching(new AnyEmailMatcher);
```

These assertion methods throw an exception if they fail, or return the matching email(s) if they succeed.
You can add your own custom criteria by implementing the EmailMatcher interface and providing an instance
of the class.

You can pass multiple matchers to assert that an email matching all the criteria was sent. For example, if you were
testing the common "password reset email" feature you could do something like:

```php
$mailhook = new \Ingenerator\Mailhook\Mailhook('/tmp/outgoing_mail.dump');
$mailhook->purge();

submit_my_password_reset_form();

$mail = $mailhook->assert()->firstEmailMatching(
    new EmailSentToMatcher('test@ingenerator.com'),
    new EmailWithLinkMatcher('/reset/')
);
$links  = $mail->getLinksMatching('/reset/');

visit_page_and_reset_password($links[0]->getUrl());
```


## Testing and developing

mailhook has a suite of [PhpSpec](http://phpspec.net) specifications. Run them with `bin/phpspec run`. Contributions
will only be accepted if they are accompanied by well structured specs. Installing with composer should get you
everything you need to work on the project.

## License

mailhook is copyright 2012-2014 inGenerator Ltd and released under the [BSD license](LICENSE).
