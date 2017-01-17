<?php

namespace FrontBundle\Twig;

use Aws\CloudFront\CloudFrontClient;
use GeneralBackend\CoreBundle\Entity\GlobalUser;
use GeneralBackend\CoreBundle\Entity\Vanilla\Conversation;
use GeneralBackend\DownloadsBundle\Entity\BundleProduct;
use GeneralBackend\DownloadsBundle\Entity\BuyParameters;
use GeneralBackend\DownloadsBundle\Entity\DownloadProduct;
use GeneralBackend\DownloadsBundle\Entity\Product;
use GeneralBackend\DownloadsBundle\Entity\ShippingProduct;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class FlofitTemplateFeatures
 * @package FrontBundle\Twig
 */
class FlofitTemplateFeatures extends \Twig_Extension
{
    private $serviceContainer;

    private $forumConnector;

    private $tokenStorage;

    private $staticPagesService;


    /**
     * FlofitTemplateFeatures constructor.
     *
     * @param ContainerInterface $serviceContainer
     * @param TokenStorage $tokenStorage
     */
    public function __construct(ContainerInterface $serviceContainer, TokenStorage $tokenStorage)
    {
        $this->serviceContainer = $serviceContainer;
        $this->tokenStorage     = $tokenStorage;

        //$this->forumConnector     = $serviceContainer->get($this->serviceContainer->getParameter("forum_service_name"));
        //$this->staticPagesService = $serviceContainer->get("general_backend_core.services.static_pages_service");
    }


    /**
     * @param string $html
     *
     * @return \Twig_Markup
     */
    private function html(string $html)
    {
        return new \Twig_Markup($html, 'UTF-8');
    }


