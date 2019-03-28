<?php
namespace App\Services\Handlers;

interface PropertyHandlerInterface{


    public function handle($itemId, $num);

    public function checkItemId($itemId) : bool;

}