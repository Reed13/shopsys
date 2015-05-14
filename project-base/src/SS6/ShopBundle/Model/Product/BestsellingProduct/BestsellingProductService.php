<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct;

class BestsellingProductService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProductsIndexedByPosition
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProducts
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function combineManualAndAutomaticBestsellingProducts(
		array $manualBestsellingProductsIndexedByPosition,
		array $automaticBestsellingProducts,
		$maxResults
	) {
		$automaticBestsellingProductsWithoutDuplicates = $this->getAutomaticBestsellingProductsExcludingManual(
			$automaticBestsellingProducts,
			$manualBestsellingProductsIndexedByPosition
		);
		$combinedBestsellingProducts = $this->getCombinedBestsellingProducts(
			$manualBestsellingProductsIndexedByPosition,
			$automaticBestsellingProductsWithoutDuplicates,
			$maxResults
		);
		return $combinedBestsellingProducts;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProducts
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProducts
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getAutomaticBestsellingProductsExcludingManual(
		array $automaticBestsellingProducts,
		array $manualBestsellingProducts
	) {
		foreach ($manualBestsellingProducts as $manualBestsellingProduct) {
			$automaticBestsellingProductIndex = array_search($manualBestsellingProduct, $automaticBestsellingProducts, true);
			if ($automaticBestsellingProductIndex !== false) {
				unset($automaticBestsellingProducts[$automaticBestsellingProductIndex]);
			}
		}
		return $automaticBestsellingProducts;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProductsIndexedByPosition
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProductsWithoutDuplicates
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getCombinedBestsellingProducts(
		array $manualBestsellingProductsIndexedByPosition,
		array $automaticBestsellingProductsWithoutDuplicates,
		$maxResults
	) {
		$combinedBestsellingProducts = [];
		for ($position = 0; $position < $maxResults; $position++) {
			if (array_key_exists($position, $manualBestsellingProductsIndexedByPosition)) {
				$combinedBestsellingProducts[] = $manualBestsellingProductsIndexedByPosition[$position];
			} elseif (count($automaticBestsellingProductsWithoutDuplicates) > 0) {
				$combinedBestsellingProducts[] = array_shift($automaticBestsellingProductsWithoutDuplicates);
			}
		}
		return $combinedBestsellingProducts;
	}

}