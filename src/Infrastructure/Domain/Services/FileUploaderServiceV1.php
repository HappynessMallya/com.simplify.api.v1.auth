<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Model\Company\TaxIdentificationNumber;
use App\Domain\Services\FileUploaderService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderServiceV1 implements FileUploaderService
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var string */
    private string $folderPath;

    /**
     * @param LoggerInterface $logger
     * @param string $folderPath
     */
    public function __construct(LoggerInterface $logger, string $folderPath)
    {
        $this->logger = $logger;
        $this->folderPath = $folderPath;
    }

    /**
     * @param UploadedFile $file
     * @param string $tin
     * @return string
     */
    public function uploadFile(UploadedFile $file, TaxIdentificationNumber $tin): string
    {
        $filename = $file->getClientOriginalName();
        $filepath = $this->getFolderPath() . '/' . $tin->value() . '/';

        try {
            $file->move($filepath, $filename);
        } catch (FileException $e) {
            $this->logger->critical(
                'Exception error trying to upload file',
                [
                    'filepath' => $filepath,
                    'filename' => $filename,
                    'method' => __METHOD__,
                ]
            );
        }

        return $filepath . $filename;
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return $this->folderPath;
    }
}