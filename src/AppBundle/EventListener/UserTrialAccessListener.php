<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\ProductGroup;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManager;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Interfaces\NecktieGatewayInterface;

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
     * @var NecktieGatewayInterface
     */
    private $necktieGateway;

    /**
     * necktie id of the billing plan, which is used to give user an facebook access
     *
     * @var int
     */
    private $necktieFacebookTrialBillingPlan;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UserTrialAccessListener constructor.
     *
     * @param TokenStorage $token
     * @param SettingsManager $settings
     * @param EntityManager $entityManager
     * @param NecktieGatewayInterface $necktieGateway
     * @param int $necktieFacebookTrialBillingPlan
     */
    public function __construct(TokenStorage $token, SettingsManager $settings, EntityManager $entityManager, NecktieGatewayInterface $necktieGateway, LoggerInterface $logger, int $necktieFacebookTrialBillingPlan)
    {
        $this->token = $token;
        $this->logger = $logger;
        $this->settings = $settings;
        $this->entityManager = $entityManager;
        $this->necktieGateway = $necktieGateway;
        $this->necktieFacebookTrialBillingPlan = $necktieFacebookTrialBillingPlan;
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }


    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        return; //todo: @TomasJancar - uncomment when the necktie version on amazon has the newest code

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
        [$trialStart, $trialEnd] = $this->getStartAndEndDate($userId);
        $now = new \DateTime();

        if ($trialStart <= $now && $trialEnd <= $now) {
            // had trial and the trial is over, now
            $product = $this
                ->entityManager
                ->getRepository(StandardProduct::class)
                ->findOneBy(['handle' => ProductGroup::HANDLE_FLOFIT]);

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
            $trialEnd = new \DateTime();
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function createUserAccess(User $user, StandardProduct $product)
    {
        $billPlanId = $this->necktieFacebookTrialBillingPlan;
        $givenProductAccess = null;
        $this->entityManager->beginTransaction();

        try {
            $productAccessId = $this->necktieGateway
                ->createTrialProductAccess($user, $billPlanId);

            if (!$productAccessId) {
                $this->logger->error('Could not create trial product access on necktie with necktie id: '.$billPlanId);

                return null;
            }

            $givenProductAccess = $this->necktieGateway->getProductAccess($user, $productAccessId);
            if (!$givenProductAccess) {
                $this->logger->error(
                    'Could not get trial product access from necktie with necktie id: '.$productAccessId
                );

                return null;
            }

            $this->entityManager->persist($givenProductAccess);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->logger->error('Could not persist trial product access from necktie due to exception', [$exception]);
            $this->entityManager->rollback();
            $givenProductAccess = null;
        }

        return $givenProductAccess;
    }
}
