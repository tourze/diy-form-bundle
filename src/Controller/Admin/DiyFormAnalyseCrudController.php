<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Analyse;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Analyse>
 */
#[AdminCrud(routePath: '/diy-form/analyse', routeName: 'diy_form_analyse')]
final class DiyFormAnalyseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Analyse::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('分析规则')
            ->setEntityLabelInPlural('分析规则管理')
            ->setPageTitle('index', '分析规则列表')
            ->setPageTitle('new', '新建分析规则')
            ->setPageTitle('edit', '编辑分析规则')
            ->setPageTitle('detail', '分析规则详情')
            ->setHelp('index', '管理表单结果分析规则')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'category', 'rule'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('form', '所属表单')
            ->setRequired(true)
            ->autocomplete()
            ->setCrudController(DiyFormFormCrudController::class)
        ;

        yield TextField::new('category', '分类')
            ->setMaxLength(200)
            ->setHelp('分析结果的分类')
        ;

        yield TextField::new('title', '标题')
            ->setMaxLength(200)
            ->setRequired(true)
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越大越靠前')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用此分析规则')
        ;

        yield TextField::new('rule', '判断条件')
            ->setMaxLength(2000)
            ->setRequired(true)
            ->setHelp('用于判断的表达式')
            ->hideOnIndex()
        ;

        yield TextareaField::new('result', '结果')
            ->setMaxLength(65535)
            ->setRequired(true)
            ->setHelp('分析结果的内容')
            ->hideOnIndex()
        ;

        yield TextField::new('thumb', '缩略图')
            ->setMaxLength(255)
            ->hideOnIndex()
        ;

        yield TextareaField::new('remark', '备注')
            ->setMaxLength(65535)
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
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('form', '所属表单'))
            ->add(TextFilter::new('title', '标题'))
            ->add(TextFilter::new('category', '分类'))
            ->add(BooleanFilter::new('valid', '有效状态'))
        ;
    }
}
