<?php
namespace TurboPancake;

use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;

class MailerFactory {

    public function __invoke(ContainerInterface $c): PHPMailer
    {
        $mailer = new PHPMailer();

        $mailer->SMTPDebug = $c->get('email.debug.level');
        $mailer->setLanguage($c->get('email.debug.lang'));
        $mailer->isSMTP();

        //Server settings
        $mailer->Host = $c->get('email.host');
        $mailer->Port = $c->get('email.port');
        if (in_array($c->get('email.encryption'), ['tls', 'ssl'])) {
            $mailer->SMTPSecure = $c->get('email.encryption');
        }

        //Auth settings
        $mailer->SMTPAuth = true;
        $mailer->Username = $c->get('email.username');
        $mailer->Password = $c->get('email.password');

        //Email settings
        $mailer->setFrom($c->get('email.from.adress'), $c->get('email.from.name'));
        foreach ($c->get('email.replyTo') as $replyToEmail => $replyToName) {
            $mailer->addReplyTo($replyToEmail, $replyToName);
        }

        return $mailer;
    }

}