<?php

namespace App\Entity;

use App\Entity\Interfaces\FilesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message implements FilesInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", length=8)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinTable(name="messages_files",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $files;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=1024, nullable=false)
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        $this->files->removeElement($file);

        return $this;
    }
}
