<?php
namespace Core\Pattern\Traits\Entity;
use Core\Feature\Export\Attributes;
use Core\Feature\Export\Fragment;
use Core\Feature\Export\Nodes;
use Core\Feature\Export\Text;

/**
 * Entity filtering
 * @author tarik
 */
trait Filter
{
    /**
     * Functions by type
     * @var array
     */
    private array $entityFilter = [];

    /**
     * Setup new filter
     * @param string    $type       type
     * @param callable  $filter     function
     */
    public function setFilter(string $type, callable $filter)
    {
        $this->entityFilter[$type] = $filter;
    }

    /**
     * Export entity with filter
     * @return array
     */
    public function export():array
    {
        $export = [];

        if ($this instanceof Attributes)
            $export[$this->getKey()]['attributes'] = is_callable($this->entityFilter['attributes']) ?
                $this->entityFilter['attributes']($this->getAttributes()) : $this->getAttributes();

        if ($this instanceof Nodes)
            $export[$this->getKey()]['nodes'] = is_callable($this->entityFilter['nodes']) ?
                $this->entityFilter['nodes']($this->getNodes()) : $this->getNodes();

        if ($this instanceof Fragment)
            $export[$this->getKey()]['fragment'] = is_callable($this->entityFilter['fragment']) ?
                $this->entityFilter['fragment']($this->getFragment()) : $this->getFragment();

        if ($this instanceof Text)
            $export[$this->getKey()]['text'] = is_callable($this->entityFilter['text']) ?
                $this->entityFilter['text']($this->getText()) : $this->getText();

        return $export;
    }
}