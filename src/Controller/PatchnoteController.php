<?php

namespace App\Controller;

use App\Entity\Patchnote;
use App\Form\PatchnoteType;
use App\Repository\PatchnoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class PatchnoteController extends AbstractController
{
    #[Route('/admin/patchnotes', name: 'app_patchnote_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PatchnoteRepository $patchnoteRepository): Response
    {
        return $this->render('patchnote/index.html.twig', [
            'patchnotes' => $patchnoteRepository->findAll(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/admin/patchnotes/new', name: 'app_patchnote_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $patchnote = new Patchnote();
        $form = $this->createForm(PatchnoteType::class, $patchnote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('patchnote_images_directory'), // Directory in config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                // Update the 'image' property to store the file name
                $patchnote->setImage($newFilename);
            }

            $entityManager->persist($patchnote);
            $entityManager->flush();

            return $this->redirectToRoute('app_patchnote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('patchnote/new.html.twig', [
            'patchnote' => $patchnote,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_patchnote_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Patchnote $patchnote, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PatchnoteType::class, $patchnote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Delete old image if new one is uploaded
                if ($patchnote->getImage()) {
                    unlink($this->getParameter('patchnote_images_directory').'/'.$patchnote->getImage());
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('patchnote_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception during file upload
                }

                // Update the 'image' property to store the file name
                $patchnote->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_patchnote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('patchnote/edit.html.twig', [
            'patchnote' => $patchnote,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_patchnote_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Patchnote $patchnote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$patchnote->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($patchnote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_patchnote_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/patchnotes', name: 'show_patchnotes')]
    public function showPatchnotes(PatchnoteRepository $patchnoteRepository): Response
    {
        return $this->render('patchnote/show_patchnotes.html.twig', [
            'patchnotes' => $patchnoteRepository->findAll(),
        ]);
    }

    #[Route('/patchnotes/{id}', name: 'app_patchnote_show', methods: ['GET'])]
    public function show(Patchnote $patchnote): Response
    {
        return $this->render('patchnote/show.html.twig', [
            'patchnote' => $patchnote,
        ]);
    }
}
