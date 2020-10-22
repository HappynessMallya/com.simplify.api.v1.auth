<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\UpdateCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Form\UpdateCompanyType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class UpdateCompanyController extends BaseController
{
    /**
     * @Route(path="/{companyId}", methods={"PUT"})
     *
     * @param Request $request
     * @param string $companyId
     * @return JsonResponse
     */
    public function UpdateCompanyAction(Request $request, string $companyId)
    {
        $updated = false;
        $command = new UpdateCompanyCommand();
        $form = $this->createForm(UpdateCompanyType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                ['errors' => $this->getValidationErrors($form)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command->setCompanyId($companyId);

        try {
            $updated = $this->commandBus->handle($command);
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                return $this->createApiResponse(['errors' => $e->getMessage()], Response::HTTP_NOT_FOUND);
            }

            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return $this->createApiResponse(['success' => $updated], Response::HTTP_OK);
    }
}