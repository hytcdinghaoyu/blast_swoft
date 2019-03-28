<?php

namespace App\Models\Dao;

use App\Models\Entity\CenterBulletinLanguage;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;

/**
 *
 * @Bean()
 */
class CenterBulletinLanguageDao
{
    public function getAllByBulletinId($bulletinId)
    {
        $all = Utils::formatArrayValue(Query::table(CenterBulletinLanguage::class)->condition(['is_actived' => 1, 'bulletin_board_id' => $bulletinId])->get()->getResult());
        if (empty($all)) {
            return [];
        }
        return $all;
    }
}