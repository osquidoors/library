<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController {

    /**
     * @Route("/library/list", name="library_list")
     */
    public function list(Request $request) {
        $title = $request->get('title');
        $response = new JsonResponse();
        $response->setData(
            array(
                'success' => true,
                'data' => array(
                    array('id' => 1, 'title'=>'Hacia rutas salvajes'),
                    array('id' => 2, 'title'=>'El nombre del viento'),
                    array('id' => 3, 'title'=>$title),
                )
            )
        );
        return $response;
    }

    /**
     * @Route("/book/create", name="book_create")
     */
    public function create(Request $req, EntityManagerInterface $em) {
        $book = new Book();
        $response = new JsonResponse();
        $title = $req->get('title', null);
        if( empty($title) ) {
            $response->setData(
                array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Title cannot be empty',
                        'data' => null
                    )
                )
            );
        }
        $book->setTitle( $title );
        $em->persist( $book );
        $em->flush();
        $response->setData(array(
            'success' => true,
            'data' => array(
                'id' => $book->getId(),
                'title'=> $book->getTitle()
            )
        ));
        return $response;
    }

    /**
     * @Route("books", name="books_get")
     */
    public function list2(BookRepository $repoBook) {
        $books = $repoBook->findAll();
        $booksAsArray = array();
        foreach ($books as $book) {
            $booksAsArray[] = array(
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage()
            );
        }
        $response = new JsonResponse();
        $response->setData(array(
            'success' => true,
            'data' => $booksAsArray
        ));
        return $response;
    }
}