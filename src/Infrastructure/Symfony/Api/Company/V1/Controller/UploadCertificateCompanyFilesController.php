<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\Command\UploadCertificateCompanyFilesCommand;
use App\Application\Company\CommandHandler\UploadCertificateCompanyFilesHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadCertificateCompanyFilesController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class UploadCertificateCompanyFilesController extends BaseController
{
    /**
     * @Route(path="/upload/{tin}", methods={"POST"})
     *
     * @param Request $request
     * @param UploadCertificateCompanyFilesHandler $handler
     * @return JsonResponse
     */
    public function createCompanyAction(
        Request $request,
        UploadCertificateCompanyFilesHandler $handler
    ): JsonResponse {
        $tin = $request->get('tin');

        if (empty($tin)) {
            $this->logger->critical(
                'Invalid TIN',
                [
                    'tin' => $tin,
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Invalid TIN',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $uploadedFile = $request->files->get('companyFiles');

        if (empty($uploadedFile)) {
            $this->logger->critical(
                'Company files must be at least 1',
                [
                    'tin' => $tin,
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Company files must be at least 1',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        foreach ($uploadedFile as $file) {
            $fileMimeType = $file->getClientMimeType();

            if (
                $fileMimeType != 'application/pkcs12' && $fileMimeType != 'application/x-pkcs12'
            ) {
                $this->logger->critical(
                    'Company files with invalid extension',
                    [
                        'tin' => $tin,
                        'mime_type' => $fileMimeType,
                        'method' => __METHOD__,
                    ]
                );

                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'Company files with invalid extension',
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        $dto = new UploadCertificateCompanyFilesCommand(
            $tin,
            $uploadedFile
        );

        try {
            $response = $handler->__invoke($dto);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An error has been occurred when upload certificates of company',
                [
                    'tin' => $tin,
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'An error has been occurred when upload certificates of company' . $exception->getCode(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'response' => $response,
            ],
            Response::HTTP_OK
        );
    }
}
