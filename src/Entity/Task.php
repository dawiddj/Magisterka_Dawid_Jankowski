<?php

namespace App\Entity;

use App\Entity\Interfaces\FilesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task implements FilesInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
     */
    protected $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="assignedTasks")
     * @ORM\JoinColumn(name="assigned_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assignedTo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinTable(name="tasks_files",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $files;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=4096)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="progress", type="integer", length=3)
     */
    protected $progress;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=false)
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline_at", type="datetime", nullable=true)
     */
    protected $deadlineAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    protected $finishedAt;

    // zmienna przechowująca nazwę statusu
    protected $statusName;

    // zmienna przechowująca kolor etykiety i postępu;
    protected $statusColors = [];

    public function __construct()
    {
        $this->status = 'new';
        $this->progress = 0;
        $this->createdAt = new \DateTime();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeInterface $finishedAt = null): self
    {
        $this->finishedAt = $finishedAt;

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

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    public function getStatusName(): string
    {
        switch ($this->status) {
            case 'accepted':
                $this->statusName = 'W REALIZACJI';
                break;

            case 'canceled':
                $this->statusName = 'ANULOWANE';
                break;

            case 'locked':
                $this->statusName = 'ZABLOKOWANE';
                break;

            case 'finished':
                $this->statusName = 'ZAKOŃCZONE';
                break;

            default:
                $this->statusName = 'NOWE';
                break;
        }

        if ($this->status == 'accepted' && !empty($this->deadlineAt) && $this->deadlineAt < (new \DateTime())) {
            $this->statusName = 'PRZETERMINOWANE';
        }

        return $this->statusName;
    }

    public function getStatusColors(): array
    {
        switch ($this->status) {
            case 'accepted':
                $this->statusColors = [
                    'label' => 'green',
                    'progress' => 'success'
                ];
                break;

            case 'canceled':
                $this->statusColors = [
                    'label' => 'yellow',
                    'progress' => 'warning'
                ];
                break;

            case 'locked':
            case 'finished':
                $this->statusColors = [
                    'label' => 'white',
                    'progress' => 'default'
                ];
                break;

            default:
                $this->statusColors = [
                    'label' => 'blue',
                    'progress' => 'primary'
                ];
                break;
        }

        if ($this->status == 'accepted' && !empty($this->deadlineAt) && $this->deadlineAt < (new \DateTime())) {
            $this->statusColors = [
                'label' => 'red',
                'progress' => 'danger'
            ];
        }

        return $this->statusColors;
    }

    public function getDeadlineAt(): ?\DateTimeInterface
    {
        return $this->deadlineAt;
    }

    public function setDeadlineAt(?\DateTimeInterface $deadlineAt): self
    {
        $this->deadlineAt = $deadlineAt;

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
