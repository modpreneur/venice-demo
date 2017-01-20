<?php

namespace AppBundle\Services;

use AppBundle\Entity\Content\GroupContent;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Content\ContentInGroup;
use Venice\AppBundle\Entity\Interfaces\ContentProductInterface;
use Venice\AppBundle\Entity\Product\Product;

/**
 * Class ProductsPage
 * @package AppBundle\Services
 */
class ProductsPage extends \Twig_Extension
{
    /** @var null  */
    private $productByTypes;

    /** @var   */
    private $allProducts;

    /** @var   */
    private $bundleProducts;

    /** @var ContainerInterface  */
    private $container;

    /** @var  User */
    private $user;


    /**
     * ProductsPage constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container      = $container;
        $this->productByTypes = null;
    }


    /**
     * @param $allProducts
     * @param User|null $user
     */
    public function initialSetup($allProducts, User $user = null)
    {
        $this->productByTypes = [];
        $this->allProducts    = [];
        $this->user           = $user;

        $this->sortContent($allProducts);
    }


    /**
     * @param \iterable $products
     */
    private function sortContent($products)
    {
        foreach ($products as $product) {
            /** @var Product $product */
            if ($product instanceof StandardProduct) {
                /** @var StandardProduct $product */

                $content = $product->getAllContent(Product::SORT);

                if ($content) {
                    $this->sortContent($content);
                }

                $this->bundleProducts[] = $product;
            } else {
                $type = $product->getType();

                if (!array_key_exists($type, $this->productByTypes)) {
                    $this->productByTypes[$type] = [];
                }

                if (!in_array($product, $this->productByTypes[$type])) {
                    $this->productByTypes[$type][] = $product;
                }

                $this->allProducts[] = $product;
            }
        }
    }


    /**
     * @return mixed
     */
    public function getDefaultLength()
    {
        return $this->container->getParameter('downloads_number_of_product_displayed');
    }


    /**
     * @param $type
     *
     * @return array
     * @throws \Exception
     */
    public function getContentByType($type)
    {
        if (is_null($this->productByTypes)) {
            throw new \Exception('Must be initialized!');
        }

        $productByTypes = [];

        foreach ($this->productByTypes as $index => $value) {
            if ($index === 'video') {
                $index = 'videoproduct';
            }

            if ($index === 'pdf') {
                $index = 'downloadproduct';
            }

            $productByTypes[$index] = $value;
        }

        $this->productByTypes = $productByTypes;

        if (!array_key_exists($type, $this->productByTypes)) {
            return [];
        }

        //usort($this->productByTypes[$type], [$this, 'productComp']);
        return $this->productByTypes[$type];
    }


    /**
     * @param Product $masterProduct
     *
     * @return array
     */
    public function getProductByMasterProductName(Product $masterProduct)
    {
        $products = [];

        foreach ($this->allProducts as $product) {
            /** @var Product $product */
            if ($product->getMasterProduct()->getName() == $masterProduct) {
                $products[] = $product;
            }
        }

        // usort($products, array($this, "productComp"));

        return $products;
    }


    /**
     * @param $bundleProductHandle
     *
     * @return array
     */
    public function getProductsByBundleProductHandle($bundleProductHandle)
    {
        $contents = [];

        foreach ($this->bundleProducts as $product) {
            foreach ($product->getAllContent(Product::SORT) as $group) {
                if ($group instanceof GroupContent && $group->getHandle() === $bundleProductHandle) {
                    // todo sort
                    foreach ($group->getItems() as $item) {
                        if ($item instanceof ContentInGroup) {
                            $contents[] = $item->getContent();
                        }

                    }
                }
            }
        }

        dump($contents);

        return $contents;
    }


    /**
     * @return mixed
     */
    public function getAllLoadedProducts()
    {
        return $this->allProducts;
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'getProductsByType'  => new \Twig_SimpleFunction($this, 'getProductsByType'),
            'countByProductType' => new \Twig_SimpleFunction($this, 'countByProductType'),
            'getDefaultLength'   => new \Twig_SimpleFunction($this, 'getDefaultLength'),
        ];
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'products_page';
    }
}
