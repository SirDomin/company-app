<?php

namespace App\Controller;

use App\Entity\Company;
use App\Exception\InvalidFieldsException;
use App\Factory\CompanyFactory;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
        private readonly CompanyFactory $companyFactory,
    ) {
    }

    #[Route('/api/companies', name: 'companies_create', methods: ['POST'])]
    public function createCompany(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $company = $this->companyFactory->createFromArray($data);
        } catch (InvalidFieldsException $exception) {
            return $this->json([
                'error' => 'Invalid fields',
                'fields' => $exception->getInvalidFields(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->companyRepository->save($company, true);

        return $this->json([
            'message' => 'Company created successfully',
            'company' => $company,
        ], Response::HTTP_CREATED, [], ['groups' => 'company:write']);
    }

    #[Route('/api/companies/{id}', name: 'companies_get_one', methods: ['GET'])]
    public function getCompany(Company $company): JsonResponse
    {
        return $this->json([
            'company' => $company,
        ], Response::HTTP_OK, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies', name: 'companies_get_list', methods: ['GET'])]
    public function getCompanies(Request $request): JsonResponse
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

        $companiesData = $this->companyRepository->findPaginatedCompanies($search, $page, $limit);

        return $this->json([
            'data' => $companiesData['data'],
            'page' => $page,
            'limit' => $limit,
            'total' => $companiesData['total'],
            'pages' => $companiesData['pages'],
        ], Response::HTTP_OK, [], ['groups' => 'company:list']);
    }

    #[Route('/api/companies/{id}', name: 'companies_update', methods: ['PUT'])]
    public function updateCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company = $this->companyFactory->updateFromArray($company, $data);

        $this->companyRepository->save($company, true);

        return $this->json([
            'message' => 'Company updated successfully',
            'company' => $company,
        ], Response::HTTP_OK, [], ['groups' => 'company:write']);
    }

    #[Route('/api/companies/{id}', name: 'companies_delete', methods: ['DELETE'])]
    public function deleteBook(Company $company): JsonResponse
    {
        $this->companyRepository->remove($company, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
