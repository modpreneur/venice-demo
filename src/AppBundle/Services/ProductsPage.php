<?php

namespace AppBundle\Services;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Venice\AppBundle\Entity\Content\Content;
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

        $this->sortProducts($allProducts);
    }


    /**
     * @param $products
     */
    private function sortProducts(iterable $products)
    {
        foreach ($products as $product) {
            /** @var Product $product */
            if ($product instanceof StandardProduct) {
                /** @var StandardProduct $product */

                if ($product->getAllContent()) {
                    $this->sortProducts($product->getAllContent());
                }

                $this->bundleProducts[] = $product;
            } else {
                $type = $product->getType();

                if (!array_key_exists($type, $this->productByTypes))
                    $this->productByTypes[$type] = [];

                if (!in_array($product, $this->productByTypes[$type]))
                    $this->productByTypes[$type][] = $product;

                $this->allProducts[] = $product;
            }
        }
    }


    /**
     * @return mixed
     */
    public function getDefaultLength()
    {
        return $this->container->getParameter("downloads_number_of_product_displayed");
    }


    /**
     * @param $type
     *
     * @return array
     * @throws \Exception
     */
    public function getProductsByType($type)
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

        dump($productByTypes);
        dump($type);

        if (!array_key_exists($type, $this->productByTypes)) {
            return [];
        }



        usort($this->productByTypes[$type], [$this, 'productComp']);
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

        usort($products, array($this, "productComp"));

        return $products;
    }


    /**
     * @param $bundleProductHandle
     * @param null $inModule
     *
     * @return array
     */
    public function getProductsByBundleProductHandle($bundleProductHandle, $inModule = null)
    {
        foreach ($this->bundleProducts as $product) {
            /** @var StandardProduct $product */
            if ($product->getHandle() == $bundleProductHandle) {
                $products = $product->getSubProducts()->toArray();

                usort($products, array($this, "productComp"));

                if (!is_null($inModule)) {
                    $moduleProducts = [];
                    foreach ($products as $prod) {
                        /** @var Product $prod */
                        if ($prod->isInModule($inModule))
                            $moduleProducts[] = $prod;
                    }
                    return $moduleProducts;
                }

                return $products;
            }
        }
        return [];
    }


    /**
     * @return mixed
     */
    public function getAllLoadedProducts()
    {
        return $this->allProducts;
    }


    /**
     * @param Content $product1
     * @param Content $product2
     *
     * @return bool
     */
    public function productComp(Content $product1, Content $product2)
    {
        return 1;
        return $product1->getOrderNumber() > $product2->getOrderNumber();
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            "getProductsByType" => new \Twig_SimpleFunction($this,"getProductsByType"),
            "countByProductType" => new \Twig_SimpleFunction($this,"countByProductType"),
            "getDefaultLength" => new \Twig_SimpleFunction($this,"getDefaultLength"),
        );
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
