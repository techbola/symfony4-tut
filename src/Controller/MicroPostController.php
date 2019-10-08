<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="micro_post_")
 */
class MicroPostController extends AbstractController
{

    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(MicroPostRepository $microPostRepository, EntityManagerInterface $entityManager)
    {
        $this->microPostRepository = $microPostRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('micro_post/index.html.twig', [
//            'posts' => $this->microPostRepository->findAll(),
            'posts' => $this->microPostRepository->findBy(
                [], ['time' => 'DESC']),
        ]);
    }

    /**
     * @Route("/add", name="add")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setTime(new DateTime());

        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('micro_post_index'));
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param MicroPost $microPost
     * @param Request $request
     * @Route("/edit/{id}", name="edit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('micro_post_index'));
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param MicroPost $microPost
     * @Route("/delete/{id}", name="delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(MicroPost $microPost)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->addFlash('notice', 'Post deleted!');

        return $this->redirect($this->generateUrl('micro_post_index'));
    }

    /**
     * @param MicroPost $post
     * @return Response
     * @Route("/{id}", name="post")
     */
    public function post(MicroPost $post)
    {
        return $this->render('micro_post/post.html.twig', [
            'post' => $post
        ]);
    }

}
