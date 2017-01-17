<?php

namespace FrontBundle\Controller;

use Assetic\Asset\GlobAsset;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use GeneralBackend\CoreBundle\Entity\Invoice;
use GeneralBackend\CoreBundle\Entity\Newsletter;
use GeneralBackend\CoreBundle\Entity\PrivacySettings;
use GeneralBackend\CoreBundle\Form\Type\PrivacySettingsType;
use GeneralBackend\CoreBundle\Helpers\Ajax;
use GeneralBackend\CoreBundle\Helpers\FlashMessages;
use GeneralBackend\DownloadsBundle\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Anchovy\CURLBundle\CURL\Curl;
use Doctrine\ORM\EntityManager;
use GeneralBackend\CoreBundle\Entity\GlobalUser;
use GeneralBackend\CoreBundle\Form\Handler\GlobalUserHandler;
use GeneralBackend\CoreBundle\Form\Type\GlobalUserType;
use GeneralBackend\CoreBundle\Services\AmemberConnector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Class StaticPagesController
 * @package GeneralBackend\CoreBundle\Controller\Front
 */
class StaticPagesController extends BaseController
{
    /**
     * @Route(  "/{handle}",
     *          requirements={
     *          "handle": "about|terms-of-use|privacy-policy|help-and-faq|shipping-policy|help|faq",
     *          },
     *          name="core_front_staticpages")
     * @return Response
     */
    public function staticPageAction($handle)
    {
        $staticPagesService = $this->get('flofit.static_pages_service');
        $page = $staticPagesService->getPageBody($handle);
        return $this->render('VeniceFrontBundle:StaticPages:generic.html.twig', ['page' => $page]);
    }


    /**
     * @Route("/social-media", name="core_front_staticpages_social_media")
     * @return Response
     */
    public function socialMediaAction()
    {
        return $this->render('VeniceFrontBundle:StaticPages:mobileSocialMedia.html.twig');
    }
}
