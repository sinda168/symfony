<?php
   //<h1> mon prmier page index.html.twigg</h1> on page index.html.twig
        //<h1>Bonjour {{prenom}}</h1>

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
     /*   #[Route('/', name: 'home')]
    public function home(): Response 
    {
        return new Response('<h1>Ma première pagee Symfony</h1>');
    }
#[Route('/acceuil',name:'homepage1')]
public function home1(): Response 
{
    return $this->render('index.html.twig');
}
#[Route('/acceuil/{name}', name: 'homepage2')]
public function home2($name): Response
{

    return $this->render('index.html.twig', ['prenom' => $name]);
}*/
  


    #[Route("/", name:"article_list")]
    public function home(EntityManagerInterface $em): Response
    {
        // Récupérer tous les articles de la base de données
        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    #[Route('/article/save', name: 'addArticle')]
    public function save(EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix(1000);

        $em->persist($article);
        $em->flush();

        return new Response('Article enregistré avec id ' . $article->getId());
    }

    #[Route('/article/new', name: 'new_article', methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm(); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    //show
    #[Route('/article/{id}' , name:'article_show')]
    public function show($id, EntityManagerInterface $em):Response{
        $article =$em->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig',array('article'=> $article));
    }

    #[Route('/article/edit/{id}', name: 'edit_article', methods: ["GET", "POST"])]
    public function edit(Request $request,$id, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article =$em->getRepository(Article::class)->find($id);
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm(); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/article/delete/{id}' , name:'delete_article',methods:["GET"])]
    public function delete(Request $request, $id, EntityManagerInterface $em):Response
    {
        $article = $em->getRepository(Article::class)->find($id);

        $em->remove($article);
        $em->flush();

        $response = new Response();
        $response->send();
        return $this->redirectToRoute('article_list');
    }
}
