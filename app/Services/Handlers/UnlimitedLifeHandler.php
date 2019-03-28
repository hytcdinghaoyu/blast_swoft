<?php
namespace App\Services\Handlers;
use App\Models\Dao\TaskDao;
use App\Utils\Config;
use Swoft\Bean\Annotation\Bean;

/**
 * @Bean()
 * Class UnlimitedLifeHandler
 * @package App\Services
 */
class UnlimitedLifeHandler implements PropertyHandlerInterface {


    public function handle($itemId, $num){
        TaskDao::setUnlimitedLife($itemId);
    }

    public function checkItemId($itemId) : bool{
        $unlimited = array_keys(Config::loadJson('unlimitedLifeBuff'));
        if (in_array($itemId, $unlimited)) {
            return true;
        }
        return false;
    }

}