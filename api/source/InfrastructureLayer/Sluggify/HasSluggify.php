<?php

namespace InfrastructureLayer\Sluggify;

trait HasSluggify
{
    /** slug
     *
     *
     *
     * @var string
     */
    protected $slug;

    /** getSlug
     *
     *  Return url appropriate version
     *
     * @return void
     */
    public function sluggify() {
        $name = $this->name;
        // transliterate
        if (function_exists('iconv'))
        {
            $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);
        }
        // replace ampersands
        $name = str_replace("&", "and", $name);
        // only letters, numbers and hyphens, all lower case
        $this->slug = preg_replace("/[^a-z0-9\-]/i", "", str_replace(" ", "-", strtolower($name)));

        if (isset($this->parent) && $this->parent) {
            $this->slug = $this->parent->getSlug() . "/" . $this->slug;
        }
    }

    /** Slug
     *
     *  Returns the
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }
}
