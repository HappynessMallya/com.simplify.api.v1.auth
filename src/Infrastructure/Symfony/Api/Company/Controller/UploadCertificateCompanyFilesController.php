<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\UploadCertificateCompanyFilesCommand;
use App\Application\Company\CommandHandler\UploadCertificateCompanyFilesHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'TIN not valid',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $uploadedFile = $request->files->get('companyFiles');

        if (empty($uploadedFile)) {
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
                $fileMimeType != 'application/pkcs12' && $fileMimeType != 'application/x-pkcs12' &&
                $fileMimeType != 'application/octet-stream'
            ) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'Company files with extension not valid',
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        try {
            $dto = new UploadCertificateCompanyFilesCommand(
                $tin,
                $uploadedFile
            );

            $response = $handler->__invoke($dto);
        } catch (Exception $exception) {
            return $this->createApiResponse(
                [
                    'success' => false
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'response' => $response
            ],
            Response::HTTP_OK
        );
    }
}
