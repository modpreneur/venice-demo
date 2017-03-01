<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trinity\NotificationBundle\Entity\Message;
use Trinity\NotificationBundle\Event\DisableNotificationEvent;
use Trinity\NotificationBundle\Event\Events;
use Trinity\NotificationBundle\Exception\AssociationEntityNotFoundException;

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.04.16
 * Time: 9:58.
 */

/**
 * Scenario2:
 * Necktie: edit product which is not synchronized
 * Venice: => throws AssociationEntityNotFoundException.
 *
 * Class Test2Command
 */
class Test2Command extends ContainerAwareCommand
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = $this->getContainer()->get('trinity.notification.reader');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        if ($dispatcher->hasListeners(Events::DISABLE_NOTIFICATION)) {
            $event = new DisableNotificationEvent();
            $dispatcher->dispatch(
                Events::DISABLE_NOTIFICATION,
                $event
            );
        }

        $message = <<< MESSAGE
        {"messageType":"notification","uid":"5746d033c0e216.83570307","clientId":"3","createdOn":1464258611,"hash":"074f65659ac6659d7f261bf8deec55e05074a74a1d612b8f59f49b2ba5eb85b2","data":"[{\"messageId\":\"5746d033c0e216.83570307\",\"method\":\"PUT\",\"data\":{\"id\":1,\"name\":\"GoDaddy\",\"description\":\"unsynchronized product edition\",\"defaultBillingPlan\":1,\"entityName\":\"product\"}}]","parent":null}    
MESSAGE;

        $message = Message::unpack($message);
        try {
            $entities = $reader->read($message);
//            dump('XXXXXXXXXXXXXXXXXXXX FAILED XXXXXXXXXXXXXXXXXXXX ');
        } catch (AssociationEntityNotFoundException $e) {
//            dump('OOOOOOOOOOOOOOOOOOOOOOOO OK OOOOOOOOOOOOOOOOOOOOOOOO');
        }

        $output->writeln('readed!');
    }

    protected function configure()
    {
        $this->setName('venice:test2');
    }
}
