<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\ProfilePhoto;
use AppBundle\Entity\User;
use AppBundle\Services\AbstractConnector;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserListener
 * @package AppBundle\UserListener
 */
class UserListener
{
    /** @var   */
    private $updatedFields;

    /** @var ContainerInterface  */
    private $container;

    /** @var null  */
    private $actualLog;

    /**
     * UserListener constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->actualLog = null;
    }


    /**
     * @param PreUpdateEventArgs $eventArgs
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        $this->updatedFields = $eventArgs->getEntityChangeSet();

        if ($entity instanceof User) {
            /** @var User $entity */
            // TODO User listener, action after user was changed

            if (is_null($this->container->get('security.token_storage')->getToken())) {
                return;
            }

            $changeSet = $eventArgs->getEntityChangeSet();

            if (array_key_exists('passwordRequestedAt', $changeSet)) {
                return;
            }

            // ADMIN LOG - IF admin user changes user
            $description = 'User edited id:' . $entity->getId() . ' username: ' . $entity->getUsername() . " \nchanged columns: \n";
            foreach ($changeSet as $key => $value) {
                if ($value[0] == $value[1]) {
                    continue;
                } // fix bug for empty columns
                if (is_object($value[0]) || is_object($value[1])) {

                    if (get_class($value[0]) == 'DateTime') {
                        $dateOld = $value[0]->format('d/m/Y');
                        $dateNew = $value[1]->format('d/m/Y');
                        $description .= $key . ' from: ' . $dateOld . ' to: ' . $dateNew . "\n";
                    } else {
                        // other objects now not logging
                    }
                } else { // can be converted to string
                    $description .= $key . ' from: ' . $value[0] . ' to: ' . $value[1] . "\n";
                }
            }

            /* @todo
            $log = new AuditLog();
            $log->setDescription($description);
            $log->setType('User updated');
            $log->setEventTime(new \DateTime());
            $log->setUser($this->container->get('security.token_storage')->getToken()->getUser()->getUsername());
            $log->setTypeId('user.updated');
            $log->setIp($this->container->get('request_stack')->getCurrentRequest()->getClientIp());
            $this->actualLog = $log;
            // END OF ADMIN LOG
             */
        }

        if ($entity instanceof ProfilePhoto) {
            /** @var ProfilePhoto $entity */
            $generator = $this->container->get('flofit.services.profile_photo_url_generator');

            $originalUrl = $generator->generateUrlToOriginalPhoto($entity);
            $croopedUrl = $generator->generateUrlToCroppedPhoto($entity);

            $entity->setOriginalPhotoUrl($originalUrl);
            $entity->setCroopedPhotoUrl($croopedUrl);
        }
    }


    /**
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        if (is_null($eventArgs)) {
            return;
        }

        $entity = $eventArgs->getEntity();

        if ($entity instanceof User) {
            $updatedFields = [];
            foreach ($this->updatedFields as $updatedFieldKey => $updatedFieldValues) {
                if (!in_array($updatedFieldKey, ['username', 'email'])) {
                    $updatedFields[] = $updatedFieldKey;
                }
            }

            /** @var AbstractConnector $connector */
            $connector = $this->container->get($this->container->getParameter('connector_service_name'));
            $connector->updateUser($entity, $updatedFields);


            /** @var User $entity */
            if (!is_null($this->actualLog)) {
                // TODO User listener, action after user was changed
                $loggerService = $this->container->get('xiidea.easy_audit.logger.service');
                $loggerService->log($this->actualLog);
            }
        }

        /* @todo
        if ($entity instanceof Post) {
            /** @var Post $entity */ /*
            try {
                $message = \Swift_Message::newInstance();

                $message
                    ->setSubject('Blog post change')
                    ->setTo('bohac@webvalley.cz')
                    ->setFrom('support@flofit.com')
                    ->setBody($entity->getContent());

                $this->container->get('mailer')->send($message);
            } catch (\Exception $e) {

            }
        *
        }*/
    }
}
