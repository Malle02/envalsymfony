<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email'),
            TextField::new('name', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating(),
            ChoiceField::new('roles')
                ->setChoices([
                    'Candidat' => 'ROLE_USER',
                    'Conseiller' => 'ROLE_CONSEILLER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->allowMultipleChoices()
                ->renderExpanded()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof User && $entityInstance->getPassword()) {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->getPassword()
        );
        $entityInstance->setPassword($hashedPassword);
    }
    
    $entityManager->persist($entityInstance);
    $entityManager->flush();
}
}