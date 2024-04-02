<?php

namespace App\Security\Voter;


use App\Entity\Products;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';


    private $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }



    protected function supports(string $attribute, $product): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if(!$product instanceof Products) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool
    {
        // Récupérer l'utilisateur à partir du token
        $user = $token->getUser();

        if(!$user instanceof UserInterface) {
            return false;
        }

        // On vérifie si l'utilisateur est admin
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }


        // Si l'utilisateur est connecté mais n'est pas admin
        // On vérifie les permissions
        switch($attribute) {
            case self::EDIT:
                // On vérifie s'il peut éditer
                return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
                break;
            
            case self::DELETE:
                // On vérifie s'il peut supprimer
                return $this->canDelete();
                break;
        }

    }

    public function canEdit() {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    public function canDelete() {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}