<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use League\Flysystem\FilesystemOperator;

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
    public function book( EntityManagerInterface $em, Request $request, FilesystemOperator $defaultStorage) {
        $bookDto = new BookDto();
        $form = $this->createForm(BookFormType::class, $bookDto);
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            // print ($bookDto->base64Image); die();
            $extension = explode('/', mime_content_type($bookDto->base64Image))[1];
            $data = explode(',', $bookDto->base64Image);
            $fileName = sprintf('%s.%s', uniqid('book', true), $extension);
            $defaultStorage->write($fileName, base64_decode($data[1]));
            $book = new Book();
            $book->setTitle( $bookDto->title );
            $book->setImage( $fileName );
            $em->persist($book);
            $em->flush();
            return $book;
        }
        return $form;
    }

}