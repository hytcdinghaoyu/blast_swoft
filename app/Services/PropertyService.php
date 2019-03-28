<?php

namespace App\Services;

use App\datalog\AccountFlow;
use App\Services\Handlers\GardenSkinHandler;
use App\Services\Handlers\ItemsHandler;
use App\Services\Handlers\UnlimitedLifeHandler;
use App\Services\Handlers\WalletHandler;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Log\Log;

/**
 * @Bean()
 */
class PropertyService
{
    
    private $handlers = [
        WalletHandler::class,
        ItemsHandler::class,
        GardenSkinHandler::class,
        UnlimitedLifeHandler::class
    ];

    /**
     * 改变所有物数量
     * @param int $itemType
     * @param $itemId
     * @param int $num
     * @param int $reason
     * @param string $subReason
     * @throws \Swoft\Exception\Exception
     */
    public function handleOne($itemId, int $num, int $reason, string $subReason = ''){
        
        $matched = false;
        foreach ($this->handlers as $handlerClass) {
            $handler = App::getBean($handlerClass);
            if(!$handler->checkItemId($itemId)){
                continue;
            }
            
            $matched = true;
            if($handler->handle($itemId, $num)){
                AccountFlow::newFlow($itemId, $num, $reason, $subReason);
            }
        }
        
        if(!$matched){
            Log::error("invalid itemId : $itemId");
        }

    }

    /**
     *
     * 批量处理不同类型的item
     * @param array $items
     * @param int $reason
     * @param string $subReason
     */
    public function handleBatch(array $items, int $reason, string $subReason= ''){

        foreach ($items as $itemId => $num) {
            $matched = false;
            foreach ($this->handlers as $handlerClass) {
                $handler = App::getBean($handlerClass);
                if($handler->checkItemId($itemId)){
                    $matched = true;
                    $handler->handle($itemId, $num);
                    AccountFlow::newFlow($itemId, $num, $reason, $subReason);
                    break;
                }
            }
            if($matched === false){
                Log::error("invalid itemId : $itemId");
            }
        }
    }


}