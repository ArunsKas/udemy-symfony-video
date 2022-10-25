<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Twig\AppExtension;

class CategoryTest extends KernelTestCase
{
    protected $mockedCategoryTreeFrontPage;
    protected $mockedCategoryTreeAdminList;
    protected $mockedCategoryTreeAdminOptionList;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator = $kernel->getContainer()->get('router');

        $tested_classes = [
            'CategoryTreeAdminList',
            'CategoryTreeAdminOptionList',
            'CategoryTreeFrontPage'
        ];

        foreach ($tested_classes as $class) {
            $name = 'mocked' . $class;
            $this->$name = $this->getMockBuilder(
                'App\Utils\\' . $class
            )
                ->disableOriginalConstructor()
                ->setMethods()
                ->getMock();

            $this->$name->urlGenerator = $urlGenerator;
        }
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFrontPage($string, $array, $id)
    {
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDb = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppExtension;
        $main_parent_id = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];
        $array = $this->mockedCategoryTreeFrontPage->buildTree($main_parent_id);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     */
    public function testCategoryTreeAdminOptionList($arrayToCompare, $arrayFromDb)
    {
        $this->mockedCategoryTreeAdminOptionList->categoriesArrayFromDb = $arrayFromDb;
        $arrayFromDb = $this->mockedCategoryTreeAdminOptionList->buildTree();
        $this->assertSame(
            $arrayToCompare,
            $this->mockedCategoryTreeAdminOptionList->getCategoryList($arrayFromDb)
        );
    }

    /**
     * @dataProvider dataForCategoryTreeAdminList
     */
    public function testCategoryTreeAdminList($string, $array)
    {
        $this->mockedCategoryTreeAdminList->categoriesArrayFromDb = $array;
        $array = $this->mockedCategoryTreeAdminList->buildTree();
        $this->assertSame($string, $this->mockedCategoryTreeAdminList->getCategoryList($array));
    }

    public function dataForCategoryTreeFrontPage(): \Generator
    {
        yield [
            '<ul><li><a href="/video-list/category/cameras,5">Cameras</a></li><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/asus,10">Asus</a></li><li><a href="/video-list/category/dell,11">Dell</a></li><li><a href="/video-list/category/lenovo,12">Lenovo</a></li></ul></li><li><a href="/video-list/category/desktop,9">Desktop</a></li></ul></li><li><a href="/video-list/category/cell-phones,7">Cell Phones</a></li></ul>',
            [
                ["id" => "1", "parent_id" => null, "name" => "Electronics"],
                ["id" => "2", "parent_id" => null, "name" => "Toys"],
                ["id" => "3", "parent_id" => null, "name" => "Books"],
                ["id" => "4", "parent_id" => null, "name" => "Movies"],
                ["id" => "5", "parent_id" => "1", "name" => "Cameras"],
                ["id" => "6", "parent_id" => "1", "name" => "Computers"],
                ["id" => "7", "parent_id" => "1", "name" => "Cell Phones"],
                ["id" => "8", "parent_id" => "6", "name" => "Laptops"],
                ["id" => "9", "parent_id" => "6", "name" => "Desktop"],
                ["id" => "10", "parent_id" => "8", "name" => "Asus"],
                ["id" => "11", "parent_id" => "8", "name" => "Dell"],
                ["id" => "12", "parent_id" => "8", "name" => "Lenovo"]
            ],
            1
        ];
        yield [
            '<ul></ul>',
            [
                ["id" => "1", "parent_id" => null, "name" => "Electronics"],
                ["id" => "2", "parent_id" => null, "name" => "Toys"],
                ["id" => "3", "parent_id" => null, "name" => "Books"],
                ["id" => "4", "parent_id" => null, "name" => "Movies"],
                ["id" => "5", "parent_id" => "1", "name" => "Cameras"],
                ["id" => "6", "parent_id" => "1", "name" => "Computers"],
                ["id" => "7", "parent_id" => "1", "name" => "Cell Phones"],
                ["id" => "8", "parent_id" => "6", "name" => "Laptops"],
                ["id" => "9", "parent_id" => "6", "name" => "Desktop"],
                ["id" => "10", "parent_id" => "8", "name" => "Asus"],
                ["id" => "11", "parent_id" => "8", "name" => "Dell"],
                ["id" => "12", "parent_id" => "8", "name" => "Lenovo"]
            ],
            2
        ];
    }

    public function dataForCategoryTreeAdminOptionList(): \Generator
    {
        yield [
            [
                ['name' => 'Electronics', 'id' => 1,],
                ['name' => '--Cameras', 'id' => 5,],
                ['name' => '--Computers', 'id' => 6,],
                ['name' => '----Laptops', 'id' => 8,],
                ['name' => '------Asus', 'id' => 10,],
                ['name' => '------Dell', 'id' => 11,],
                ['name' => '------Lenovo', 'id' => 12,],
                ['name' => '----Desktop', 'id' => 9,],
                ['name' => '--Cell Phones', 'id' => 7,],
                ['name' => 'Toys', 'id' => 2,],
                ['name' => 'Books', 'id' => 3,],
                ['name' => 'Movies', 'id' => 4,],
            ],
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null,],
                ['name' => 'Toys', 'id' => 2, 'parent_id' => null,],
                ['name' => 'Books', 'id' => 3, 'parent_id' => null,],
                ['name' => 'Movies', 'id' => 4, 'parent_id' => null,],
                ['name' => 'Cameras', 'id' => 5, 'parent_id' => '1',],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => '1',],
                ['name' => 'Cell Phones', 'id' => 7, 'parent_id' => '1',],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => '6',],
                ['name' => 'Desktop', 'id' => 9, 'parent_id' => '6',],
                ['name' => 'Asus', 'id' => 10, 'parent_id' => '8',],
                ['name' => 'Dell', 'id' => 11, 'parent_id' => '8',],
                ['name' => 'Lenovo', 'id' => 12, 'parent_id' => '8',]
            ]
        ];
    }

    public function dataForCategoryTreeAdminList(): \Generator
    {
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Asus<a href="/admin/su/edit-category/10"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/10">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Dell<a href="/admin/su/edit-category/11"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/11">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Lenovo<a href="/admin/su/edit-category/12"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/12">Delete</a></li></ul>',
            [
                ['name' => 'Asus', 'id' => 10, 'parent_id' => null,],
                ['name' => 'Dell', 'id' => 11, 'parent_id' => null,],
                ['name' => 'Lenovo', 'id' => 12, 'parent_id' => null,]
            ]
        ];
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Books<a href="/admin/su/edit-category/3"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/3">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Movies<a href="/admin/su/edit-category/4"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/4">Delete</a></li></ul>',
            [
                ['name' => 'Toys', 'id' => 2, 'parent_id' => null,],
                ['name' => 'Books', 'id' => 3, 'parent_id' => null,],
                ['name' => 'Movies', 'id' => 4, 'parent_id' => null,],
            ],
        ];
    }
}
