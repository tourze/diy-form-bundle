<?php

declare(strict_types=1);

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<SendLog>
 */
#[AdminCrud(routePath: '/diy-form/send-log', routeName: 'diy_form_send_log')]
final class DiyFormSendLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SendLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SMS日志')
            ->setEntityLabelInPlural('SMS日志管理')
            ->setPageTitle('index', 'SMS日志列表')
            ->setPageTitle('new', '新建SMS日志')
            ->setPageTitle('edit', '编辑SMS日志')
            ->setPageTitle('detail', 'SMS日志详情')
            ->setHelp('index', '管理SMS发送和接收日志')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'batchId', 'mobile', 'memo'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('batchId', '发送批次号')
            ->setMaxLength(100)
            ->setRequired(true)
            ->setHelp('用于标识发送批次')
        ;

        yield TextField::new('zone', '区号')
            ->setMaxLength(6)
            ->setHelp('手机号区号，可选')
            ->hideOnIndex()
        ;

        yield TextField::new('mobile', '手机号码')
            ->setMaxLength(20)
            ->setRequired(true)
            ->setHelp('接收SMS的手机号码')
        ;

        $statusField = EnumField::new('status', '接收状态');
        $statusField->setEnumCases(SmsReceiveEnum::cases());
        $statusField->setHelp('SMS接收状态');
        yield $statusField;

        yield TextField::new('memo', '退回原因')
            ->setMaxLength(100)
            ->setHelp('如果退回，记录退回原因')
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
            ->add(TextFilter::new('batchId', '发送批次号'))
            ->add(TextFilter::new('mobile', '手机号码'))
            ->add(ChoiceFilter::new('status', '接收状态')
                ->setChoices([
                    '已发送' => SmsReceiveEnum::SENT,
                    '已退回' => SmsReceiveEnum::REJECT,
                ])
            )
        ;
    }
}
