<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Form;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Form>
 */
#[AdminCrud(routePath: '/diy-form/form', routeName: 'diy_form_form')]
final class DiyFormFormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Form::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('表单')
            ->setEntityLabelInPlural('表单管理')
            ->setPageTitle('index', '表单列表')
            ->setPageTitle('new', '新建表单')
            ->setPageTitle('edit', '编辑表单')
            ->setPageTitle('detail', '表单详情')
            ->setHelp('index', '管理自定义表单配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('title', '标题')
            ->setMaxLength(120)
            ->setRequired(true)
            ->setHelp('表单的标题，必须唯一')
        ;

        yield TextareaField::new('description', '描述')
            ->setMaxLength(65535)
            ->hideOnIndex()
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越大越靠前')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用此表单')
        ;

        yield DateTimeField::new('startTime', '开始时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield TextareaField::new('remark', '备注')
            ->setMaxLength(65535)
            ->hideOnIndex()
        ;

        yield AssociationField::new('fields', '字段')
            ->onlyOnDetail()
            ->setTemplatePath('@EasyAdmin/crud/field/association.html.twig')
        ;

        yield AssociationField::new('analyses', '分析规则')
            ->onlyOnDetail()
            ->setTemplatePath('@EasyAdmin/crud/field/association.html.twig')
        ;

        yield AssociationField::new('records', '提交记录')
            ->onlyOnDetail()
            ->setTemplatePath('@EasyAdmin/crud/field/association.html.twig')
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
            ->add(TextFilter::new('title', '标题'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('endTime', '结束时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
