<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SupplementDatabaseController
 *
 * @Route("/products/platinumclub/supplement-database")
 * @package GeneralBackend\CoreBundle\Controller\Front
 */
class SupplementDatabaseController extends Controller
{
    /**
     * @return bool
     */
    private function checkAccess()
    {
        /** @var User $user */
        $user = $this->getUser();

        if (is_null($user)) {
            return false;
        }

        return true;
    }


    /**
     * @Route("", name="downloads_front_supplementDatabase_about")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        if (!$this->checkAccess()) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:index.html.twig',
            []
        );
    }


    /**
     * @Route("/food-drugs-and-supplements", name="downloads_front_supplementDatabase_foodDrugsAndSupplements")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function foodDrugsAndSupplementsAction()
    {
        if (!$this->checkAccess()) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        $pageTitle = 'Food Drugs and Supplements';
        $iframeLink = 'http://3rdparty.naturalstandard.com/index-herbs.asp';

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:iframePage.html.twig',
            ['iframeLink' => $iframeLink, 'pageTitle' => $pageTitle, 'iframeHeight' => 28250]
        );
    }


    /**
     * @Route("/medical-conditions", name="downloads_front_supplementDatabase_medicalConditions")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function medicalConditionsAction()
    {
        if (!$this->checkAccess()) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        $pageTitle  = 'Medical Conditions';
        $iframeLink = 'http://3rdparty.naturalstandard.com/index-conditions.asp';

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:iframePage.html.twig',
            ['iframeLink' => $iframeLink, 'pageTitle' => $pageTitle, 'iframeHeight' => 7100]
        );
    }


    /**
     * @Route("/drug-interactions", name="downloads_front_supplementDatabase_drugInteractions")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function drugInteractionsAction()
    {
        if (!$this->checkAccess()) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        $pageTitle  = 'Drug Interactions';
        $iframeLink = 'http://3rdparty.naturalstandard.com/index-interactions.asp';

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:iframePage.html.twig',
            ['iframeLink' => $iframeLink, 'pageTitle' => $pageTitle, 'iframeHeight' => 200]
        );
    }


    /**
     * @Route("/drug-nutrient-depletions", name="downloads_front_supplementDatabase_drugNutrientDepletions")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function drugNutrientDepletionsAction()
    {
        if (!$this->checkAccess()) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        $pageTitle  = 'Drug Nutrient Depletions';
        $iframeLink = 'http://3rdparty.naturalstandard.com/content/interactionHTML/depletions-drugs.asp';

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:iframePage.html.twig',
            ['iframeLink' => $iframeLink, 'pageTitle' => $pageTitle, 'iframeHeight' => 800]
        );
    }


    /**
     * @Route("/herb-supplement-nutrient", name="downloads_front_supplementDatabase_herbSupplementNutrient")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function herbSupplementNutrientAction()
    {
        $pageTitle  = 'Herb supplement nutrients';
        $iframeLink = 'http://3rdparty.naturalstandard.com/index-herbs.asp';

        return $this->render(
            'VeniceFrontBundle:SupplementDatabase:iframePage.html.twig',
            ['iframeLink' => $iframeLink, 'pageTitle' => $pageTitle, 'iframeHeight' => 1500]
        );
    }
}