<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Enum\FieldType;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractCrudController<Field>
 */
#[AdminCrud(routePath: '/diy-form/field', routeName: 'diy_form_field')]
final class DiyFormFieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Field::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('字段')
            ->setEntityLabelInPlural('字段管理')
            ->setPageTitle('index', '字段列表')
            ->setPageTitle('new', '新建字段')
            ->setPageTitle('edit', '编辑字段')
            ->setPageTitle('detail', '字段详情')
            ->setHelp('index', '管理表单字段配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'sn', 'title', 'description'])
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

        yield TextField::new('sn', '序列号')
            ->setMaxLength(120)
            ->setRequired(true)
            ->setHelp('字段的唯一标识符')
        ;

        yield ChoiceField::new('type', '类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => FieldType::class])
            ->formatValue(function ($value) {
                return $value instanceof FieldType ? $value->getLabel() : '';
            })
            ->setRequired(true)
        ;

        yield TextField::new('title', '标题')
            ->setMaxLength(255)
            ->setRequired(true)
        ;

        yield TextField::new('placeholder', '提示文本')
            ->setMaxLength(255)
            ->hideOnIndex()
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越大越靠前')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用此字段')
        ;

        yield BooleanField::new('required', '必填')
            ->setHelp('是否为必填字段')
            ->hideOnIndex()
        ;

        yield IntegerField::new('maxInput', '最大输入/选择')
            ->setHelp('限制输入或选择的最大数量')
            ->hideOnIndex()
        ;

        yield TextareaField::new('description', '描述')
            ->setMaxLength(65535)
            ->hideOnIndex()
        ;

        yield TextField::new('bgImage', '背景图')
            ->setMaxLength(255)
            ->hideOnIndex()
        ;

        yield TextareaField::new('showExpression', '显示规则')
            ->setMaxLength(65535)
            ->setHelp('控制字段显示的条件表达式')
            ->hideOnIndex()
        ;

        yield TextareaField::new('extra', '额外信息')
            ->setMaxLength(65535)
            ->setHelp('JSON格式的额外配置信息')
            ->hideOnIndex()
        ;

        yield AssociationField::new('options', '选项')
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
        $choices = [];
        foreach (FieldType::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('form', '所属表单'))
            ->add(TextFilter::new('sn', '序列号'))
            ->add(TextFilter::new('title', '标题'))
            ->add(ChoiceFilter::new('type', '类型')->setChoices($choices))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(BooleanFilter::new('required', '必填'))
        ;
    }
}
