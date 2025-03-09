<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function save(Employee $employee, bool $flush = false): void
    {
        $this->getEntityManager()->persist($employee);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Employee $employee, bool $flush = false): void
    {
        $this->getEntityManager()->remove($employee);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCompany(int $companyId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.company = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPaginatedEmployees(int $companyId, ?string $search, int $page, int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.company = :companyId')
            ->setParameter('companyId', $companyId)
        ;

        if ($search) {
            $queryBuilder->andWhere('e.firstName LIKE :search OR e.lastName LIKE :search')
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('e.id', 'DESC');

        $maxResultCount = $this->countEmployees($companyId, $search);

        return [
            'data' => $queryBuilder->getQuery()->getResult(),
            'total' => $maxResultCount,
            'pages' => ceil($maxResultCount / $limit),
        ];
    }

    private function countEmployees(int $companyId, ?string $search): int
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.company = :companyId')
            ->setParameter('companyId', $companyId)
        ;

        if ($search) {
            $queryBuilder->andWhere('e.firstName LIKE :search OR e.lastName LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
