<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Exception\InvalidFieldsException;
use App\Factory\EmployeeFactory;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EmployeeFactory $employeeFactory,
    ) {
    }

    #[Route('/api/employees', name: 'employees_create', methods: ['POST'])]
    public function createEmployee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $employee = $this->employeeFactory->createFromArray($data);
        } catch (InvalidFieldsException $exception) {
            return $this->json([
                'error' => 'Invalid fields',
                'fields' => $exception->getInvalidFields(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->employeeRepository->save($employee, true);

        return $this->json([
            'message' => 'Employee created successfully',
            'employee' => $employee,
        ], Response::HTTP_CREATED, [], ['groups' => 'employee:write']);
    }

    #[Route('/api/employees/{id}', name: 'employees_get_one', methods: ['GET'])]
    public function getEmployee(Employee $employee): JsonResponse
    {
        return $this->json([
            'employee' => $employee,
        ], Response::HTTP_OK, [], ['groups' => 'employee:read']);
    }

    #[Route('/api/companies/{companyId}/employees', name: 'employees_get_list', methods: ['GET'])]
    public function getEmployees(Request $request, int $companyId): JsonResponse
    {
        $search = $request->query->get('search');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        if ($page < 1) {
            return $this->json(['error' => 'Page must be greater than 0'], Response::HTTP_BAD_REQUEST);
        }

        if ($limit < 1) {
            return $this->json(['error' => 'Limit must be greater than 0'], Response::HTTP_BAD_REQUEST);
        }

        $employeesData = $this->employeeRepository->findPaginatedEmployees($companyId, $search, $page, $limit);

        return $this->json([
            'data' => $employeesData['data'],
            'page' => $page,
            'limit' => $limit,
            'total' => $employeesData['total'],
            'pages' => $employeesData['pages'],
        ], Response::HTTP_OK, [], ['groups' => 'employee:list']);
    }

    #[Route('/api/employees/{id}', name: 'employees_update', methods: ['PUT'])]
    public function updateEmployee(Request $request, Employee $employee): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $employee = $this->employeeFactory->updateFromArray($employee, $data);
        } catch (InvalidFieldsException $exception) {
            return $this->json([
                'error' => 'Invalid fields',
                'fields' => $exception->getInvalidFields(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->employeeRepository->save($employee, true);

        return $this->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee,
        ], Response::HTTP_OK, [], ['groups' => 'employee:write']);
    }

    #[Route('/api/employees/{id}', name: 'employees_delete', methods: ['DELETE'])]
    public function deleteEmployee(Employee $employee): JsonResponse
    {
        $this->employeeRepository->remove($employee, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
