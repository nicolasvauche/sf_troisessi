<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        try {
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);

            $errors = $form->getErrors(true);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $mediaFile */
                $mediaFile = $form->get('media')->getData();
                if ($mediaFile) {
                    $filename = $fileUploader->upload($mediaFile, $this->getParameter('media_directory'), $slugger->slug($post->getTitle()));
                    $post->setMedia($filename);
                }

                $postRepository->add($post, true);

                return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('post/new.html.twig', [
                'post' => $post,
                'form' => $form,
                'errors' => $errors,
            ]);
        } catch (FormSizeFileException $e) {
            dd($e->getMessage());
        }
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, FileUploader $fileUploader, SluggerInterface $slugger, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $errors = $form->getErrors(true);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $mediaFile */
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile) {
                if ($post->getMedia()) {
                    $fileUploader->delete($this->getParameter('media_directory'), $post->getMedia());
                }

                $filename = $fileUploader->upload($mediaFile, $this->getParameter('media_directory'), $slugger->slug($post->getTitle()));
                $post->setMedia($filename);
            }
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository, FileUploader $fileUploader): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            if ($post->getMedia()) {
                $fileUploader->delete($this->getParameter('media_directory'), $post->getMedia());
            }
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
