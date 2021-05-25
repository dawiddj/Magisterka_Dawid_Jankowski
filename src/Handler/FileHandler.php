<?php

namespace App\Handler;

use App\Entity\File;
use App\Entity\Interfaces\FilesInterface;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHandler extends BaseEntityHandler
{
    protected $entityName = 'File';

    /** @var FileRepository */
    protected $repository;

    /**
     * @param UploadedFile[]|null $uploadedFiles
     * @throws \Exception
     */
    public function uploadFiles(FilesInterface $entity, array $uploadedFiles = []): void
    {
        if ($uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $fileEntity = (new File())
                    ->setFileName($uploadedFile->getClientOriginalName())
                    ->setHash($this->randomStr(64))
                    ->setMimeType($uploadedFile->getMimeType());
                $this->save($fileEntity);

                try {
                    $fileDirectory = $this->getFileDirectory($fileEntity);

                    $uploadedFile->move($fileDirectory, $uploadedFile->getClientOriginalName());

                    $entity->addFile($fileEntity);
                } catch (\Exception $exception) {
                    $this->remove($fileEntity);
                    throw $exception;
                }
            }

            $this->save($fileEntity);
        }
    }

    public function getFileDirectory(File $file): ?string
    {
        $directory = $this->parameterBag->get('files_dir');
        $subdirectory = implode(DIRECTORY_SEPARATOR, str_split((string)$file->getId()));
        $fileDirectory = $directory.DIRECTORY_SEPARATOR.$subdirectory;

        if (!file_exists($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }

        return $fileDirectory;
    }

    public function getFilePath(File $file): ?string
    {
        return $this->getFileDirectory($file).DIRECTORY_SEPARATOR.$file->getFileName();
    }

    public function randomStr($length) : string
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}