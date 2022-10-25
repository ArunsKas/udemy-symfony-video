<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\AppExtension;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $string, string $slug): void
    {
        $slugger = new AppExtension();
        $this->assertSame($slug, $slugger->slugify($string));
    }

    public function getSlugs(): \Generator
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield [' Lorem  Ipsum ', 'lorem-ipsum'];
        yield ['LOrem  Ipsum ', 'lorem-ipsum'];
        yield [' Lorem  Ipsum! ', 'lorem-ipsum'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield [' Lorem  Ipsum+', 'lorem-ipsum'];
    }
}