    /**
     * @return \Twig_Markup
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function notificationBlock()
    {
        $templater = $this->serviceContainer->get('templating');

        $output = $templater->render(
            'VeniceFrontBundle:FlofitFeatures:notificationBlock.html.twig',
            [
                'count' => 10
            ]
        );

        return $this->html($output);
    }


    /**
     * @return \Twig_Markup
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function footerLinksBlock()
    {
        $templater = $this->serviceContainer->get('templating');

        $html = $templater->render(
            'VeniceFrontBundle:FlofitFeatures:footerLinksBlock.html.twig'
        );

        return $this->html($html);
    }


    /**
     * @return mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function messagesBlock()
    {
        $templater = $this->serviceContainer->get('templating');

        $user = $this->getUser();

        if (is_null($user)) {
            throw new AuthenticationException('User must be logged');
        }

        if ($this->forumConnector) {
            $conversations = $this->forumConnector->getConversations($user);
        } else {
            $conversations = [];
        }

        $unreaded = 0;
        foreach ($conversations as $conversation) {
            /** @var Conversation $conversation */
            if ($conversation->getCountNewMessages() > 0) {
                $unreaded += $conversation->getCountNewMessages();
            }
        }

        $readAllUrl = $this->serviceContainer->getParameter('forum_read_all_url');
        $conversationUrl = $this->serviceContainer->getParameter('forum_read_conversation_url');

        $html =  $templater->render(
            'VeniceFrontBundle:FlofitFeatures:messagesBlock.html.twig',
            [
                'readAllUrl'      => $readAllUrl,
                'conversationUrl' => $conversationUrl,
                'conversations'   => $conversations,
                'count'           => $unreaded > 99 ? 99 : $unreaded
            ]
        );

        return $this->html($html);
    }


    /**
     * @param Product $product
     * @param $template
     * @param bool $solveAccess
     * @param bool $dummy
     *
     * @return string|\Twig_Markup
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function productRenderer(Product $product, $template, $solveAccess = true, $dummy = false)
    {
        $templater = $this->serviceContainer->get('templating');
        if ($product instanceof ShippingProduct) {
            return '';
        } else {
            if ($product instanceof BundleProduct) {
                $result = "";
                foreach ($product->getSubProducts() as $subProduct) {
                    $result .= $this->productRenderer($subProduct, $template);
                }

                return $result;
            }

            try {
                $html =  $templater->render(
                    "DownloadsBundle/" . $template . "/" . strtolower($product->getTypeString()) . ".html.twig",
                    [
                        "access"        => $solveAccess ? ($this->getUser()->haveAccess($product)) : true,
                        "daysRemaining" => $solveAccess ? $this->getUser()->daysRemainingToUnlock($product) : 0,
                        "product"       => $product,
                        "dummy"         => $dummy
                    ]
                );

                return $this->html($html);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }


    public function generateSecureLink(DownloadProduct $product, $expireSeconds = null, $onlyForRequesterIp = false)
    {
        if (strlen($product->getFileProtected()) == 0) {
            return $product->getFile();
        }

        $rootDir = $this->serviceContainer->get("kernel")->getRootDir();

        $cloudFront = CloudFrontClient::factory(array(
            "private_key" => $rootDir . "/crt/pk-APKAJWKWONDIO5YORBMA.pem",
            "key_pair_id" => "APKAJWKWONDIO5YORBMA"
        ));

        $expires = time() + (int)($this->serviceContainer->getParameter("amazon_cloud_front_link_expiration"));

        return $cloudFront->getSignedUrl(array(
            "url" => $product->getFileProtected(),
            "expires" => $expires
        ));
    }


    public function minutesAndSecondsString($seconds)
    {
        $mins = floor($seconds / 60);
        $secs = floor($seconds % 60);

        return str_pad($mins, 2, "0", STR_PAD_LEFT) . ":" . str_pad($secs, 2, "0", STR_PAD_LEFT);
    }


    public function getForumLink()
    {
        return $this->serviceContainer->getParameter("forum_url");
    }


    public function getMessagesLink()
    {
        return $this->serviceContainer->getParameter("messages_url");
    }


    public function getValueFromParameter($parameter)
    {
        return $this->serviceContainer->getParameter($parameter);
    }


    public function generateOCBLink(
        Product $product,
        $useStoredCard,
        GlobalUser $user = null,
        array $otherParams = array()
    ) {
        return $this->generateOCBLinkByBuyParameters($product->getBuyCBParameters(), $useStoredCard, $user,
            $otherParams);
    }


    public function generateMobileOCBLink(Product $product, GlobalUser $user = null, array $otherParams = array())
    {
        return $this->generateOCBLinkByBuyParameters($product->getBuyCBMobileParameters(), true, $user, $otherParams,
            "ocb-mobile");
    }


    public function generateOCBLinkByBuyParameters(
        BuyParameters $buyParameters,
        $useStoredCard,
        GlobalUser $user = null,
        array $otherParams = array(),
        $ocbAction = "ocb"
    ) {
        $amemberURL = $this->serviceContainer->getParameter("amember_url");
        $secretKey = $this->serviceContainer->getParameter("amember_user_hash_key");

        if (is_null($user)) {
            $user = $this->getUser();
        }

        if (substr($amemberURL, strlen($amemberURL)) != "/") {
            $amemberURL .= "/";
        }

        $amemberURL .= "payment/c-b/{$ocbAction}?";

        $buyParameters = $buyParameters->generateOCBLink($user, $secretKey, $useStoredCard);

        if (is_null($buyParameters)) {
            return "";
        }

        $otherParamsString = "";
        if (count($otherParams) > 0) {
            $otherParamsString = "&" . http_build_query($otherParams);
        }

        return $amemberURL . $buyParameters . $otherParamsString;
    }


    public function getLastCard(GlobalUser $user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }

        /** @var AbstractConnector $connector */
        $connector = $this->serviceContainer->get($this->serviceContainer->getParameter("connector_service_name"));

        $lastPaymentInfo = $connector->getLastPaymentInfo($user);
        if (is_null($lastPaymentInfo)) {
            return "stored";
        }

        return $lastPaymentInfo->getPaymentMethod();
    }


    /**
     * @param BuyParameters $buyParameters
     *
     * @return string
     */
    public function generatePriceString(BuyParameters $buyParameters)
    {
        return $buyParameters->getPriceString();
    }


    /**
     * @return string
     */
    public function passwordCheckerBlock()
    {

        return '';
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_Function(
                'notificationBlock',
                [
                    $this, 'notificationBlock',
                    ['is_safe' => ['html']]
                ]
            ),
            new \Twig_Function('messagesBlock', [$this, 'messagesBlock', ['is_safe' => ['html']]]),

            new \Twig_Function('footerLinksBlock', [$this, 'footerLinksBlock',
                array('is_safe' => array('html'))
            ]),

            new \Twig_Function(
                'productRenderer',
                [$this, 'productRenderer', array('is_safe' => array('html'))]
            ),

            new \Twig_Function(
                'generateSecureLink',
                [$this, 'generateSecureLink',
                array('is_safe' => array('html'))]
            ),

            new \Twig_Function('minutesAndSecondsString', [$this, 'minutesAndSecondsString']),

            new \Twig_Function(
                'passwordCheckerBlock',
                [
                    $this,
                    'passwordCheckerBlock',
                    array('is_safe' => array('html'))
                ]
            ),

            new \Twig_Function('generateOCBLink', [$this, 'generateOCBLink']),
            new \Twig_Function('generateMobileOCBLink', [$this, 'generateMobileOCBLink']),
            new \Twig_Function('generatePriceString', [$this, 'generatePriceString']),
            new \Twig_Function('getLastCard', [$this, 'getLastCard']),
            new \Twig_Function('getForumLink', [$this, 'getForumLink']),
            new \Twig_Function('getMessagesLink', [$this, 'getMessagesLink']),
            new \Twig_Function('getValueFromParameter', [$this, 'getValueFromParameter']),
        );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'flofit_features';
    }


    /**
     * @return GlobalUser|null
     */
    private function getUser()
    {
        $user = null;
        if (!is_null($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();

            return is_object($user) ? $user : null;
        }

        return $user;
    }
}
