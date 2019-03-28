<?php
namespace App\Tlog;

class LoginLog extends TLog
{

    protected $primary = 'PlayerLogin';


    /**
     * (必填)等级
     * @var int
     */
    public $Level = 0;

    /**
     * (必填)玩家好友数量
     * @var int
     */
    public $PlayerFriendsNum = 0;

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
     * (必填)登录渠道
     * @var string
     */
    public $LoginChannel = 0;


    /**
     * (必填)玩家角色ID
     * @var int
     */
    public $vRoleID = 'NULL';


    /**
     *(必填)玩家角色名
     * @var string
     */
    public $vRoleName = 'NULL';

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

    //这里剩余XX有三种命名方式，20180626 第一totalXX，第二XX，第三countXX -_-
    /**
     * (必填)剩余星星数
     * @var int
     */
    public $totalStar = 0;

    /**
     * (必填)剩余金币数
     * @var int
     */
    public $Gold = 0;

    /**
     * (必填)剩余银币数
     * @var int
     */
    public $Silver = 0;


    /**
     * (必填)剩余关前炸弹道具数
     * @var int
     */
    public $CountBoom = 0;

    /**
     * (必填)剩余关前加三步道具数
     * @var int
     */
    public $CountThreeStep = 0;

    /**
     * (必填)剩余弹弓道具数
     * @var int
     */
    public $CountSingle = 0;

    /**
     * (必填)剩余横向火箭道具数
     * @var int
     */
    public $CountRow = 0;

    /**
     * (必填)剩余竖向火箭道具数
     * @var int
     */
    public $CountColumn = 0;

    /**
     * (必填)剩余置换道具数
     * @var int
     */
    public $CountRefresh = 0;

    /**
     * (必填)剩余镭射枪道具数
     * @var int
     */
    public $CountColor = 0;

    /**
     * (必填)繁荣度
     * @var int
     */
    public $Prosperity = 0;
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'Level',
                'PlayerFriendsNum',
                'ClientVersion',
                'SystemSoftware',
                'SystemHardware',
                'TelecomOper',
                'Network',
                'ScreenWidth',
                'ScreenHight',
                'Density',
                'LoginChannel',
                'vRoleID',
                'vRoleName',
                'CpuHardware',
                'Memory',
                'GLRender',
                'GLVersion'
            ],
            'tail' => array_merge(parent::scenarios()['tail'], [
                'totalStar',
                'Gold',
                'Silver',
                'CountBoom',
                'CountThreeStep',
                'CountSingle',
                'CountRow',
                'CountColumn',
                'CountRefresh',
                'CountColor' ,
                'Prosperity'
            ])
        ];
    }
    
    
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
        $this->LoginChannel = $originLog['channel'] ?? $this->LoginChannel;
        $this->Level = $originLog['topLevel'] ?? $this->Level;
    }
}