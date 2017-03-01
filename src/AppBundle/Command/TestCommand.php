<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trinity\NotificationBundle\Entity\Message;
use Trinity\NotificationBundle\Event\DisableNotificationEvent;
use Trinity\NotificationBundle\Event\Events;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.04.16
 * Time: 9:58.
 */

/**
 * Scenario1:
 * Necktie: Create a new product
 * Venice: => product and billing plan are created and properly associated.
 *
 * Class TestCommand
 */
class TestCommand extends ContainerAwareCommand
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

        $messageData = <<< MESSAGE
        {"messageType":"notification","uid":"5740a59e2895e7.65784898","clientId":"3","parent":null, "createdOn":1463854494,"hash":"0d64df9796ca0ec29d16e84ae690de9bd5eb96117ee7d226b7f1ec363cd164a9","data":"[{\"messageId\":\"5740a59e2895e7.65784898\",\"method\":\"POST\",\"data\":{\"id\":25,\"product\":24,\"initialPrice\":1234,\"rebillPrice\":null,\"frequency\":null,\"rebillTimes\":null,\"trial\":null,\"entityName\":\"billing-plan\"}},{\"messageId\":\"5740a59e2895e7.65784898\",\"method\":\"POST\",\"data\":{\"id\":24,\"name\":\"integration testing\",\"description\":\"description\",\"defaultBillingPlan\":25,\"entityName\":\"product\"}}]"}    
MESSAGE;

        $message = Message::unpack($messageData);

        /** @var array $entities */
        $entities = $reader->read($message);

        /** @var BillingPlan $bp */
        $bp = $entities[0];
        /** @var StandardProduct $product */
        $product = $entities[1];

        if ($product->getDefaultBillingPlan() !== $bp || $bp->getProduct() !== $product) {
//            dump('XXXXXXXXXXXXXXXXXXXX FAILED XXXXXXXXXXXXXXXXXXXX ');
        } else {
//            dump('OOOOOOOOOOOOOOOOOOOOOOOO OK OOOOOOOOOOOOOOOOOOOOOOOO');
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->remove($bp);
        $em->remove($product);
        $em->flush();
        $output->writeln('readed!');
    }

    protected function configure()
    {
        $this->setName('venice:test');
    }
}
