implement i18n :
 - lead the mail message form the file 
 -

file : emails/file.php;
- Email::factory('file')
  ->from($from)
  ->to($to)
  ->set(':var', value)
  ->send()

syntax of the message file :

<subject> subject </subject>

<message>
