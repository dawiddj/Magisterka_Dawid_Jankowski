<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessages(User $user, int $limit = 100, int $timestamp = 0, ?string $searchPhrase = null)
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->addSelect('u.id as userId, u.firstName', 'u.lastName', 'u.imageName', 'u.lastActiveAt')
            ->leftJoin('App:User', 'u', Expr\Join::WITH, 'm.createdBy = u.id');

        if ($searchPhrase && strlen($searchPhrase) > 1) {
            $queryBuilder->leftJoin('m.files', 'f')
                ->where('m.content LIKE :phrase')
                ->orWhere('f.fileName LIKE :phrase')
                ->setParameter('phrase', '%'.$searchPhrase.'%');
        }

        $rawMessages = $queryBuilder->setMaxResults($limit)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $messages = [];
        $messagesIds = [];

        if ($rawMessages) {
            foreach ($rawMessages as $rawMessage) { // przetwarzanie pobranych wiadomości;
                $rawMessageData = $rawMessage[0];
                $userData = $rawMessage;
                unset($userData[0]);

                $currentDate = new \DateTime();
                $currentDate->modify('-15 seconds');

                $rawMessage = array_merge($rawMessageData, $userData); // scalanie/spłaszczanie tablicy;

                $rawMessage['ownMessage'] = ($rawMessage['userId'] == $user->getId()); // własna wiadomość
                $rawMessage['offline'] = ($rawMessage['lastActiveAt'] < $currentDate); // użytkownik jest offline od minimum 15 sekund;
                $rawMessage['createdAt'] = $rawMessage['createdAt']->format('Y-m-d H:i'); // data utworzenia;
                $rawMessage['files'] = [];

                if ($rawMessage['imageName']) { // ścieżka do awatara użytkownika;
                    $rawMessage['userImagePath'] = join('/', str_split($rawMessage['userId'], 1)).'/'.$rawMessage['imageName'];
                } else {
                    $rawMessage['userImagePath'] = 'no-user.png';
                }

                if ($rawMessage['userId']) { // personalia (imię i nazwisko) użytkownika, który napisał wiadomość;
                    $rawMessage['userPersonalities'] = $rawMessage['firstName'].' '.$rawMessage['lastName'];
                } else {
                    $rawMessage['userPersonalities'] = 'użytkownik usunięty';
                }

                $messages[$rawMessage['id']] = $rawMessage;
                $messagesIds[] = $rawMessage['id'];
            }
        }

        if ($messagesIds) {
            $files = $this->_em->createQueryBuilder()
                ->select('f', 'm.id as messageId')
                ->from('App:File', 'f')
                ->join('App:Message', 'm', Expr\Join::WITH, 'f MEMBER OF m.files')
                ->where('m.id IN (:messagesIds)')
                ->setParameter('messagesIds', $messagesIds)
                ->getQuery()
                ->getArrayResult();

            if ($files) {
                foreach ($files as $file) {
                    $messages[$file['messageId']]['files'][] = $file[0];
                }
            }
        }

        return $messages;
    }
}
