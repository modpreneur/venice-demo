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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Trinity\Component\Utils\Services\PriceStringGenerator;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Services\BuyUrlGenerator;

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
     * @var User
     */
    protected $user;

    /**
     * AppApiDownloadsUtil constructor.
     * @param BuyUrlGenerator $buyUrlGenerator
     * @param PriceStringGenerator $priceGenerator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BuyUrlGenerator $buyUrlGenerator, PriceStringGenerator $priceGenerator, EntityManagerInterface $entityManager)
    {
        $this->buyUrlGenerator = $buyUrlGenerator;
        $this->priceGenerator = $priceGenerator;
        $this->entityManager = $entityManager;
    }

    public function getDataForProduct(User $user, StandardProduct $product)
    {
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
        } else {
            return [$this->getProductData($user, $product)];
        }
    }

    /**
     * @param User $user
     * @param StandardProduct $product
     *
     * @return array
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     */
    protected function getProductData(User $user, StandardProduct $product)
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
            'invoiceOrder' => 0 //todo? wtf?
        ];
    }

    /**
     * This must have the same output as the getProductData() method
     *
     * @param User $user
     * @param StandardProduct $product
     * @param GroupContent $group
     * @return array
     */
    protected function getGroupContentData(User $user, StandardProduct $product, GroupContent $group)
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
            'invoiceOrder' => 0 //todo? wtf?
        ];
    }

    /**
     * Get array of all contents of the group(uses ProductInGroup to determine the order)
     *
     * @param GroupContent $groupContent
     * @return array
     */
    protected function getContentsFromGroup(GroupContent $groupContent, $hasAccess)
    {
        /** @var ContentInGroup $contentInGroup */
        foreach ($groupContent->getItems() as $contentInGroup) {
            $content = $this->getContentData($contentInGroup->getContent(), $contentInGroup->getOrderNumber(), $hasAccess);

            if ($content !== null) {
                $groupContents[] = $content;
            }
        }

        return $groupContents;
    }

    protected function getContentFromProduct(StandardProduct $product)
    {
//      return array of contents in the appropriate format

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
        } elseif ($product->getHandle() === ProductGroup::HANDLE_FLOMERSION) {
            return $this->getAllContentFromAllGroups($product);
        } else {
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
    }

    /**
     * Get appropriate data for each content
     *
     * @param Content $content
     */
    protected function getContentData(Content $content, $orderNumber, $hasAccess)
    {
        if ($content->getType() === VideoContent::TYPE) {
            /** @var VideoContent $content*/

            $data = [
                'type' => 'videoproduct',
                'access' => $hasAccess,
                'lastPlayed' => '2016-11-11 02:12:57', //todo: ???
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
            /** @var PdfContent $content*/

            $data = [
                'type' => 'downloadproduct',
                'access' =>  $hasAccess,
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
        } else { //unknown content type
//            $data = ['UNKNOWNTYPE'.$content->getType().$content->getName()];
            return null;
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
    protected function getProductPrice(StandardProduct $product)
    {
        $bp = $this->getBillingPlanForProduct($product);

        if (!$bp) {
            return '';
        }

        return $this->priceGenerator->generateFullPriceStr($this->getBillingPlanForProduct($product));
    }

    /**
     * @param StandardProduct $product
     * @return string
     * @throws \Exception
     */
    protected function getProductBuyLink(StandardProduct $product)
    {
        $bp = $this->getBillingPlanForProduct($product);

        if (!$bp) {
            return '';
        }

        return $this->buyUrlGenerator->generateBuyUrl($product, $bp->getId());
    }

    /**
     * @param StandardProduct $product
     * @return BillingPlan|null
     */
    protected function getBillingPlanForProduct(StandardProduct $product)
    {
        if ($product->getHandle() === ProductGroup::HANDLE_NUTRITION_AND_MEALS) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 402]); //cb id
        } if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT_PHYSICAL) {
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
     */
    protected function getAllContentFromAllGroups(StandardProduct $product)
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
    protected function getFlofitShippingProductData()
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
            'sendInApi' => true
        ];
    }
}
