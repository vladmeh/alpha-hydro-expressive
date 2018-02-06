<?php

namespace Api\Repository;

use Api\Entity\Categories;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;


/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function findByActiveNoDeleted($parentId = 0)
    {
        $categories = $this->findBy(
            [
                'parentId' => $parentId,
                'active' => 1,
                'deleted' => 0,
            ],
            ['sorting' => 'ASC']
        );

        return $categories;
    }

    public function findByNoDeleted($parentId = 0)
    {
        $categories = $this->findBy(
            [
                'parentId' => $parentId,
                'deleted' => 0,
            ],
            ['sorting' => 'ASC']
        );

        return $categories;
    }

    public function findByDeleted($parentId = 0)
    {
        $categories = $this->findBy(
            [
                'parentId' => $parentId,
                'deleted' => 1,
            ],
            ['sorting' => 'ASC']
        );

        return $categories;
    }

    /**
     * @param int $parentId
     * @return array|int
     */
    public function treeCategories($parentId = 0)
    {

        /** @var Collection $categories */
        $categories = $this->findByActiveNoDeleted($parentId);


        $result = [];

        /** @var Categories $category */
        foreach ($categories as $category){
            $children = $category->getChildren();
            if ($children->count() != 0){
                $result[][$category->getName()] = $this->_fetchSubCategoriesInArrayName($children);
            }
        }

        return $result;
    }

    private function _fetchSubCategoriesInArrayName(Collection $categories)
    {
        $result = [];

        foreach ($categories as $category){
            if ($category->getChildren()->count() != 0)
                $result[][$category->getName()] = $this->_fetchSubCategoriesInArrayName($category->getChildren());
        }

        return $result;
    }

}
