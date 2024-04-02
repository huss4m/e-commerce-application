<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'app_admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();
        return $this->render('admin/products/index.html.twig', compact('products'));
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On créé un nouveau produit
        $product = new Products();

        // On créé le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);


        // On traite la requête du formulaire
        $productForm->handleRequest($request);
        
        if ($productForm->isSubmitted() && $productForm->isValid()) { 
            
            // On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // On arroundit le prix en multipliant par 100
            // $price = $product->getPrice()*100;
            // $product->setPrice($price);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');

            // Redirection
            return $this->redirectToRoute('app_admin_products_index');
        }


        //return $this->render('admin/products/add.html.twig', [
        //   'productForm' => $productForm->createView()
        //]);

        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }


    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // On divise le prix par 100
        // $price = $product->getPrice()/100;
        // $product->setPrice($price);
        
        // On créé le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);


        // On traite la requête du formulaire
        $productForm->handleRequest($request);
        
        if ($productForm->isSubmitted() && $productForm->isValid()) { 
            
            // On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // On arroundit le prix en multipliant par 100
            // $price = $product->getPrice()*100;
            // $product->setPrice($price);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');

            // Redirection
            return $this->redirectToRoute('app_admin_products_index');
        }
        return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }
}
