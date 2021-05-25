<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="first_name", type="string", length=180)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=180)
     */
    private $lastName;

    /**
     * @ORM\Column(name="image_name", type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(name="user_roles", type="array")
     */
    private $roles = [];

    private $type;

    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_logged_at", type="datetime", nullable=true)
     */
    protected $lastLoggedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_active_at", type="datetime", nullable=true)
     */
    protected $lastActiveAt;

    protected $userImage;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->createdAt = new \DateTime();
        $this->roles = [];
        $this->roles[] = 'ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;

        return $this;
    }

    public function getChoiceData()
    {
        return $this->username.' ('.$this->firstName.' '.$this->lastName.')';
    }

    public function serialize()
    {
        return serialize(array(
            $this->id, $this->username, $this->password, $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list ($this->id, $this->username, $this->password, $this->salt)

            = unserialize($serialized, array('allowed_classes' => false));
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastLoggedAt(): ?\DateTimeInterface
    {
        return $this->lastLoggedAt;
    }

    public function setLastLoggedAt(?\DateTimeInterface $lastLoggedAt): self
    {
        $this->lastLoggedAt = $lastLoggedAt;

        return $this;
    }

    public function getLastActiveAt(): ?\DateTimeInterface
    {
        return $this->lastActiveAt;
    }

    public function setLastActiveAt(?\DateTimeInterface $lastActiveAt): self
    {
        $this->lastActiveAt = $lastActiveAt;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUserImage()
    {
        return $this->userImage;
    }

    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;

        return $this;
    }

    public function getType(): ?string
    {
        if (in_array('ROLE_ADMIN', $this->roles)) {
            $this->type = 'administrator';
        } else {
            $this->type = 'student';
        }

        return $this->type;
    }

    public function setType(string $userType): self
    {
        $this->roles = [];
        $this->roles[] = 'ROLE_USER';

        if ($userType == 'administrator') {
            $this->roles[] = 'ROLE_ADMIN';
        }

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getUserImagePath(): ?string
    {
        if ($this->imageName) {
            return '/user_images/'.join('/', str_split($this->getId(), 1)).'/'.$this->imageName;
        }

        return '/user_images/no-user.png';
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
