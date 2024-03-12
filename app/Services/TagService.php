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

    public function createParcelTags($parcel, $tags)
    {
        $id_tags = [];
        foreach($tags as $tag) {
            $id_tags[] = $tag->id;
        }

        $parcel->tags()->sync($id_tags);
    }

    public function createAccountSchedulingTag($account_scheduling, $tags)
    {
        $unique_tags = $this->getUniqueTags($tags);
        $existents_tags = [];
        $no_existents_tags = [];

        foreach ($unique_tags as $tag) {
            $exists_tag = $this->findTagByName($account_scheduling, $tag["name"]);
            if ($exists_tag) {
                $existents_tags[] = $exists_tag->id;
            } else {
                $no_existents_tags[] = $tag;
            }
        }

        $account_scheduling->tags()->sync($existents_tags);

        foreach($no_existents_tags as $new_tag) {
            $account_scheduling->tags()->create($new_tag);
        }
    }

    public function createAccountEntryTag($entry, $tags)
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

    public function getTags(array $tags = [])
    {
        if (empty($tags)) {
            return Tag::get();
        }

        return Tag::whereIn("name", $tags)->get();
    }

    public function getTagsIds(array $tags = [])
    {
        $newTags = $this->getTags($tags)->toArray();
        $newTags = array_filter($newTags, function($tag) use ($tags) {
            return in_array($tag["name"], $tags);
        });
        
        return array_map(function($tags) {
            return $tags["id"];
        }, $newTags);
    }
}