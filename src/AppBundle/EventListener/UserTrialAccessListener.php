<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductAccess;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManager;
use Venice\AppBundle\Entity\User;

/**
 * Class UserTrialAccessListener
 * @package AppBundle\EventListener
 */
class UserTrialAccessListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorage
     */
    private $token;

    /**
     * @var SettingsManager
     */
    private $settings;

    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * UserTrialAccessListener constructor.
     *
     * @param TokenStorage $token
     * @param SettingsManager $settings
     * @param EntityManager $entityManager
     */
    public function __construct(TokenStorage $token, SettingsManager $settings, EntityManager $entityManager)
    {
        $this->token = $token;
        $this->settings = $settings;
        $this->entityManager = $entityManager;
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest'
        ];
    }


    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() &&
            $event->getRequest()->get('_route') !== 'downloads_dashboard'
        ) {
            return;
        }

        if ($this->token->getToken() === null) {
            return;
        }

        /** @var User $user */
        $user = $this->token->getToken()->getUser();
        $userId = is_object($user) ? $user->getId() : null;

        if (!$userId) {
            return;
        }

        $this->processUserAccess($user);
    }


    /**
     * @param User $user
     *
     * @throws \Exception
     */
    private function processUserAccess(User $user)
    {
        $userId = $user->getId();
        $now = new \DateTime();
        [$trialStart, $trialEnd] = $this->getStartAndEndDate($userId);

        if ($trialStart <= $now && $trialEnd <= $now) {
            // has trial
            $product = $this
                ->entityManager
                ->getRepository(StandardProduct::class)
                ->findOneBy(['handle' => 'flofit']);

            // trial vypršel
            if ($now > $trialEnd) {
                $access = $this
                    ->entityManager
                    ->getRepository(ProductAccess::class)->findOneBy(['user' => $user, 'product' => $product]);

                if ($access) {
                    return;
                }
            }

            if ($product && !$user->hasAccessToProduct($product)) {
                $this->createUserAccess($user, $product);
            }
        }
    }


    /**
     * @param int $userId
     *
     * @return array
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    private function getStartAndEndDate(int $userId)
    {
        /** @var \Datetime $trialStart */
        $trialStart = $this->settings->get('trialStart', $userId, 'user');

        /** @var \DateTime $trialEnd */
        $trialEnd = $this->settings->get('trialEnd', $userId, 'user');

        if ($trialStart === null) {
            $trialStart = new \DateTime();
            $trialEnd   = new \DateTime();
            $trialEnd->add(new \DateInterval('P7D'));

            $this->settings->set('trialStart', $trialStart, $userId, 'user');
            $this->settings->set('trialEnd', $trialEnd, $userId, 'user');
        }

        return [$trialStart, $trialEnd];
    }


    /**
     * @param User $user
     *
     * @param $product
     *
     * @return ProductAccess
     * @throws \Exception
     */
    private function createUserAccess(User $user, StandardProduct $product)
    {
        $this->entityManager->beginTransaction();
        [$trialStart, $trialEnd] = $this->getStartAndEndDate($user->getId());

        try {
            $access = new ProductAccess();
            $access->setUser($user);
            $access->setProduct($product);
            $access->setCreatedAt(new \DateTime());
            $access->setFromDate($trialStart);
            $access->setToDate($trialEnd);
            $access->setNecktieId(1);

            $product->addProductAccess($access);

            $this->entityManager->persist($access);
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $access;
    }
}
