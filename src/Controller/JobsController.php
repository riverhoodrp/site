<?php

namespace App\Controller;

use App\Entity\Jobs;
use App\Form\JobsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class JobsController extends AbstractController
{
    #[Route('/jobs', name: 'jobs_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $jobs = $em->getRepository(Jobs::class)->findAll();

        return $this->render('jobs/list.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/jobs/{slug}', name: 'jobs_detail')]
    public function detail(Jobs $job): Response
    {
        return $this->render('jobs/detail.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/admin/jobs', name: 'admin_jobs_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminList(EntityManagerInterface $em): Response
    {
        $jobs = $em->getRepository(Jobs::class)->findAll();

        return $this->render('jobs/admin_list.html.twig', [
            'jobs' => $jobs
        ]);
    }

    #[Route('/admin/jobs/new', name: 'jobs_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $job = new Jobs();
        $form = $this->createForm(JobsType::class, $job);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('admin_jobs_list');
        }

        return $this->render('jobs/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/jobs/edit/{id}', name: 'jobs_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Jobs $job, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(JobsType::class, $job);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\'annonce a été modifiée avec succès.');

            return $this->redirectToRoute('admin_jobs_list');
        }

        return $this->render('jobs/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/jobs/delete/{id}', name: 'jobs_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Jobs $job, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $em->remove($job);
            $em->flush();

            $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
            dump("L'annonce a été supprimée avec succès.");
        } else {
            $this->addFlash('error', 'Token CSRF invalide. La suppression a échoué.');
            dump("Token CSRF invalide. La suppression a échoué.");
        }

        return $this->redirectToRoute('admin_jobs_list');
    }
}
