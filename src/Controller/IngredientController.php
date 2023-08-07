<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IngredientController extends AbstractController
{
     #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {   
        /**
         * Cette fonction afiche tous les ingrédients
        */
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' =>  $ingredients
        ]);
    }

    /**
     * Ajout d'un nouveau ingrédient
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new')]
    public function new( Request $request, EntityManagerInterface $manager): Response {

         $ingredient = new Ingredient();
         $form = $this->createForm(IngredientType::class, $ingredient);

         $form -> handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
           $ingredient = $form->getData();
           
           $manager->persist($ingredient);
           $manager->flush();
  
           $this->addFlash(
            'success',
            'Ingrédient créer avec succès !'
           );

            return $this->redirectToRoute('ingredient.index');
         }
        
         return $this->render('pages/ingredient/new.html.twig',[
             'form' => $form->createView()
            ]);
    }
   /**
    * Modification des données
    */


   #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit( IngredientRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
{
    $ingredient = $repository ->findOneBy(["id" => $id]);
    $form = $this->createForm(IngredientType::class, $ingredient);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $ingredient = $form->getData();

    
        $manager->persist($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Ingrédient modifié avec succès!'
        );

        return $this->redirectToRoute('ingredient.index');
    }

    return $this->render('pages/ingredient/edit.html.twig', [
        'form' => $form->createView()
    ]);
}
/**
 * Suppression d'un nouveau d'un grédient
 */
#[ROute('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
 public function delete(IngredientRepository $repository, int $id, EntityManagerInterface $manager) : Response
 {
    $ingredient = $repository ->findOneBy(["id" => $id]);
    $manager->remove($ingredient);
    $manager->flush();

     $this->addFlash(
            'success',
            'Ingrédient supprimé avec succès!'
        );


    return $this->redirectToRoute('ingredient.index');
 }

   
}
