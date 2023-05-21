<?php

namespace App\Repository;

use App\Entity\Liked;
use App\Entity\UserProfile;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Liked>
 *
 * @method Liked|null find($id, $lockMode = null, $lockVersion = null)
 * @method Liked|null findOneBy(array $criteria, array $orderBy = null)
 * @method Liked[]    findAll()
 * @method Liked[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liked::class);
    }

    public function save(Liked $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Liked $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Liked[] Returns an array of Liked objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Liked
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Returns an array of Liked objects for the given user profile.
     *
     * @param UserProfile $userProfile
     * @return Liked[]
     */
    public function findLikedRecipesByUserProfile(UserProfile $userProfile): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.recipe', 'r')
            ->andWhere('l.user = :user')
            ->setParameter('user', $userProfile->getUser())
            ->getQuery()
            ->getResult();
    }
}
