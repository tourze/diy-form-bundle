<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Entity\SmsDsn;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('自定义表单')) {
            $item->addChild('自定义表单');
        }

        $diyFormMenu = $item->getChild('自定义表单');
        if (null === $diyFormMenu) {
            return;
        }

        $diyFormMenu->addChild('表单管理', [
            'uri' => $this->linkGenerator->getCurdListPage(Form::class),
        ]);

        $diyFormMenu->addChild('字段管理', [
            'uri' => $this->linkGenerator->getCurdListPage(Field::class),
        ]);

        $diyFormMenu->addChild('选项管理', [
            'uri' => $this->linkGenerator->getCurdListPage(Option::class),
        ]);

        $diyFormMenu->addChild('分析规则', [
            'uri' => $this->linkGenerator->getCurdListPage(Analyse::class),
        ]);

        $diyFormMenu->addChild('提交记录', [
            'uri' => $this->linkGenerator->getCurdListPage(Record::class),
        ]);

        $diyFormMenu->addChild('提交数据', [
            'uri' => $this->linkGenerator->getCurdListPage(Data::class),
        ]);

        $diyFormMenu->addChild('SMS日志', [
            'uri' => $this->linkGenerator->getCurdListPage(SendLog::class),
        ]);

        $diyFormMenu->addChild('SMS配置', [
            'uri' => $this->linkGenerator->getCurdListPage(SmsDsn::class),
        ]);
    }
}
