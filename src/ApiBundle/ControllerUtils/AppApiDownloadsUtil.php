<?php

namespace ApiBundle\ControllerUtils;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Content\ContentInGroup;
use AppBundle\Entity\Content\GroupContent;
use AppBundle\Entity\Content\PdfContent;
use AppBundle\Entity\Content\VideoContent;
use AppBundle\Entity\ContentProduct;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductGroup;
use AppBundle\Entity\User;
use AppBundle\Entity\UserPlayedVideos;
use Doctrine\ORM\EntityManagerInterface;
use Trinity\Component\Utils\Services\PriceStringGenerator;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Interfaces\ContentInterface;
use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Services\BuyUrlGenerator;
use Venice\AppBundle\Services\InvoiceOrderService;
use Venice\AppBundle\Services\NecktieGateway;

/**
 * Class AppApiDownloadsUtil
 */
class AppApiDownloadsUtil
{
    /**
     * @var BuyUrlGenerator
     */
    protected $buyUrlGenerator;

    /**
     * @var PriceStringGenerator
     */
    protected $priceGenerator;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var InvoiceOrderService
     */
    protected $invoiceOrderService;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var NecktieGateway
     */
    protected $necktieGateway;

    /**
     * @var Order[]
     */
    protected $userOrders;

    /**
     * AppApiDownloadsUtil constructor.
     * @param BuyUrlGenerator $buyUrlGenerator
     * @param PriceStringGenerator $priceGenerator
     * @param EntityManagerInterface $entityManager
     * @param InvoiceOrderService $orderService
     * @param NecktieGateway $necktieGateway
     */
    public function __construct(
        BuyUrlGenerator $buyUrlGenerator,
        PriceStringGenerator $priceGenerator,
        EntityManagerInterface $entityManager,
        InvoiceOrderService $orderService,
        NecktieGateway $necktieGateway
    ) {
        $this->buyUrlGenerator = $buyUrlGenerator;
        $this->priceGenerator = $priceGenerator;
        $this->entityManager = $entityManager;
        $this->invoiceOrderService = $orderService;
        $this->necktieGateway = $necktieGateway;
    }

