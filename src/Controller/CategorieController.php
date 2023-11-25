<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(Request $request)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }

        $categories = $this->em->getRepository(Categorie::class)->findAll();
        $inverscateg = array_reverse($categories);
        $firstthree = array_slice($inverscateg, 0, 3);

        $new_categorie = new Categorie();

        $userRole = $this->getUser();
        $userRole = $userRole->getRoles();
        $form = $this->createForm(CategorieType::class, $new_categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_categorie);
            $this->em->flush();
            return $this->redirectToRoute('app_categories');
        }       

        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
            'allcategories' => $firstthree,
            'userRole' => $userRole,
            'form' => $form->createView()
        ]);
    }

    #[Route('/categorie/add', name: 'add_categorie')]
    public function add(Request $request)
    {
        if($this->getUser()->getRoles() !== ['ROLE_ADMIN']){
            return $this->redirectToRoute('app_login');
        }

        $new_categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $new_categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_categorie);
            $this->em->flush();
            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/add.html.twig', [
            'controller_name' => 'CategorieController',
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/categorie/{id}', name: 'details_categorie')]
    public function details(Categorie $categorie = null, Request $request)
    {
        if($categorie === null){
            return $this->redirectToRoute('app_categories');
        }
        $userRole = $this->getUser();
        $userRole = $userRole->getRoles();
        
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($categorie);
            $this->em->flush();
            return $this->redirectToRoute('details_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('categorie/details.html.twig', [
            'controller_name' => 'CategorieController',
            'allcategories' => $categorie,
            'userRole' => $userRole,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categorie/delete/{id}', name: 'delete_categorie')]
    public function delete(Categorie $categorie = null)
    {
        if($this->getUser()->getRoles() !== ['ROLE_ADMIN']){
            return $this->redirectToRoute('app_categories');
        }

        if($categorie === null){
            return $this->redirectToRoute('app_categories');
        }
        $this->em->remove($categorie);
        $this->em->flush();
        return $this->redirectToRoute('app_categories');
    }
}
