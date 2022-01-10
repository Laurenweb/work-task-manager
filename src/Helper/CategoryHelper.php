<?php

namespace App\Helper;

use App\Entity\Category;

class CategoryHelper {

    private static function compareCategory(Category $a, Category $b) {
        if ($a === $b) {
            return 0;
        }
        if (!$a->getDuration()) {
            return -1;
        }
        if (!$b->getDuration()) {
            return 1;
        }
        return $b->getDuration() - $a->getDuration();
    }


    public static function sortCategoryByUser(array $categories): array {
        $categoryByUser = [];
        foreach ($categories as $category) {
            $email = $category->getUser()->getEmail();
            if (!isset($categoryByUser[$email])) {
                $categoryByUser[$email] = [];
            }
            $type = $category->getType();
            if (!isset($categoryByUser[$email][$type])) {
                $categoryByUser[$email][$type] = [];
            }
            $categoryByUser[$email][$type][] = $category;
        }
        foreach ($categoryByUser as $email => $userCategories) {
            usort($categoryByUser[$email]['other'], 'static::compareCategory');
        }
        return $categoryByUser;
    }

}