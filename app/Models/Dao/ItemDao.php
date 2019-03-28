<?php
namespace App\Models\Dao;

use App\Constants\ItemID;
use App\Models\Entity\Item;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;

/**
 *
 * @Bean()
 */
class ItemDao
{

    public function isValidDbItem($itemId)
    {
        return in_array($itemId, ItemID::getConstantsValues());
    }

    public function filterDbItemArr(array $rawItems)
    {
        return array_intersect($rawItems, ItemID::getConstantsValues());
    }

    public function updateNumber($uid, $items)
    {
        $itemIds = array_keys($items);
        $itemIds = $this->filterDbItemArr($itemIds);

        if (!$itemIds) {
            return;
        }


        $itemsData =  Item::findAll(['uid' =>$uid, 'item' => $itemIds])->getResult();

        $now = time();
        // 更新数据库已有数据
        if (!empty($itemsData)) {
            foreach ($itemsData as $k => $e) {
                $number = $e->getNumber() + $items[$e->getItem()];
                if ($number < 0) {
                    $number = 0;
                }
                $e->setNumber($number);
                $e->setUpdatedAt($now);
                $e->update();
                unset($items[$e->getItem()]);
            }
        }
        // 新建数据库没有的数据
        if (!empty($items)) {
            $bulkArr = [];
            foreach ($items as $itemId => $number) {
                if (!$this->isValidDbItem($itemId)) {
                    continue;
                }

                if ($number < 0) {
                    $number = 0;
                }

                $bulkArr[] = [
                    'uid' => $uid,
                    'item' => $itemId,
                    'number' => $number,
                    'created_at' => time(),
                    'updated_at' => time()
                ];
            }

            if (empty($bulkArr)) {
                return;
            }
            Query::table(Item::class)->batchInsert($bulkArr)->getResult();


        }
    }

    public function findItemsByUid($uid)
    {
        return Utils::formatArrayValue(Query::table(Item::class)->condition(['uid' =>$uid])->get(['item', 'number'])->getResult());
    }

    public function findAllByUid($uid):array
    {
        return Utils::formatArrayValue(Query::table(Item::class)->condition(['uid' =>$uid])->get()->getResult());
    }
}