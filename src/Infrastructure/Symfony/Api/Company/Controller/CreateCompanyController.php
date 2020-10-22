<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\CreateCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Form\CreateCompanyType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class CreateCompanyController extends BaseController
{
    /**
     * @Route(path="/", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createCompanyAction(Request $request)
    {
        $command = new CreateCompanyCommand();
        $form = $this->createForm(CreateCompanyType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                ['errors' => $this->getValidationErrors($form)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $companyId = $this->commandBus->handle($command);

        if (empty($companyId)) {
            return $this->createApiResponse(['errors' => 'No created'], Response::HTTP_FORBIDDEN);
        }

        return $this->createApiResponse(['company_id' => $companyId], Response::HTTP_CREATED);
    }
}
