<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Model\Company\CertificatePassword;
use App\Domain\Services\CertificateDataService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CertificateDataFileService
 * @package App\Infrastructure\Domain\Services
 */
class CertificateDataFileService implements CertificateDataService
{
    public const CERTIFICATE_PASSWORD = 'Kimara20';

    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param string $filepath
     * @param CertificatePassword $certificatePassword
     * @return string
     */
    public function createCertificateData(string $filepath, CertificatePassword $certificatePassword): string
    {
        $filesystem = new Filesystem();
        $serializer = $this->getSerializer();

        // Path to the PFX file
        $pfxFilePath = $filepath;

        $filepathWithoutExtension = explode('.', $pfxFilePath);

        $dataFilePath = $filepathWithoutExtension[0] . '.data';

        // Step 1: Convert PFX to PEM
        $pemFilePath = $this->convertPfxToPem($filesystem, $filepathWithoutExtension[0], $certificatePassword->value());

        // Step 2: Extract Certificate Information
        $certificateInfo = $this->extractCertificateInfo($pemFilePath);
        // Prepare the data for JSON serialization
        $jsonData = [
            'serial' => $certificateInfo['serial'],
            'password' => $certificatePassword->value()
        ];

        // Step 3: Convert to JSON
        $json = $serializer->serialize($jsonData, JsonEncoder::FORMAT);

        // Step 4: Save JSON to file
        $filesystem->dumpFile($dataFilePath, $json);

        return $dataFilePath;
    }

    /**
     * @param Filesystem $filesystem
     * @param string $pfxFilePath
     * @param string $certificatePassword
     * @return string
     */
    private function convertPfxToPem(Filesystem $filesystem, string $pfxFilePath, string $certificatePassword): string
    {
        $pemFilePath = $pfxFilePath . '.pem';

        // Convert PFX to PEM
        $command = [
            'openssl',
            'pkcs12',
            '-in',
            $pfxFilePath . '.pfx',
            '-out',
            $pemFilePath,
            '-nodes',
            '-password',
            'pass:' . $certificatePassword
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $pemFilePath;
    }

    /**
     * @param string $pemFilePath
     * @return array
     */
    private function extractCertificateInfo(string $pemFilePath): array
    {

        $command = [
            'openssl',
            'x509',
            '-in',
            $pemFilePath,
            '-noout',
            '-serial',
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $serial = trim(str_replace('serial=', '', $output));

        return [
            'serial' => $serial
        ];
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer(): SerializerInterface
    {
        // Configure the serializer if needed
        // Here, we use the default serializer with the JSON encoder
        return $this->serializer;
    }
}
