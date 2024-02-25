<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    /**
     * Create the invoice entry
     */
    public function getUniqueTags(array $tags): array
    {
        $new_tags = [];
        $unique_tags = array_unique($tags);

        foreach ($unique_tags as $tag) {
            $new_tags[] = ["name" => $tag];
        }

        return $new_tags;
    }

    /**
     * Creates an invoice entry tags
     */ 
    public function createInvoiceEntryTags($entry, array $tags)
    {
        $unique_tags = $this->getUniqueTags($tags);
        $existents_tags = [];
        $no_existents_tags = [];
        
        foreach ($unique_tags as $tag) {
            $exists_tag = $this->findTagByName($entry, $tag["name"]);
            if ($exists_tag) {
                $existents_tags[] = $exists_tag->id;
            } else {
                $no_existents_tags[] = $tag;
            }
        }

        $entry->tags()->sync($existents_tags);

        foreach($no_existents_tags as $new_tag) {
            $entry->tags()->create($new_tag);
        }
    }

    /**
     * Find tag by name
     */ 
    private function findTagByName($entry, string $name)
    {
        return Tag::where("name", $name)
                ->first();
    }
}