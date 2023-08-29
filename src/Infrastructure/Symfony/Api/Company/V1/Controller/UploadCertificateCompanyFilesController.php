<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\V1\Command\UploadCertificateCompanyFilesCommand;
use App\Application\Company\V1\CommandHandler\UploadCertificateCompanyFilesHandler;
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
    public function uploadCertificateCompanyFilesAction(
        Request $request,
        UploadCertificateCompanyFilesHandler $handler
    ): JsonResponse {
        $tin = $request->get('tin');
        $serial = $request->get('serial');

        if (empty($tin) | empty($serial)) {
            $this->logger->critical(
                'TIN or Serial missing',
                [
                    'tin' => $tin,
                    'serial' => $serial,
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'TIN or Serial missing',
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
                        'serial' => $serial,
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
            $uploadedFile,
            $serial
        );

        try {
            $response = $handler->__invoke($dto);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An error has been occurred when upload certificates of company',
                [
                    'tin' => $tin,
                    'serial' => $serial,
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
