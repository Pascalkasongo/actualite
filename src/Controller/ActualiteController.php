<?php

namespace App\Controller;

use App\Entity\Actualite;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/actualite')]
final class ActualiteController extends AbstractController
{   
    #[IsGranted('ROLE_ADMIN')]
    #[Route(name: 'app_actualite_index', methods: ['GET'])]
    public function index(ActualiteRepository $actualiteRepository): Response
    {
        return $this->render('actualite/index.html.twig', [
            'actualites' => $actualiteRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

  
#[IsGranted('ROLE_ADMIN')]
#[Route('/new', name: 'app_actualite_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response {

    $actualite = new Actualite();

    $form = $this->createForm(ActualiteType::class, $actualite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        
        $actualite->setCreatedAt(new \DateTimeImmutable());

       
        $actualite->setCreatedBy($this->getUser());

        $coverFile = $form->get('cover')->getData();

        if ($coverFile) {

            $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

            try {
                $coverFile->move(
                    $this->getParameter('covers_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // gérer l'erreur si nécessaire
            }

            $actualite->setCover($newFilename);
        }

        $entityManager->persist($actualite);
        $entityManager->flush();

        return $this->redirectToRoute('app_actualite_index');
    }

    return $this->render('actualite/new.html.twig', [
        'actualite' => $actualite,
        'form' => $form->createView(),
    ]);
}
    #[Route('/{id}/{theme}/{sousTheme}', name: 'app_actualite_show', methods: ['GET'])]
    public function show(Actualite $actualite): Response
    {
        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actualite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Actualite $actualite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actualite/edit.html.twig', [
            'actualite' => $actualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, Actualite $actualite, EntityManagerInterface $entityManager): Response
    {   
        
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actualite);
            $entityManager->flush();
            return $this->redirectToRoute('app_actualite_index');
        }
        
        
    }
}
