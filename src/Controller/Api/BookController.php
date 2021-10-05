<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractFOSRestController {

    /**
     * @Rest\Get(path="/books/")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function books( BookRepository $bookRepository ) {
        return $bookRepository->findAll();
    }

    /**
     * @Rest\Post(path="/book/")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function book( EntityManagerInterface $em, Request $request) {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $em->persist($book);
            $em->flush();
            return $book;
        }
        return $form;
    }

}