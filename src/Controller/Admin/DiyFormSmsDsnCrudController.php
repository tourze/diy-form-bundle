<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\SmsDsn;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<SmsDsn>
 */
#[AdminCrud(routePath: '/diy-form/sms-dsn', routeName: 'diy_form_sms_dsn')]
final class DiyFormSmsDsnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsDsn::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SMS配置')
            ->setEntityLabelInPlural('SMS配置管理')
            ->setPageTitle('index', 'SMS配置列表')
            ->setPageTitle('new', '新建SMS配置')
            ->setPageTitle('edit', '编辑SMS配置')
            ->setPageTitle('detail', 'SMS配置详情')
            ->setHelp('index', '管理SMS服务连接配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'dsn'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
        ;

        yield TextField::new('name', '名称')
            ->setMaxLength(100)
            ->setRequired(true)
            ->setHelp('SMS服务配置的名称')
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用此SMS配置')
        ;

        yield IntegerField::new('weight', '权重')
            ->setRequired(true)
            ->setHelp('配置的优先级权重，数值越大优先级越高')
        ;

        yield TextareaField::new('dsn', 'DSN配置')
            ->setMaxLength(65535)
            ->setRequired(true)
            ->setHelp('SMS服务的连接字符串')
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
            ->add(TextFilter::new('name', '名称'))
            ->add(BooleanFilter::new('valid', '有效状态'))
        ;
    }
}