    /**
     * @param User $user
     * @param StandardProduct $product
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     */
    public function getDataForProduct(User $user, StandardProduct $product): ?array
    {
        try {
            $this->userOrders = $this->necktieGateway->getOrders($user);
        } catch (\Throwable $exception) {
            $this->userOrders = [];
        }

        if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT) {
            $repo = $this->entityManager->getRepository(StandardProduct::class);
            $data = [];

            $data[] = $this->getProductData($user, $product);

            $product = $repo->findOneBy(['handle' => ProductGroup::HANDLE_7_DAY_RIP_MIX]);
            if ($product) {
                $data[] = $this->getProductData($user, $product);
            }

            $product = $repo->findOneBy(['handle' => ProductGroup::HANDLE_NUTRITION_AND_MEALS]);
            if ($product) {
                $data[] = $this->getProductData($user, $product);
            }

            $product = $repo->findOneBy(['handle' => ProductGroup::HANDLE_PLATINUM_MIX]);
            if ($product) {
                $data[] = $this->getProductData($user, $product);
            }

            return $data;
        }
        return [$this->getProductData($user, $product)];
    }

    /**
     * @param User $user
     * @param StandardProduct $product
     *
     * @return array
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     * @throws \Exception
     */
    protected function getProductData(User $user, StandardProduct $product): array
    {
        $this->user = $user;

        $subProducts = [];
        if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT) {
            $subProducts[] = $this->getFlofitShippingProductData();
        }

        return [
            'type' => 'bundleproduct',
            'access' => $user->hasAccessToProduct($product),
            'moduleNumber' => 1,
            'openIn' => 0,
            'buylink' => $this->getProductBuyLink($product),
            'price' => $this->getProductPrice($product),
            'subProducts' => array_merge($subProducts, $this->getContentFromProduct($product)),
            'shortName' => $product->getShortName(),
            'upselMiniatureMobile' => $product->getUpselMiniatureMobile(),
            'shortDescription' => $product->getShortDescription(),
            'longDescription' => $product->getDescriptionForCustomer(),
            'isRecommended' => $product->isIsRecommended(),
            'customTemplateName' => $product->getCustomTemplateName(),
            'upsellOrder' => $product->getUpsellOrder(),
            'id' => $product->getId(),
            'name' => $product->getName(),
            'image' => $product->getImage(),
            'description' => $product->getDescription(),
            'orderNumber' => $product->getOrderNumber(),
            'delayed' => false,
            'invoiceOrder' => $this->invoiceOrderService->getInvoiceOrderForProductName(
                $this->userOrders,
                $product->getName()
            ),
        ];
    }

    /**
     * This must have the same output as the getProductData() method
     *
     * @param User $user
     * @param StandardProduct $product
     * @param GroupContent $group
     * @return array
     * @throws \Exception
     */
    protected function getGroupContentData(User $user, StandardProduct $product, GroupContent $group): array
    {
        $this->user = $user;

        return [
            'type' => 'bundleproduct',
            'access' => $user->hasAccessToProduct($product),
            'moduleNumber' => 1,
            'openIn' => 0,
            'buylink' => $this->getProductBuyLink($product),
            'price' => $this->getProductPrice($product),
            'subProducts' => $this->getContentsFromGroup($group, $this->user->hasAccessToProduct($product)),
            'shortName' => $group->getName(),
            'upselMiniatureMobile' => $product->getUpselMiniatureMobile(),
            'shortDescription' => $group->getDescription(),
            'longDescription' => $group->getDescription(),
            'isRecommended' => false,
            'customTemplateName' => $product->getCustomTemplateName(),
            'upsellOrder' => 0,
            'id' => $group->getId(),
            'name' => $group->getName(),
            'image' => $product->getImage(),
            'description' => $group->getDescription(),
            'orderNumber' => 0,
            'delayed' => false,
            'invoiceOrder' => 0,
        ];
    }

    /**
     * Get array of all contents of the group(uses ProductInGroup to determine the order)
     *
     * @param GroupContent $groupContent
     * @param $hasAccess
     * @return array
     */
    protected function getContentsFromGroup(GroupContent $groupContent, $hasAccess): array
    {
        $groupContents = [];

        /** @var ContentInGroup $contentInGroup */
        foreach ($groupContent->getItems() as $contentInGroup) {
            $content = $this->getContentData($contentInGroup->getContent(), $contentInGroup->getOrderNumber(), $hasAccess);

            if ($content !== null) {
                $groupContents[] = $content;
            }
        }

        return $groupContents;
    }

    /**
     * @param StandardProduct $product
     * @return array
     * @throws \Exception
     */
    protected function getContentFromProduct(StandardProduct $product): array
    {
        // return array of contents in the appropriate format

        if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT) {
            $groupContents = $this->getAllContentFromAllGroups($product);

            /** @var ContentProduct $contentProduct */
            foreach ($product->getContentProducts() as $contentProduct) {
                $content = $this->getContentData($contentProduct->getContent(), $contentProduct->getOrderNumber(), $this->user->hasAccessToProduct($product));

                if ($content !== null) {
                    $groupContents[] = $content;
                }
            }

            return $groupContents;
        }

        if ($product->getHandle() === ProductGroup::HANDLE_FLOMERSION) {
            return $this->getAllContentFromAllGroups($product);
        }

        // Else
        $data = [];
        foreach ($product->getContentProducts() as $contentProduct) {
            $content = $this->getContentData(
                $contentProduct->getContent(),
                $contentProduct->getOrderNumber(),
                $this->user->hasAccessToProduct($product)
            );

            if ($content !== null) {
                $data[] = $content;
            }
        }

        return $data;
    }

    /**
     * Get appropriate data for each content
     *
     * @param Content|ContentInterface $content
     * @param $orderNumber
     * @param $hasAccess
     *
     * @return array|null
     */
    protected function getContentData(ContentInterface $content, $orderNumber, $hasAccess): ?array
    {
        $data = null;
        if ($content->getType() === VideoContent::TYPE) {
            /** @var VideoContent $content */

            $data = [
                'type' => 'videoproduct',
                'access' => $hasAccess,
                'lastPlayed' => $this->entityManager->getRepository(UserPlayedVideos::class)
                    ->getDateOfLastUserPlayOfVideo($this->user, $content),
                'length' => $content->getDuration(),
                'previewImage' => $content->getPreviewImage(),
                'needGear' => $content->isNeedGear(),
                'videoMobile' => $content->getVideoMobile(),
                'videoLq' => $content->getVideoLq(),
                'videoHq' => $content->getVideoHq(),
                'videoHd' => $content->getVideoHd(),
                'HTTPstream' => $content->getHttpStream(),
                'vimeoThumbnailId' => $content->getVimeoThumbnailId(),
                'id' => $content->getId(),
                'name' => $content->getName(),
                'image' => $content->getPreviewImage(),
                'description' => $content->getDescription(),
                'orderNumber' => $orderNumber,
                'delayed' => false,
                'sendInApi' => true,
            ];

        } elseif ($content->getType() === PdfContent::TYPE) {
            /** @var PdfContent $content */

            $data = [
                'type' => 'downloadproduct',
                'access' => $hasAccess,
                'file' => $content->getLink(),
                'fileProtected' => $content->getFileProtected(),
                'downloadType' => 0, //constant in referential api
                'fileSize' => $content->getFileSize(),
                'downloadName' => '',
                'id' => $content->getId(),
                'name' => $content->getName(),
                'image' => '',
                'description' => $content->getDescription(),
                'orderNumber' => $orderNumber,
                'delayed' => false,
                'sendInApi' => true,
            ];
        }

        return $data;
    }

    /**
     * @param StandardProduct $product
     * @return string
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     */
    protected function getProductPrice(StandardProduct $product): string
    {
        $billingPlan = $this->getBillingPlanForProduct($product);

        if (!$billingPlan) {
            return '';
        }

        return $this->priceGenerator->generateFullPriceStr($billingPlan);
    }

    /**
     * @param StandardProduct $product
     * @return string
     * @throws \Exception
     */
    protected function getProductBuyLink(StandardProduct $product): string
    {
        $billingPlan = $this->getBillingPlanForProduct($product);

        if (!$billingPlan) {
            return '';
        }

        return $this->buyUrlGenerator->generateBuyUrl($product, $billingPlan->getId());
    }

    /**
     * @param StandardProduct $product
     * @return BillingPlan|null
     */
    protected function getBillingPlanForProduct(StandardProduct $product): ?BillingPlan
    {
        if ($product->getHandle() === ProductGroup::HANDLE_NUTRITION_AND_MEALS) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 402]); //cb id
        }
        if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT_PHYSICAL) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 206]); //cb id
        } elseif ($product->getHandle() === ProductGroup::HANDLE_PLATINUM_MIX) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 401]); //cb id
        } elseif ($product->getHandle() === ProductGroup::HANDLE_7_DAY_RIP_MIX) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 403]); //cb id
        } elseif ($product->getHandle() === ProductGroup::HANDLE_FLOMERSION) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 404]); //cb id
        } else {
            return null;
        }
    }

    /**
     * @param StandardProduct $product
     * @return array
     * @throws \Exception
     */
    protected function getAllContentFromAllGroups(StandardProduct $product): array
    {
        $ret = [];
        //get all groups
        foreach ($product->getAllContentByType(GroupContent::TYPE) as $group) {
            $ret[] = $this->getGroupContentData($this->user, $product, $group);
        }

        return $ret;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    protected function getFlofitShippingProductData(): ?array
    {
        $product = $this->entityManager->getRepository(StandardProduct::class)->findOneBy(['handle' => ProductGroup::HANDLE_FLOFIT_PHYSICAL]);

        if ($product === null) {
            return null;
        }

        return [
            'type' => 'shippingproduct',
            'access' => true,
            'buylink' => $this->getProductBuyLink($product),
            'price' => $this->getProductPrice($product),
            'shippingPrice' => 'already included',
            'id' => $product->getId(),
            'name' => 'Shipping-product',
            'image' => null,
            'description' => $product->getDescription(),
            'orderNumber' => 0,
            'delayed' => false,
            'sendInApi' => true,
        ];
    }
}
