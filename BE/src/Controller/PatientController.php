<?php

namespace App\Controller;

use App\Services\ConsultationService;
use App\Services\PatientService;
use App\Entity\User;
use App\Entity\Disease;
use App\Entity\DiseaseCategory;

use App\Entity\Patient;
use App\Entity\BloodGroup;
use App\Entity\RhFactor;
use App\Services\DiseaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Webmozart\Assert\Assert as AssertAssert;

class PatientController extends AbstractController
{

    public function __construct(private PatientService $patientService,
                                private ValidatorInterface $validator,
                                private ConsultationService $consultationService,
                                private Security $security,
                                private DiseaseService $diseaseService
    ){}

    #[Route('/api/patients', name: 'get_all_patients', methods: ['GET'])]
    public function getAllPatients(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can view patients'], 403);
        }

        $patients = $this->patientService->getAllPatients();

        $patientsJson = $this->patientService->serializePatients($patients);

        return new JsonResponse($patientsJson, 200, [], true);
    }

    #[Route('/api/patients/{cnp}', name: 'get_patient_by_cnp', methods: ['GET'])]
    public function getPatientByCnp(string $cnp): JsonResponse
    {

        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can search a patient'], 403);
        }

        $patient = $this->patientService->getPatientByCnp($cnp);
        if (!$patient) {
            return $this->json(['message' => 'Patient not found'], 404);
        }
        $patientJson = $this->patientService->serializePatient($patient);

        return new JsonResponse($patientJson, 200, [], true);
    }

    #[Route('/api/patients', name: 'add_patient', methods: ['POST'])]
    public function addPatient(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can add a patient'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
        'email' => [new Assert\NotBlank(), new Assert\Email()],
         'cnp' => [new Assert\NotBlank(), new Assert\Length(['min' => 13, 'max' => 13])],
        'firstName' => [new Assert\NotBlank()],
        'lastName' => [new Assert\NotBlank()],
        'locality' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],
        'address' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],

        'phone' => [new Assert\NotBlank(), new Assert\Length(['min' => 10])],
        'bloodGroup' => [new Assert\NotNull(), new Assert\Choice(['choices' => ['A', 'B', 'O', 'AB'], 'message' => 'Invalid blood group'])],
        'rh' => [new Assert\NotNull(), new Assert\Choice(['choices' => ['NEGATIV', 'POZITIV'], 'message' => 'Invalid Rh factor'])],
        'weight' => [new Assert\Positive()],
        'height' => [new Assert\Positive()],
        'allergies' => [new Assert\Length(['max' => 255])],
        'occupation' => [new Assert\Length(['max' => 100])],
        'diseases' => [new Assert\Type(['type' => 'array'])]]);
    $violations = $this->validator->validate($data, $constraints);

    if (count($violations) > 0) {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }
        return new JsonResponse(['errors' => $errors], 400);
    }

    $recordDate = new \DateTime();


    $patient = (new Patient())
        ->setEmail($data['email'])
        ->setCnp($data['cnp'])
        ->setFirstName($data['firstName'])
        ->setLastName($data['lastName'])
        ->setLocality($data['locality'])
        ->setAddress($data['address'])
        ->setPhone($data['phone'])
        ->setBloodGroup(BloodGroup::from($data['bloodGroup']))
        ->setRh(RhFactor::from($data['rh']))
        ->setWeight($data['weight'])
        ->setHeight($data['height'])
        ->setAllergies($data['allergies'])
        ->setOccupation($data['occupation'])
        ->setRecordDate($recordDate);
        $diseases = $data['diseases'] ?? [];
        foreach ($diseases as $diseaseData) {
            try {
                $category = $diseaseData['category'];
                
                if (is_string($category)) {
                    $category = DiseaseCategory::tryFrom($category);
                    if (!$category) {
                        $category = DiseaseCategory::OTHER; 
                    }
                }  if ($category instanceof DiseaseCategory) {
                    $disease = $this->diseaseService->addDisease(
                        $diseaseData['name'],
                        $category,
                        $diseaseData['description']
                    );
                   
                        $patient->addMedicalHistory($disease);
                    
                } else {
                    throw new \InvalidArgumentException('Invalid disease category.');
                }
            } catch (\Exception $e) {
                return new JsonResponse(['error' => $e->getMessage()], 400);
            }
        }
        
       try {
           $this->patientService->addPatient($patient);
           return new JsonResponse(['message' => 'Patient added successfully'], 201);
       } catch (\Exception $e) {
           return new JsonResponse(['error' => $e->getMessage()], 400);
       }
}


    #[Route('/api/patients/{cnp}', name: 'update_patient', methods: ['PUT'])]
    public function updatePatient(string $cnp, Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can update a patient'], 403);
        }
        $data = json_decode($request->getContent(), true);
        

        try {
            $patient = $this->patientService->updatePatient($cnp, $data);

            if (!$patient) {
                return new JsonResponse(['error' => 'Patient not found'], 404);
            }
            $patientJson = $this->patientService->serializePatient($patient);

            return new JsonResponse($patientJson, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 400);
        }
    }

    #[Route('/api/patients/{cnp}', name: 'delete_patient', methods: ['DELETE'])]
    public function deletePatient(string $cnp): JsonResponse
    { 
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctor can delete a patient.'], 403);
        }
        try {
            $this->patientService->deletePatient($cnp);
            return new JsonResponse(['success' => 'Doctor deleted successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/Newpatients', name: 'get_patients_doctor', methods: ['GET'])]
    public function getPatientsByDoctor(): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }
    
        $role = $user->getRoles();
    
        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can view patients'], 403);
        }
    
        $email = $user->getEmail(); 
        $patients = $this->patientService->getPatientsByDoctorEmail($email);
    
        if (!$patients) {
            return new JsonResponse(['message' => 'No patients found for the given doctor'], 404);
        }
    
        $patientsJson = $this->patientService->serializePatients($patients);
        return new JsonResponse($patientsJson, 200, [], true);

    }
    #[Route('/api/patients/search/{Diseasename}', name: 'find_patients_by_doctor_and_disease', methods: ['GET'])]
public function findPatientsByDoctorAndDisease(string $Diseasename): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['error' => 'Missing user'], 400);
    }

    $role = $user->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can search patients'], 403);
    }

    $doctorEmail = $user->getEmail();

    try {
        $patients = $this->patientService->findPatientsByDoctorAndDisease($doctorEmail, $Diseasename);
        
        if (empty($patients)) {
            return new JsonResponse(['message' => 'No patients found for the given doctor and disease'], 404);
        }

        $patientsJson = $this->patientService->serializePatients($patients);
        return new JsonResponse($patientsJson, 200, [], true);

    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}
#[Route('/api/patients/specialization/count', name: 'count_patients_by_specialization', methods: ['GET'])]
public function countPatientsBySpecialization(): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['error' => 'Missing user'], 400);
    }

    $role = $user->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can view the count of patients by specialization'], 403);
    }

    try {
        $counts = $this->patientService->countPatientsBySpecialization();
        
        return new JsonResponse($counts, 200);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}
    #[Route('/api/patients/chronic/count', name: 'count_patients_by_chronic_disease', methods: ['GET'])]
    public function countPatientsByChronicDisease(): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can view the count of patients by chronic diseases'], 403);
        }

        try {
            $counts = $this->patientService->countPatientsByChronicDisease();

            return new JsonResponse($counts, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

     
}

