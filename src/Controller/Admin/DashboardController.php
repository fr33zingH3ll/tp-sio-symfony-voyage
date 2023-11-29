<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Entity\Travel;
use App\Entity\Environment;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tp Souadia');
    }

    public function configureMenuItems(): iterable
    {        
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class)->setController(UserCrudController::class);;
        yield MenuItem::linkToCrud('Travel', 'fa fa-travel', Travel::class)->setController(TravelCrudController::class);;
        yield MenuItem::linkToCrud('Environement', 'fa fa-environement', Environment::class)->setController(EnvironmentCrudController::class);;
    }
}
