<?php

namespace App\Controller;


use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class BookController extends AbstractController
{
    #[Route('/api/books', name: 'books', methods: ['GET'])]
    public function getBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {

        $booklist = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($booklist,'json');
        
        return new JsonResponse($jsonBookList,Response::HTTP_OK,[],true);
    }

    #[Route('/api/books/pages', name: 'books_page', methods: ['GET'])]
    public function getBooksPaged(Request $request, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);

        $limit = $request->get('limit', 3);
        $booklist = $bookRepository->findAllWithPagination($page,
        $limit);
        $jsonBookList = $serializer->serialize($booklist,'json');
        
        return new JsonResponse($jsonBookList,Response::HTTP_OK,[],true);
    }

    #[Route('/api/books/{id}', name: 'book_by_id', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas les droits suffisants pour créer un livre')]
    
    public function getBookById(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {

        $book = $bookRepository->find($id);
        if ($book)
        {
            $jsonBook = $serializer->serialize($book,'json');
            return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);
    }
}
