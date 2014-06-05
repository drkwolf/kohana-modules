# Email Module For Kohana 3.x

Factory-based email class. This class is a simple wrapper around [Swiftmailer](http://github.com/swiftmailer/swiftmailer).

## Features

 - template file
 - language support (from user setting) //TODO
 - mass mail

## Usage

mails can be create and send in multiple ways, also sending mail in user
language can be easly set.

### Sending mail from template file

```php
    $email = Email::from_file('file_name')
        ->from('you@example.com', 'My Name')
        ->to('person@example.com')
        ->set(':var1' => value) // file varaibles
        ->send()
        ;
```
syntax for template file: the first line must be -- subject, and  the third -- message, like flow:
the default path for email are in `application/email`, template path can be set
then in the constructor.
```
-- subject
the subject :title
-- message
the body
...
```

### Normal way
we can also send mail by specifying body and subject in the constructor.

```php
    $mailer = Email::factory($subject, $body, $msg_type)
        ->from($this->from->email)
        ->to($this->to->email)
        ->send($message_swift);
```
### mass mail
Email::to method accepts array of mail, just get the users mail and put in
Email::to or Email::bcc (hide users mail) to send mail to a list of users.

```php
    $emails = ORM::factory('user')->find_all()->as_array('id', 'email');

    $failds_emails = array();

    $ret = Email::from_file('newsletter')
      ->bcc($emails)
      ->to($newsletter->user->email)
      ->from($newsletter->user->email)
      ->set(':newsletter', $newsletter->html)
      ->set(':title', $newsletter->title)
      ->send($faild_emails)
      ;

    if ( $ret )
    {
      $newsletter->set('sent', true)->save();
      Message::notice("Newsletter set with succcess to all subscriber");
    }
    else
    {
      Message::error("Faild mail ".print_r($failds_emails));
    }
```
 
### errors  //TODO

### misc
Additional recipients can be added using the `to()`, `cc()`, and `bcc()` methods.
 
Additional senders can be added using the `from()` and `reply\_to()` methods. If
multiple sender addresses are specified, you need to set the actual sender of
the message using the `sender()` method. Set the bounce recipient by using the
`return\_path()` method.


## Configuration

Configuration is stored in `config/email.php`. Options are dependant upon transport method used. Consult the Swiftmailer documentation for options available to each transport.

### smpt configuration





To access and modify the [Swiftmailer message](http://swiftmailer.org/docs/messages) directly, use the `raw_message()` method.
