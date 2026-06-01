<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'test'        => 'app\common\command\TestCommand',
        'publish:version'        => app\common\command\PublishVersionCommand::class,
        'finance:member:fee'        => 'app\common\command\FinanceMemberFeeCashCommand',
        'turntable:check'        => 'app\common\command\TurntableCheckCommand',
        'set:member:default'        => app\common\command\SetMemberDefaultCommand::class,
        'delete:log'        => 'app\common\command\DeleteCommand',
        'follow:order'        => app\common\command\FollowOrderCommand::class,
        'delete:member:job:log'        => app\common\command\DeleteMemberJobLogCommand::class,
    ],
];
