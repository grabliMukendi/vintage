<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/article/liste", name="article_list")
     */
    public function index(EntityManagerInterface $manager)
    {
        return $this->render('article/index.html.twig', [
            'articles' => $manager->getRepository(Articles::class)->findAll()
        ]);
    }

    /**
     * @Route("/admin/article/add", name="article_add")
     */
    public function articleAdd(EntityManagerInterface $manager, Request $request) 
    {
        $article = new Articles;
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            foreach($article->getContents() as $content) 
            {
                $content->setArticles($article);
                $manager->persist($content);
            }

            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'L\'article <strong>'. $article->getTitle() .',</strong> a été ajouté avec succès.');
            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/article_add_up.html.twig', 
        [
            'action' => 'Ajouter',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/update/{id}", name="article_update")
     * @param Articles $id
     */
    public function articleUpdate($id = null, EntityManagerInterface $manager, Request $request) 
    {
        $article = $manager->getRepository(Articles::class)->find($id);
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'L\'article <strong>'. $article->getTitle() .',</strong> a été modifié avec succès.');
            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/article_add_up.html.twig', 
        [
            'action' => 'Modifier',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/delete/{id}", name="article_delete")
     * @param Articles $id
     */
    public function articleDelete($id = null, EntityManagerInterface $manager) 
    {
        $article = $manager->getRepository(Articles::class)->find($id);

        if(null !== $article) 
        {  
            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', 'L\'article <strong>'. $article->getTitle() .',</strong> a été supprimé avec succès.');
        }

        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/terms-of-sale", name="conditions_vente")
     */
    public function termsOfSale(EntityManagerInterface $manager) 
    {
        return $this->render('article/terms_of_sale.html.twig', 
        [
            'terms_of_sale' => $manager->getRepository(Articles::class)->findBy([ 'title' => 'Conditions de vente' ]),
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function mentionsLegales(EntityManagerInterface $manager) 
    {
        return $this->render('article/mentions_legales.html.twig', 
        [
            'mentions_legales' => $manager->getRepository(Articles::class)->findBy([ 'title' => 'Mentions Légales' ]),
        ]);
    }

    /**
     * @Route("/qui-sommes-nous", name="qui_sommes_nous")
     */
    public function quiSommesNous(EntityManagerInterface $manager) 
    {
        return $this->render('article/qui_sommes_nous.html.twig', 
        [
            'qui_sommes_nous' => $manager->getRepository(Articles::class)->findOneBy([ 'title' => 'Qui sommes-nous ?' ]),
        ]);
    }

}
