<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Repository\FormRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class SqlController extends AbstractController
{
    #[Route(path: '/diy-form-sql/{id}', name: 'diy-model-sql')]
    public function __invoke(string $id, FormRepository $formRepository, Connection $connection): Response
    {
        $form = $formRepository->findOneBy([
            'id' => $id,
        ]);
        if (null === $form) {
            throw new NotFoundHttpException('找不到模型数据');
        }

        $selectParts = [
            'ce.id',
            'ce.user_id',
            'ce.start_time',
            'ce.finish_time',
        ];
        $sqlLines = [];

        foreach ($form->getSortedFields() as $field) {
            $alias = "v{$field->getId()}";
            $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($field->getTitle());
            $sqlLines[] = "LEFT JOIN diy_form_data AS {$alias} ON (ce.id = {$alias}.record_id AND {$alias}.field_id = '{$field->getId()}')";
            $selectParts[] = "{$alias}.input AS {$name}";
        }

        $selectParts = implode(', ', $selectParts);
        array_unshift($sqlLines, "SELECT {$selectParts} FROM diy_form_record AS ce");

        $sqlLines[] = "WHERE ce.form_id = '{$form->getId()}'";

        return new Response(trim(implode("\n", $sqlLines)));
    }
}
