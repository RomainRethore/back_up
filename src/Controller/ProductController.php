<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }

    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès');

            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/new.html.twig', [
            'controller_name' => 'ProductController',
            'form' => $form,
        ]);
    }

    #[Route('/product/update/{id}', name: 'app_product_update')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, Product $product): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ProductFormType::class, $product, [
            'attr' => ['id' => 'mix-form'],
            // ])->add('submit', SubmitType::class, [
            //     'label' => 'Valider',
        ])->add('cancel', SubmitType::class, [
            'label' => 'Annuler',
            'attr' => [
                'formnovalidate' => true,
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/product/delete/{id}/{action}', name: 'app_product_delete')]
    public function delete(int $id, string $action, Product $product, ProductRepository $ProductRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product = $ProductRepository->find($id);

        if ($action === 'confirm') {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('message', 'Le produit ' . $product->getName() . ' a bien été supprimé');

            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/delete.html.twig', [
            'product' => $product
        ]);
    }
}
