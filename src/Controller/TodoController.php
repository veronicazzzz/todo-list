<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TodoController extends AbstractController
{
    /**
     * @var TodoRepository
     */
    private $todoRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param TodoRepository $todoRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(TodoRepository $todoRepository, SerializerInterface $serializer)
    {
        $this->todoRepository = $todoRepository;
        $this->serializer     = $serializer;
    }

    /**
     * @return JsonResponse
     */
    public function getAllAction(): JsonResponse
    {
        $user = $this->getUser();

        $todos = $this->todoRepository->findBy(['user' => $user]);

        return $this->serializedResponse($todos);
    }

    /**
     * @param Todo $todo
     * @return JsonResponse
     */
    public function getAction(Todo $todo): JsonResponse
    {
        if ($todo->getUser() !== $this->getUser()) {
            return new JsonResponse(
                [
                    'code' => '403',
                    'message' => 'Access denied'
                ],
                Response::HTTP_FORBIDDEN);
        }

        return $this->serializedResponse($todo);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $todo = new Todo();

        $content = json_decode($request->getContent(), true);

        $form = $this->createForm(TodoType::class, $todo);

        $form->submit($content);

        try {
            $todo->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            return $this->serializedResponse($todo, 201);
        } catch (\Throwable $e) {
            return new JsonResponse(
                [
                    'code' => '400',
                    'message' => 'Bad request'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param Todo $todo
     * @param Request $request
     * @return JsonResponse
     */
    public function editAction(Todo $todo, Request $request): JsonResponse
    {
        if ($todo->getUser() !== $this->getUser()) {
            return new JsonResponse(
                [
                    'code' => '403',
                    'message' => 'Access denied'
                ],
                Response::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);

        $editedTodo = new Todo();
        $form = $this->createForm(TodoType::class, $editedTodo);

        $form->submit($content);

        try {
            $todo->setThing($editedTodo->getThing() ?: $todo->getThing());
            $todo->setDone($editedTodo->getDone() ?: $todo->getDone());

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            return $this->serializedResponse($todo);
        } catch (\Throwable $e) {
            return new JsonResponse(
                [
                    'code' => '400',
                    'message' => 'Bad request'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param Todo $todo
     * @return JsonResponse
     */
    public function deleteAction(Todo $todo): JsonResponse
    {
        if ($todo->getUser() !== $this->getUser()) {
            return new JsonResponse(
                [
                    'code' => '403',
                    'message' => 'Access denied'
                ],
                Response::HTTP_FORBIDDEN);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);
        $em->flush();

        return $this->serializedResponse($todo, 204);
    }

    /**
     * @param $object
     * @param int $code
     * @return JsonResponse
     */
    private function serializedResponse($object, int $code = 200): JsonResponse
    {
        $response = json_decode($this->serializer->serialize($object, 'json',
            [
                AbstractNormalizer::IGNORED_ATTRIBUTES =>
                    [
                        'user'
                    ]
            ]
        ), true);

        return new JsonResponse($response, $code);
    }
}
