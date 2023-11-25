<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ArticleController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/article', name: 'app_article')]
    public function index()
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }

        $allarticles = $this->em->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'allarticles' => $allarticles   
        ]);
    }

    #[Route('/article/add', name: 'add_article')]
    public function add(Request $request)
    {
        if($this->getUser() === null or $this->getUser()->getRoles() !== ['ROLE_ADMIN']){
            return $this->redirectToRoute('app_login');
        }

        $new_article = new Article();
        $form = $this->createForm(ArticleType::class, $new_article);
        $form->handleRequest($request);

        // $userRole = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_article);
            $this->em->flush();
            return $this->redirectToRoute('app_article');
        }       

        return $this->render('article/add.html.twig', [
            'controller_name' => 'ArticleController',
            // 'userRole' => $userRole,
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/{id}', name: 'details_article')]
    public function details(Request $request, Article $article = null)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }

        if($article === null){
            return $this->redirectToRoute('app_article');
        }

        $coms = $this->em->getRepository(Commentaire::class)->findAll();

        $userRole = $this->getUser();
        $userRole = $userRole->getRoles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();
            return $this->redirectToRoute('details_article', ['id' => $article->getId()]);
        }

        return $this->render('article/details.html.twig', [
            'controller_name' => 'ArticleController',
            'allarticles' => $article,
            'userRole' => $userRole,
            'coms' => $coms,
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(Article $article)
    {
        if($this->getUser()->getRoles() !== ['ROLE_ADMIN']){
            return $this->redirectToRoute('app_article');
        }

        if($article === null){
            return $this->redirectToRoute('app_article');
        }
        $this->em->remove($article);
        $this->em->flush();
        return $this->redirectToRoute('app_article');
    }

    #[Route('/article/check/{id}', name: 'check_article')]
    public function publier(Commentaire $commentaire, Article $article)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }

        $article = $commentaire->getArticle();
        $etat = $commentaire->isEtat();
        $commentaire->setEtat(!$etat);

        $this->em->persist($commentaire);
        $this->em->flush();

        // return $this->redirectToRoute('app_article');
        return $this->redirectToRoute('details_article', ['id' => $article->getId()]);
    }
}
