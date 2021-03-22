<?php
namespace Module\Content\Entity;
use Core\Pattern\Traits\Entity;

class Entry implements \Core\Feature\Entity, \Core\Feature\Export\Text
{
    use Entity;
    
    /**
     * Entity name to export
     * @var string
     */
    const ENTITY_NAME = 'entry';

    /**
     * Date format template
     * @var string
     */
    const DATE_FORMAT = 'd.m.Y H:i';

    /**
     * When entry was created
     * @var \DateTime
     */
    protected \DateTime     $created;

    /**
     * Modified time
     * @var \DateTime|null
     */
    protected ?\DateTime    $modified;

    /**
     * When entry was published,
     * we do not show entry with null publish date
     * @var \DateTime|null
     */
    protected ?\DateTime    $published;

    /**
     * Entry constructor.
     * @param int           $contentID
     * @param string        $title
     * @param string|null   $description
     * @param string        $text
     * @param int           $authorID
     * @param int|null      $parentID
     * @param string|null   $tags
     * @param string|null   $modified
     * @param string        $created
     * @param string|null   $published
     * @throws \Exception
     */
    public function __construct(
        protected int       $contentID,
        protected string    $title,
        protected ?string   $description,
        protected string    $text,
        protected int       $authorID,
        protected ?int      $parentID,
        protected ?string   $tags,
        ?string $modified,
        string $created,
        ?string $published
    )
    {
        $this->created      = new \DateTime($created);
        $this->modified     =  $modified ? new \DateTime($modified) : NULL;
        $this->published    =  $published ? new \DateTime($published) : NULL;
    }
    
    /**
     * Get entry ID
     * @return int
     */
    public function getID():int
    {
        return $this->contentID;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Feature\Export\Attributes::getAttributes()
     */
    public function getAttributes():?array
    {
        return
        [
            'id'            => $this->contentID,
            'author'        => $this->authorID,
            'title'         => $this->title,
            'description'   => $this->description,
            'parent'        => $this->parentID,
            'tags'          => $this->tags,
            'created'       => $this->created->format(self::DATE_FORMAT),
            'modified'      => $this->modified ? $this->modified->format(self::DATE_FORMAT) : NULL,
            'published'     => $this->published ? $this->published->format(self::DATE_FORMAT) : NULL,
        ];
    }

    /**
     * Get text content
     * @return string[]|null
     */
    public function getText(): ?array
    {
        return [ 'text' => $this->text ];
    }
}