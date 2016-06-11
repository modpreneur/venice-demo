<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trinity\NotificationBundle\Event\DisableNotificationEvent;
use Trinity\NotificationBundle\Event\Events;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.04.16
 * Time: 9:58
 */


class Test3Command extends ContainerAwareCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = $this->getContainer()->get('trinity.notification.message_reader');

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        if ($dispatcher->hasListeners(Events::DISABLE_NOTIFICATION)) {
            dump("DISAVBLE");
            $event = new DisableNotificationEvent();
            $dispatcher->dispatch(
                Events::DISABLE_NOTIFICATION,
                $event
            );
        }

        $message = <<< MESSAGE
        {"messageType":"notification","uid":"5731e9b485a2d5.49020542","clientId":"3","timestamp":1462888884,"hash":"dfaa5f60cf171bd0df61e5ce4f58b135a2388907d7b7663b1f801515cb08e5a4","notifications":[{"batchId":"5731e9b485a2d5.49020542","method":"POST","data":{"id":142,"product":129,"initialPrice":323,"rebillPrice":null,"frequency":null,"rebillTimes":null,"trial":null,"entityName":"billing-plan"}}]}
MESSAGE;

        $entities = $reader->read($message);

        $em = $this->getContainer()->get("doctrine.orm.entity_manager");
        foreach ($entities as $entity) {
            $em->remove($entity);
        }
        $em->flush();
        $output->writeln("readed!");
    }


    protected function configure()
    {
        $this->setName('venice:test3');
    }
}