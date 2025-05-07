<?php

namespace DiyFormBundle\Controller;

use DiyFormBundle\Repository\FormRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class SqlController extends AbstractController
{
    #[Route('/diy-form-sql/{id}', name: 'diy-model-sql')]
    public function main(string $id, FormRepository $formRepository, Connection $connection): Response
    {
        $form = $formRepository->findOneBy([
            'id' => $id,
        ]);
        if (!$form) {
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
            $name = $connection->getDatabasePlatform()->quoteIdentifier($field->getTitle());
            $sqlLines[] = "LEFT JOIN diy_form_data AS {$alias} ON (ce.id = {$alias}.record_id AND {$alias}.field_id = '{$field->getId()}')";
            $selectParts[] = "{$alias}.input AS {$name}";
        }

        $selectParts = implode(', ', $selectParts);
        array_unshift($sqlLines, "SELECT {$selectParts} FROM diy_form_record AS ce");

        $sqlLines[] = "WHERE ce.form_id = '{$form->getId()}'";

        return new Response(trim(implode("\n", $sqlLines)));
    }
}
