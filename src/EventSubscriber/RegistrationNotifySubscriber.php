<?php

namespace App\EventSubscriber;

use App\Events;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Envoi un mail de bienvenue à chaque creation d'un utilisateur
 *
 */
class RegistrationNotifySubscriber implements EventSubscriberInterface
{
    private $mailer;
    //private $sender;

    public function __construct(MailerInterface $mailer/*, $sender*/)
    {
        // On injecte notre expediteur et la classe pour envoyer des mails
        $this->mailer = $mailer;
        //$this->sender = $sender;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // le nom de l'event et le nom de la fonction qui sera déclenché
            Events::USER_REGISTERED => 'onUserRegistrated',
        ];
    }

    public function onUserRegistrated(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();
        $subject = 'Merci pour l\'enregistrement !';

        $email = (new TemplatedEmail())
            ->from('vintage.grabli@gmail.com')
            ->to($user->getEmail())//$user->getEmail()
            ->subject($subject)

            // path of the Twig template to render
            ->htmlTemplate('security/signup_brindille.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => $user->getUsername(),
                'civilite' => $user->getCivilite(),
            ]);

        $this->mailer->send($email);
    }
    
}