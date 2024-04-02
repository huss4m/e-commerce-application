<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')] 
    public function list(Categories $category, ProductsRepository $productsRepository, Request $request): Response
    {
        // On va chercher le numéro de page dans l'URL
        $page = $request->query->getInt('page', '1');


        $products = $productsRepository->findProductsPaginated($page, $category->getSlug(), 2);

        
        return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}
