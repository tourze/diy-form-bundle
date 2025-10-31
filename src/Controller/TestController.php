<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Repository\FormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    /**
     * 获取所有可能的tag.
     *
     * @throws NotFoundHttpException
     */
    #[Route(path: '/diy-form/get-form-tags/{id}', methods: ['GET'])]
    public function __invoke(string $id, FormRepository $formRepository): Response
    {
        $form = $formRepository->find($id);
        if (null === $form) {
            throw new NotFoundHttpException('找不到表单配置');
        }

        $tags = [];
        foreach ($form->getFields() as $field) {
            foreach ($field->getOptions() as $option) {
                $tags = array_merge($tags, $option->getTagList());
            }
        }

        $tags = array_unique($tags);
        $tags = array_values($tags);

        return $this->json($tags);
    }
}
