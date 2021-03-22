<?php 
namespace Core\Pattern\Traits;
use Core\Feature\Export\Attributes;
use Core\Feature\Export\Fragment;
use Core\Feature\Export\Nodes;
use Core\Feature\Export\Text;

/**
 * Entity default trait
 * @author tarik
 */
trait Entity
{
    /**
     * Get entity key
     * You must to set constant ENTITY_NAME
     * @return string
     */
    public function getKey():string
    {
        return self::ENTITY_NAME;
    }

    /**
     * Export Entity to array data
     * @return array
     */
    public function export():array
    {
        $export = [];

        if ($this instanceof Attributes)
            $export[$this->getKey()]['attributes'] = $this->getAttributes();

        if ($this instanceof Nodes)
            $export[$this->getKey()]['nodes'] = $this->getNodes();

        if ($this instanceof Fragment)
            $export[$this->getKey()]['fragment'] = $this->getFragment();

        if ($this instanceof Text)
            $export[$this->getKey()]['text'] = $this->getText();

        return $export;
    }
}
