<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api;

use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BaseController
 * @package App\Infrastructure\Symfony\Controller\Api
 */
class BaseController extends AbstractController
{
    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    protected function createApiResponse($data = null, $statusCode = 200)
    {
        $json = [];

        if (!is_null($data)) {
            $json = $data;
        }

        return new JsonResponse(
            $json,
            $statusCode,
            [
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, GET, PUT, DELETE, PATCH, OPTIONS',
            ]
        );
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return void
     */
    protected function processForm(Request $request, FormInterface $form)
    {
        $data = $this->decodeRequestBody($request);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    /**
     * @param Request $request
     * @param bool $isArray
     * @return array|mixed
     */
    protected function decodeRequestBody(Request $request, bool $isArray = true)
    {
        if (!$request->getContent()) {
            throw new BadRequestHttpException('Invalid request data');
        }

        $data = json_decode($request->getContent(), $isArray);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid request data');
        }

        return $data;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getValidationErrors(FormInterface $form): array
    {
        return $this->getErrors($form->getErrors(true, false));
    }

    /**
     * @param mixed $formErrors
     * @return array
     */
    private function getErrors($formErrors): array
    {
        $errors = [];

        foreach ($formErrors as $error) {
            if (get_class($error) === 'Symfony\\Component\\Form\\FormError') {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            } elseif ($error->hasChildren()) {
                $errors[$error->getForm()->getName()] = $this->getErrors($error);
            } else {
                $errors[$error[0]->getOrigin()->getName()] = $error[0]->getMessage();
            }
        }

        return $errors;
    }
}
