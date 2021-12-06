<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registrationAction(Request $request): JsonResponse
    {
        $user = new User();

        $content = json_decode($request->getContent(), true);

        $form = $this->createForm(UserType::class, $user);

        $form->submit($content);

        try {
            if (!$user->getPassword()) {
                throw new RuntimeException();
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $user->setRoles(['ROLE_USER']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(
                [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                ],
                Response::HTTP_CREATED
            );
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
}