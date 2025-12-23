<?php

namespace App\Controller\Admin;

use App\Controller\BookingController;
use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SummerHouse;
use App\Entity\User;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Feip Backend');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Bookings', 'fa fa-envilope', Booking::class)->setController(BookingCrudController::class);
        yield MenuItem::linkToCrud('Houses', 'fa fa-envilope', SummerHouse::class)->setController(SummerHouseCrudController::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-envilope', User::class)->setController(UserCrudController::class);


    }
}
