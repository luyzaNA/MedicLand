<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\ConsultationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ConsultationController extends AbstractController
{

    public function __construct(private ConsultationService $consultationService, 
                                private SerializerInterface $serializerInterface,
                                private Security $security)
    {
    }

    #[Route('/api/consultation', name: 'add_consultationcon', methods: ['POST'])]

    public function addConsultation(Request $request): JsonResponse
    {

        $doctor = $this->getUser();

        if(!$doctor  instanceof User)     
        {
            return new JsonResponse (['error' => 'Missing patient'], 400);

        }          

        $role = $doctor->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can add consultation'], 403);
        }

        $data = json_decode( $request->getContent(), true);

        if (!isset($data['patient'])) {
            return new JsonResponse( ['error' => 'Missing patient'], 400);
        }

        $patient = $data['patient'];

        $date = new \DateTime($request->get('date'));

        $diseases = $request->get('diseases', []);

        $medication = $request->get('medication', null);

        try {
            $consultation = $this->consultationService->addConsultation(
                $patient,
                $doctor,
                $date,
                $diseases,
                $medication
            );

            return new JsonResponse($this->consultationService->serializeConsultation($consultation));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}