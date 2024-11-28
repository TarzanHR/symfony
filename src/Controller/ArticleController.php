<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Uploader;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/creer', name: 'app_article_article')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        Uploader $uploader
    ): Response 
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $imageFile = $form->get('brochure')->getData();
    
            if ($imageFile) 
            {
                $newFilename = $uploader->uploadFile($imageFile);
                $article->setImageFilename($newFilename);
            }
    
            $entityManager->persist($article);
            $entityManager->flush();
    
            $this->addFlash('success', 'Article créé avec succès !');
    
            return $this->redirectToRoute('liste_article');
        }
    
        return $this->render('article/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/article/liste', name: 'liste_article')]
    public function liste(EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/liste.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/update/{id}', name: 'update_article')]
    public function update(
        EntityManagerInterface $entityManager,
        Request $request,
        Uploader $uploader,
        int $id
    ): Response 
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
    
        if (!$article) 
        {
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }
    
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $imageFile = $form->get('brochure')->getData();
    
            if ($imageFile) 
            {
                if ($article->getImageFilename()) 
                {
                    $uploader->deleteFile($article->getImageFilename());
                }
    
                $newFilename = $uploader->uploadFile($imageFile);
                $article->setImageFilename($newFilename);
            }
    
            $entityManager->flush();
    
            $this->addFlash('success', 'Article mis à jour avec succès !');
    
            return $this->redirectToRoute('liste_article');
        }
    
        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/article/delete/{id}', name : 'delete_article')]
    public function delete(
        EntityManagerInterface $entityManager,
        Uploader $uploader,
        int $id
    ): Response {
        $article = $entityManager->getRepository(Article::class)->find($id);
    
        if ($article->getImageFilename()) 
        {
            $uploader->deleteFile($article->getImageFilename());
        }
    
        $entityManager->remove($article);
        $entityManager->flush();
    
        $this->addFlash('success', 'Article supprimé avec succès !');
    
        return $this->redirectToRoute('liste_article');
    }
    

    #[Route('/article/deleteall', name: 'deleteall_article')]
    public function deleteall(EntityManagerInterface $entityManager, Uploader $uploader): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
    
        foreach ($articles as $article) 
        {
            if ($article->getImageFilename()) 
            {
                $uploader->deleteFile($article->getImageFilename());
            }
    
            $entityManager->remove($article);
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Tous les articles ont été supprimés avec succès !');
    
        return $this->redirectToRoute('liste_article');
    }
    
}
