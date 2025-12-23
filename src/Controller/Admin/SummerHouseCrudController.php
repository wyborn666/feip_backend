<?php

namespace App\Controller\Admin;

use App\Entity\SummerHouse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class SummerHouseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SummerHouse::class;
    }

    #[\Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('address')
            ->add('price')
            ->add('bedrooms')
            ->add('distanceFromSea')
            ->add('hasShower');
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
