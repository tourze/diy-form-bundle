<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Option;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Option>
 */
#[AdminCrud(routePath: '/diy-form/option', routeName: 'diy_form_option')]
final class DiyFormOptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Option::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('选项')
            ->setEntityLabelInPlural('选项管理')
            ->setPageTitle('index', '选项列表')
            ->setPageTitle('new', '新建选项')
            ->setPageTitle('edit', '编辑选项')
            ->setPageTitle('detail', '选项详情')
            ->setHelp('index', '管理字段选项配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'sn', 'text', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('field', '所属字段')
            ->setRequired(true)
            ->autocomplete()
            ->setCrudController(DiyFormFieldCrudController::class)
        ;

        yield TextField::new('sn', '序列号')
            ->setMaxLength(120)
            ->setRequired(true)
            ->setHelp('选项的唯一标识符')
        ;

        yield TextField::new('text', '选项文本')
            ->setMaxLength(1000)
            ->setRequired(true)
        ;

        yield TextField::new('description', '说明文本')
            ->setMaxLength(1000)
            ->hideOnIndex()
        ;

        yield TextField::new('tags', '标签')
            ->setMaxLength(600)
            ->setHelp('多个标签用逗号分隔')
            ->hideOnIndex()
        ;

        yield BooleanField::new('allowInput', '自由输入')
            ->setHelp('是否允许用户自由输入')
            ->hideOnIndex()
        ;

        yield BooleanField::new('answer', '正确答案')
            ->setHelp('是否为正确答案')
            ->hideOnIndex()
        ;

        yield TextField::new('icon', 'ICON')
            ->setMaxLength(255)
            ->hideOnIndex()
        ;

        yield TextField::new('selectedIcon', '选中态ICON')
            ->setMaxLength(255)
            ->hideOnIndex()
        ;

        yield TextField::new('mutex', '互斥分组')
            ->setMaxLength(1000)
            ->setHelp('互斥选项的分组标识')
            ->hideOnIndex()
        ;

        yield TextareaField::new('showExpression', '显示规则')
            ->setMaxLength(65535)
            ->setHelp('控制选项显示的条件表达式')
            ->hideOnIndex()
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
            ->add(EntityFilter::new('field', '所属字段'))
            ->add(TextFilter::new('sn', '序列号'))
            ->add(TextFilter::new('text', '选项文本'))
            ->add(TextFilter::new('tags', '标签'))
            ->add(BooleanFilter::new('allowInput', '自由输入'))
            ->add(BooleanFilter::new('answer', '正确答案'))
        ;
    }
}
