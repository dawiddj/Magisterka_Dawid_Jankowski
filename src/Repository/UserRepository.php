<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findLastActiveUsers()
    {
        $lastActiveDate = new \DateTime();
        $lastActiveDate->modify('-15 seconds');

        $users = $this->createQueryBuilder('u')
            ->where('u.isActive > 0')
            ->andWhere('u.lastActiveAt >= :lastActiveDate')
            ->setParameter('lastActiveDate', $lastActiveDate)
            ->getQuery()
            ->getArrayResult();

        if ($users) {
            foreach ($users as $key => $user) {
                if ($user['imageName']) {
                    $users[$key]['userImagePath'] = join('/', str_split($user['id'], 1)).'/'.$user['imageName'];
                } else {
                    $users[$key]['userImagePath'] = 'no-user.png';
                }
            }
        }

        return $users;
    }
}
