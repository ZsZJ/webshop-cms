<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods="GET")
     */
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('product/index.html.twig', ['products' => $productRepository->findAll(), 'categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/search", name="product_search", methods="GET|POST")
     */
    public function search(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $search_products = $productRepository->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('p.id LIKE :id')
            ->orWhere('p.name LIKE :name')
            ->orWhere('c.name LIKE :category')
            ->setParameter('id', $request->get('value'))
            ->setParameter('name', '%'.$request->get('value').'%')
            ->setParameter('category', '%'.$request->get('value').'%')
        ->getQuery()
        ->getResult();

        return $this->render('product/index.html.twig', ['products' => $search_products, 'categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/filter", name="product_filter", methods="GET|POST")
     */
    public function filter(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $filter_products = $productRepository->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('p.category = :category')
            ->setParameter('category', $request->get('filter'))
            ->getQuery()
            ->getResult();

        return $this->render('product/index.html.twig', ['products' => $filter_products, 'categories' => $categoryRepository->findAll()]);
    }


    /**
     * @Route("/new", name="product_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods="GET")
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="GET|POST")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE")
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
