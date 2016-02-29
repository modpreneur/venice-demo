<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.02.16
 * Time: 15:28
 */

namespace AdminBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Venice\AdminBundle\Controller\DashboardController as VeniceDashboardController;


class DashboardController extends VeniceDashboardController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function indexAction(Request $request)
    {
        dump("Overriden controller action");

        return parent::indexAction($request);
    }

    /**
     * @Route("/admin/dashboard-extended")
     */
    public function dashboardExtendedAction(Request $request)
    {
        $this->getBreadcrumbs();

        return $this->render("VeniceAdminBundle:Dashboard:dashboard_extended.html.twig");
    }

}