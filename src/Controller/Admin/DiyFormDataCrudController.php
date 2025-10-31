<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Data;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Data>
 */
#[AdminCrud(routePath: '/diy-form/data', routeName: 'diy_form_data')]
final class DiyFormDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Data::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('提交数据')
            ->setEntityLabelInPlural('提交数据管理')
            ->setPageTitle('index', '提交数据列表')
            ->setPageTitle('new', '新建提交数据')
            ->setPageTitle('edit', '编辑提交数据')
            ->setPageTitle('detail', '提交数据详情')
            ->setHelp('index', '管理单个字段的提交数据')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'input'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('record', '所属记录')
            ->setRequired(true)
            ->autocomplete()
            ->setCrudController(DiyFormRecordCrudController::class)
        ;

        yield AssociationField::new('field', '所属字段')
            ->autocomplete()
            ->setCrudController(DiyFormFieldCrudController::class)
        ;

        yield TextareaField::new('input', '输入数据')
            ->setMaxLength(65535)
            ->setRequired(true)
            ->setHelp('用户输入的数据内容')
        ;

        yield BooleanField::new('skip', '是否跳过')
            ->setHelp('是否跳过了此字段')
            ->hideOnIndex()
        ;

        yield BooleanField::new('deletable', '是否可删除')
            ->setHelp('此数据是否可被删除')
            ->hideOnIndex()
        ;

        yield ArrayField::new('answerTags', '回答标签')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // Removed reorder due to EasyAdmin version compatibility
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('record', '所属记录'))
            ->add(EntityFilter::new('field', '所属字段'))
            ->add(TextFilter::new('input', '输入数据'))
            ->add(BooleanFilter::new('skip', '是否跳过'))
            ->add(BooleanFilter::new('deletable', '是否可删除'))
        ;
    }
}
