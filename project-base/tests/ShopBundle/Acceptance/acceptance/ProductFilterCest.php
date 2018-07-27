<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductFilterPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class ProductFilterCest
{
    public function testAllProductFilters(
        AcceptanceTester $me,
        ProductFilterPage $productFilterPage,
        ProductListPage $productListPage
    ) {
        $me->wantTo('test all product filters');
        $me->amOnPage('/tv-audio/');
        $productListPage->assertProductsTotalCount(28);

        $productFilterPage->setMinimalPrice(40);
        $productListPage->assertProductsTotalCount(22);

        $productFilterPage->setMaximalPrice(400);
        $productListPage->assertProductsTotalCount(16);

        $productFilterPage->filterByBrand('LG');
        $productListPage->assertProductsTotalCount(3);

        $productFilterPage->filterByBrand('Hyundai');
        $productListPage->assertProductsTotalCount(7);

        $productFilterPage->filterByParameter('HDMI', 'Yes');
        $productListPage->assertProductsTotalCount(6);

        $productFilterPage->filterByParameter('Screen size', '27"');
        $productListPage->assertProductsTotalCount(2);

        $productFilterPage->filterByParameter('Screen size', '30"');
        $productListPage->assertProductsTotalCount(4);
    }
}
