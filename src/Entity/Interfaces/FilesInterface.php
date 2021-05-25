<?php


namespace App\Entity\Interfaces;


use App\Entity\File;
use Doctrine\Common\Collections\Collection;

interface FilesInterface
{
    public function getFiles(): Collection;

    public function addFile(File $file): self;

    public function removeFile(File $file): self;
}