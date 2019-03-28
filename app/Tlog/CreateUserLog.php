<?php
namespace App\Tlog;

class CreateUserLog extends TLog
{

    protected $primary = 'PlayerRegister';

    /**
     * (可选)客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';

    /**
     * (可选)移动终端操作系统版本
     * @var string
     */
    public $SystemSoftware = 'NULL';

    /**
     * (可选)移动终端机型
     * @var string
     */
    public $SystemHardware = 'NULL';

    /**
     * (必填)运营商
     * @var string
     */
    public $TelecomOper = 'NULL';

    /**
     * (可选)3G/WIFI/2G
     * @var string
     */
    public $Network = 'NULL';

    /**
     * (可选)显示屏宽度
     * @var int
     */
    public $ScreenWidth = 0;

    /**
     * (可选)显示屏高度
     * @var int
     */
    public $ScreenHight = 0;

    /**
     * (可选)像素密度
     * @var int
     */
    public $Density = 0;

    /**
     * (必填)注册渠道
     * @var string
     */
    public $channel = 'NULL';

    /**
     * (可选)cpu类型|频率|核数
     * @var string
     */
    public $CpuHardware = 'NULL';

    /**
     * (可选)内存信息单位M
     * @var int
     */
    public $Memory = 0;

    /**
     * (可选)opengl render信息
     * @var string
     */
    public $GLRender = 'NULL';

    /**
     * (可选)opengl版本信息
     * @var string
     */
    public $GLVersion = 'NULL';
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'ClientVersion',
                'SystemSoftware',
                'SystemHardware',
                'TelecomOper',
                'Network',
                'ScreenWidth',
                'ScreenHight',
                'Density',
                'channel',
                'CpuHardware',
                'Memory',
                'GLRender',
                'GLVersion',
            ],
            'tail' => parent::scenarios()['tail']
        ];
    }

}