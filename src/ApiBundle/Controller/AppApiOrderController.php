<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 13.07.15
 * Time: 11:52
 */

namespace ApiBundle\Controller;

use ApiBundle\Api;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Venice\AppBundle\Entity\Order;

/**
 * @Route("/api/invoices")
 *
 * Class AppApiOrderController
 */
class AppApiOrderController extends FOSRestController
{
    use Api;

    /**
     * Get user invoices
     *
     * You will be probably interested in invoiceItems
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "status": 1,
     *               "invoiceId": 1,
     *               "firstTotal": 10,
     *               "secondTotal": 20,
     *               "rebillTimes": 2,
     *               "invoiceItems": [
     *               {
     *                   "title": "invoice item #1"
     *               },
     *               {
     *                   "title": "invoice item #2"
     *               }
     *               ]
     *           },
     *           {
     *               "status": 1,
     *               "invoiceId": 3,
     *               "firstTotal": 30,
     *               "secondTotal": 40,
     *               "rebillTimes": 1,
     *               "invoiceItems": [
     *               {
     *                   "title": "invoice item #3"
     *               },
     *               {
     *                   "title": "invoice item #4"
     *               }
     *               ]
     *           }
     *           ]
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get all invoices for the user",
     * )
     *
     * @Get("/", name="api_get_invoices")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @throws \LogicException
     */
    public function getOrdersAction(Request $request)
    {
        $user = $this->getUser();

        $necktieConnector = $this->get('venice.app.necktie_gateway');
        $orders = $necktieConnector->getOrders($user);

        $arrayizer = $this->get('flofit.services.arrayizer');
        $arrayizer->setCallbacks($this->getBeforeCallback());
        $arrayizer->setWithout([
            '<OrderItem>.order',
            '<OrderItem>.type',
            '<OrderItem>.productName',
            '<OrderItem>.initial_price',
            '<OrderItem>.rebillPrice',
            'user',
            'firstTotal',
            'secondTotal',
            'rebillTimes',
            'firstPaymentDate',
            'stringPrice',
            'receipt',
            'status',
            'necktieId',
            'items'
        ]);

        $ordersArray = [];

        foreach ($orders as $orderObject) {
            $ordersArray[] = $arrayizer->arrayize($orderObject);
        }

        return new JsonResponse($this->okResponse($ordersArray));
    }

    /**
     * @return callable
     */
    private function getBeforeCallback()
    {
        /**
         * @param $currentObject
         * @param $propertiesArray
         */
        return function ($currentObject, $properties, & $propertiesArray) {
            if ($currentObject instanceof Order) {
                $propertiesArray['status'] = (string)$this->getInvoiceStatusNumberByName(
                    $currentObject->getStatus()
                );
                $propertiesArray['invoiceId'] = $currentObject->getNecktieId();
                $propertiesArray['startedDate'] = $currentObject->getFirstPaymentDate()
                    ->format('Y-m-d H:i:s');

                $propertiesArray['invoiceItems'] = $currentObject->getItems();
            }
        };
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function getInvoiceStatusNumberByName(string $name)
    {
        switch ($name) {
            case 'PENDING':
                return 0;
            case 'NORMAL':
                return 1;
            case 'RECURRING':
                return 2;
            case 'CANCELED':
                return 3;
            case 'REFUNDED':
                return 4;
            case 'COMPLETED':
                return 5;
            default:
                return 0;
        }
    }
}
