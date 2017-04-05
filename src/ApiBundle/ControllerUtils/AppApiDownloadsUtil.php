<?php
namespace ApiBundle\Controller;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Content\GroupContent;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductGroup;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Trinity\Component\Utils\Services\PriceStringGenerator;
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
     * @param User $user
     * @param StandardProduct $product
     *
     * @return array
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     */
    public function getProductData(User $user, StandardProduct $product)
    {
        $this->user = $user;

        return [
            'type' => 'bundleproduct',
            'access' => $user->hasAccessToProduct($product),
            'moduleNumber' => 1,
            'openIn' => 0,
            'buylink' => $this->getProductBuyLink($product),
            'price' => $this->getProductPrice($product),
            'subProducts' => $this->getSubProducts($product),
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
    public function getGroupContentData(User $user, StandardProduct $product, GroupContent $group)
    {
        $this->user = $user;

        return [
            'type' => 'bundleproduct',
            'access' => $user->hasAccessToProduct($product),
            'moduleNumber' => 1,
            'openIn' => 0,
            'buylink' => $this->getProductBuyLink($product),
            'price' => $this->getProductPrice($product),
            'subProducts' => $this->getSubContents($group),
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
    protected function getSubContents(GroupContent $groupContent)
    {
        //todo:!
        //return
        return [];
    }

    protected function getSubProducts(StandardProduct $product)
    {
        //todo: return array of contents in the appropriate format
        if ($product->getHandle() === ProductGroup::HANDLE_FLOFIT) {
            $groupContents = $this->getAllContentFromAllGroups($product);

            foreach ($product->getContentProducts() as $contentProduct) {
                $content = [];
                //todo: ....

                array_merge($groupContents, $content);
            }

            return []; //todo
        } elseif ($product->getHandle() === ProductGroup::HANDLE_FLOMERSION) {
            return $this->getAllContentFromAllGroups($product);
        } else {
            return [];
        }
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
        return $this->priceGenerator->generateFullPriceStr($this->getBillingPlanForProduct($product));
    }

    /**
     * @param StandardProduct $product
     * @return string
     * @throws \Exception
     */
    protected function getProductBuyLink(StandardProduct $product)
    {
        return $this->buyUrlGenerator->generateBuyUrl($product, $this->getBillingPlanForProduct($product)->getId());
    }

    /**
     * @param StandardProduct $product
     * @return BillingPlan|null
     */
    protected function getBillingPlanForProduct(StandardProduct $product)
    {
        if ($product->getHandle() === ProductGroup::HANDLE_NUTRITION_AND_MEALS) {
            return $this->entityManager->getRepository(BillingPlan::class)->findOneBy(['itemId' => 402]); //cb id
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
}
