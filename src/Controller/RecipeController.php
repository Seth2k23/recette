<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
            /**
             * Cette fonction afiche tous les ingrédients
            */
              $recipes = $paginator->paginate(
                $repository->findAll(),
                $request->query->getInt('page', 1),
                8
            );
            return $this->render('pages/recipe/index.html.twig', [
                'recipes' =>  $recipes,
            ]);
        }
        /**
         * Ajout d'un nouveau recette
         */
        #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $manager): Response
        {

            $recipe = new Recipe();
            $form = $this->createForm(RecipeType::class, $recipe);

            $form->handleRequest($request);
             if($form->isSubmitted() && $form->isValid()){
             $recipe = $form->getData();
             
             $manager->persist($recipe);
             $manager->flush();

             $this->addFlash(
                'success',
                'Recette ajouter avec succès!'
            );

             return $this->redirectToRoute('recipe.index');

            }


            return $this->render('pages/recipe/new.html.twig', [
                'form' => $form->createView()
            ]);
        }
        /**
         * Modification de recette
         */
        #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
        public function edit(RecipeRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
         $recipe = $repository ->findOneBy(["id" => $id]);
        $form = $this->createForm(RecipeType::class,  $recipe);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
    
        
            $manager->persist( $recipe);
            $manager->flush();
    
            $this->addFlash(
                'success',
                'Recette modifié avec succès!'
            );
    
            return $this->redirectToRoute('recipe.index');
        }
    
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Suppression de recette
     */
    #[ROute('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
 public function delete(RecipeRepository $repository, int $id, EntityManagerInterface $manager) : Response
 {
    $recipe = $repository ->findOneBy(["id" => $id]);
    $manager->remove($recipe);
    $manager->flush();

     $this->addFlash(
            'success',
            'Recette supprimé avec succès!'
        );


    return $this->redirectToRoute('recipe.index');
 }
}
