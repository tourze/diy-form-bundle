<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Record;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/**
 * @extends AbstractCrudController<Record>
 */
#[AdminCrud(routePath: '/diy-form/record', routeName: 'diy_form_record')]
final class DiyFormRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Record::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('提交记录')
            ->setEntityLabelInPlural('提交记录管理')
            ->setPageTitle('index', '提交记录列表')
            ->setPageTitle('new', '新建提交记录')
            ->setPageTitle('edit', '编辑提交记录')
            ->setPageTitle('detail', '提交记录详情')
            ->setHelp('index', '管理表单提交记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id'])
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

        yield AssociationField::new('user', '提交用户')
            ->hideOnIndex()
        ;

        yield AssociationField::new('inviter', '邀请人')
            ->hideOnIndex()
        ;

        yield BooleanField::new('finished', '是否完成')
            ->setHelp('表单是否已完成提交')
        ;

        yield DateTimeField::new('startTime', '开始时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('finishTime', '完成时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnIndex()
        ;

        yield IntegerField::new('lockVersion', '乐观锁版本')
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield ArrayField::new('answerTags', '答题标签')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield ArrayField::new('submitData', '原始提交数据')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield ArrayField::new('extraData', '额外信息')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield TextField::new('createdFromUa', '创建时UA')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield TextField::new('updatedFromUa', '更新时UA')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;

        yield AssociationField::new('datas', '提交数据')
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
            ->add(EntityFilter::new('form', '所属表单'))
            ->add(EntityFilter::new('user', '提交用户'))
            ->add(BooleanFilter::new('finished', '是否完成'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('finishTime', '完成时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
