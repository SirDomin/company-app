<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company, bool $flush = false): void
    {
        $this->getEntityManager()->persist($company);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Company $company, bool $flush = false): void
    {
        $this->getEntityManager()->remove($company);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByVat(string $vat): ?Company
    {
        return $this->findOneBy(['vat' => $vat]);
    }

    public function findPaginatedCompanies(?string $search, int $page, int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($search) {
            $queryBuilder->andWhere('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('c.id', 'DESC');

        $maxResultCount = $this->countCompanies($search);

        return [
            'data' => $queryBuilder->getQuery()->getResult(),
            'total' => $maxResultCount,
            'pages' => ceil($maxResultCount / $limit),
        ];
    }

    private function countCompanies(?string $search): int
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        if ($search) {
            $queryBuilder->andWhere('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
