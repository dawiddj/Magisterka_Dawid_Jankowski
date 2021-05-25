<?php

namespace App\Controller;

use App\Entity\File;
use App\Handler\FileHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 *
 * Kontroler sterujący zadaniami związanymi z edycją i przydzielaniem zadań/tasków;
 *
 * @package App\Controller
 */
class FileController extends BasicController
{
    /**
     * Wyświetlanie pliku
     *
     * @IsGranted("ROLE_USER")
     * @Route("/files/display/{hash}", name="app_display_file")
     */
    public function getFile(File $file, FileHandler $fileHandler, Request $request): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $fileHandler->getFilePath($file)
        );
    }
}