<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{

  protected static $dbconnection;
  public $categorylist;
  public $categoriesArrayFromDb;

  public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
  {
    $this->entityManager = $entityManager;
    $this->urlGenerator = $urlGenerator;
    $this->categoriesArrayFromDb = $this->getCategories();
  }

  abstract public function getCategoryList(array $categories_array);

  public function buildTree(int $parent_id = NULL): array {
    $subcategory = [];
    foreach ($this->categoriesArrayFromDb as $category) {
      if ($category['parent_id'] == $parent_id) {
        $children = $this->buildTree($category['id']);
        if ($children) {
          $category['children'] = $children;
        }
        $subcategory[] = $category;
      }
    }

    return $subcategory;
  }

  private function getCategories(): array
  {
    if (self::$dbconnection) {
      return self::$dbconnection;
    } else {
      $conn = $this->entityManager->getConnection();
      $sql = "SELECT * FROM categories";
//      $stmt = $conn->prepare($sql);
      $stmt = $conn->executeQuery($sql);
      return self::$dbconnection = $stmt->fetchAllAssociative();
    }
  }
}
