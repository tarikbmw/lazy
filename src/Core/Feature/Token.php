<?php
namespace Core\Feature;

/**
 * Trait Token
 * Token generation
 * @package Core\Feature
 */
trait Token
{
    /**
     * UUID generator
     * @param bool $useDelimeter  use delimeter betweeen groups
     * @return string
     */
    public function generateUUID(bool $useDelimeter = false):string
    {
        $format = $useDelimeter ? '%04x%04x-%04x-%04x-%04x-%04x%04x%04x' : '%04x%04x%04x%04x%04x%04x%04x%04x';
        $generator = fn() => mt_rand(0, 0xffff);
        return strtoupper(sprintf($format, $generator(), $generator(), $generator(), $generator() | 0x4000, $generator() | 0x8000, $generator(), $generator(), $generator()));
    }
}