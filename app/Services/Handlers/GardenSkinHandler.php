<?php
namespace App\Services\Handlers;
use Swoft\Bean\Annotation\Bean;

/**
 * @Bean()
 * Class GardenSkinHandler
 * @package App\Services
 */
class GardenSkinHandler implements PropertyHandlerInterface {


    public function handle($itemId, $num){

    }

    public function checkItemId($itemId) : bool{
        return false;
    }

}