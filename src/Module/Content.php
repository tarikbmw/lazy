<?php
namespace Module;
use Core\Database\MySql\Query;
use Module\Content\Entity\Entry;

/**
 * Class Content
 * Blog demo
 * @package Module
 */
class Content extends \Core\Module
{
    use \Core\Pattern\Traits\Singleton; // Use if for singleton pattern

    /**
     * Module name
     * Use constant instead of setKey() method in constructor
     */
    protected const MODULE_NAME = 'content';

    /**
     * Get content entry by ID
     * @param int $contentID
     * @return Entry
     */
    public function getEntryByID(int $contentID):Entry
    {
        $query = new Query('select * from `content` where contentID = ?');
            $query->id = $contentID;
        return $query->createEmitter('\Module\Content\Entity\Entry')->spawn();
    }

    public function getEntries(int $start = 0, int $limit = 8, ?int $parentID = NULL, string $order = 'contentID asc'):array
    {
        $qryParent  = $parentID ? ' where parentID = ?' : NULL;
        $qryLimit   = $start || $limit ? " limit ?, ?" : NULL;
        $qryOrder   = $order ?  " order by $order" : NULL;

        $query = new Query('select * from `content`'.$qryParent.$qryOrder.$qryLimit);
            if ($parentID)
                $query->parent = $parentID;
            $query->start = $start;
            $query->limit = $limit;
        return $query->createFactory('\Module\Content\Entity\Entry')->spawn();
    }
}