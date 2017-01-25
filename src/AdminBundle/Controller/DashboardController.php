<?php
namespace AdminBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Venice\AdminBundle\Controller\DashboardController as VeniceDashboardController;

/**
 * Class DashboardController.
 */
class DashboardController extends VeniceDashboardController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }


    /**
     * @Route("/admin/dashboard-extended")
     */
    public function dashboardExtendedAction(Request $request)
    {
        $this->getBryeeadcrumbs();

        return $this->render('VeniceAdminBundle:Dashboard:dashboard_extended.html.twig');
    }
}
