<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CommentaireController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(Request $request)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        
        $articles = $this->em->getRepository(Article::class)->findAll();
        if ($articles == null) {
            return $this->redirectToRoute('app_categories');
        }
        $userConnected = $this->getUser();

        $new_commentaire = new Commentaire();
        $new_commentaire->setAuteur($userConnected);
        $form = $this->createForm(CommentaireType::class, $new_commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_commentaire);
            $this->em->flush();

            return $this->redirectToRoute('app_commentaire');
        }

        return $this->render('commentaire/add.html.twig', [
            'controller_name' => 'CommentaireController',
            'allarticles' => $articles,
            'userConnected' => $userConnected,
            'form' => $form->createView(),
        ]);
    }

    /* #[Route('/commentaire/check/{id}', name: 'check_commentaire')]
    public function publier(Commentaire $commentaire)
    {
    $commentaire->setEtat(false);

    $this->em->persist($commentaire);
    $this->em->flush();

    return $this->redirectToRoute('app_commentaire');
    } */
}
