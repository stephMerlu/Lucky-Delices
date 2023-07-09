<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

public function findByCategory(string $category): array
{
    return $this->createQueryBuilder('r')
        ->join('r.category', 'c')
        ->andWhere('c.name = :category')
        ->setParameter('category', $category)
        ->getQuery()
        ->getResult();
}

public function searchRecipes($searchTerm, $searchBy)
{
    $queryBuilder = $this->createQueryBuilder('r');

    if ($searchTerm) {
        if ($searchBy === 'name') {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('r.name', ':searchTerm')
            );
        } elseif ($searchBy === 'ingredient') {
            $queryBuilder->join('r.ingredient', 'i')
                ->andWhere(
                    $queryBuilder->expr()->like('i.name', ':searchTerm')
                );
        }

        $queryBuilder->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}

public function findRecentRecipes($limit)
{
    return $this->createQueryBuilder('r')
        ->orderBy('r.updatedAt', 'DESC') 
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

public function findFavoriteRecipes()
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.favorite = true')
        ->getQuery()
        ->getResult();
}

public function findNewsletterRecipe(): ?Recipe
{
    return $this->createQueryBuilder('r')
        ->setMaxResults(1)
        ->orderBy('r.createdAt', 'DESC')
        ->getQuery()
        ->getOneOrNullResult();
}
//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
