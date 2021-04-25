<?php

namespace App\Repository;

use App\Entity\StoredImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoredImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoredImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoredImage[]    findAll()
 * @method StoredImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoredImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoredImage::class);
    }

    // /**
    //  * @return StoredImage[] Returns an array of StoredImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StoredImage
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
