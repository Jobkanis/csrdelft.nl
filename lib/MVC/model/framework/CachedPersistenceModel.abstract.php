<?php

require_once 'MVC/model/framework/PersistenceModel.abstract.php';
require_once 'MVC/model/CsrMemcache.singleton.php';

/**
 * CachedPersistenceModel.abstract.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Uses runtime cache and Memcache to provide caching on top of PersistenceModel.
 * Lazy loading: request-multiple-retrieve-once entities from foreign key relations.
 * Prefetch: grouping queries of foreign key relations beforehand.
 * 
 * N.B. modifying objects in the cache affects every reference to it!
 * 
 */
abstract class CachedPersistenceModel extends PersistenceModel {

	private $runtime_cache = array();

	/**
	 * Calculate key for caching.
	 * 
	 * @param array $primary_key_values
	 * @return int
	 */
	protected function cacheKey(array $primary_key_values) {
		return get_called_class() . crc32(implode('-', $primary_key_values));
	}

	protected function isCached($key, $memcache = false) {
		if (isset($this->runtime_cache[$key])) {
			return true;
		} elseif ($memcache AND CsrMemcache::isAvailable()) {
			// exists without retrieval
			if (CsrMemcache::instance()->add($key, '')) {
				CsrMemcache::instance()->delete($key);
				return false;
			}
			return true;
		}
		return false;
	}

	protected function getCached($key, $memcache = false) {
		if (array_key_exists($key, $this->runtime_cache)) {
			return $this->runtime_cache[$key];
		} elseif ($memcache AND CsrMemcache::isAvailable()) {
			$cache = CsrMemcache::instance()->get($key);
			if ($cache !== false) {
				$value = unserialize($cache);
				// unserialize once 
				$this->setCache($key, $value, false);
				return $value;
			}
		}
	}

	protected function setCache($key, $value, $memcache = false) {
		$this->runtime_cache[$key] = $value;
		if ($memcache AND CsrMemcache::isAvailable()) {
			CsrMemcache::instance()->set($key, serialize($value));
		}
	}

	protected function unsetCache($key, $memcache = false) {
		unset($this->runtime_cache[$key]);
		if ($memcache AND CsrMemcache::isAvailable()) {
			CsrMemcache::instance()->delete($key);
		}
	}

	/**
	 * Flushing memcache should not be necessary!
	 * 
	 * @param boolean $memcache This can be used to partially clear memcache.
	 */
	protected function flushCache($memcache = false) {
		if ($memcache AND CsrMemcache::isAvailable()) {
			// this is obviously not complete at all
			foreach ($this->runtime_cache as $key => $value) {
				CsrMemcache::instance()->delete($key);
			}
		}
		$this->runtime_cache = array();
	}

	/**
	 * Cache entire resultset from a PDOStatement.
	 * Optional: put in memcache.
	 * 
	 * @param string $resultKey
	 * @param PDOStatement $result
	 * @param boolean $memcache_result
	 * @param boolean $memcache_individual
	 */
	protected function cacheResult($resultKey, PDOStatement $result, $memcache_result = false, $memcache_individual = false) {
		$cache = array();
		foreach ($result as $item) {
			$key = $this->cacheKey($item->getValues(true));
			// do NOT update (requires explicit unsetCache)
			if ($this->isCached($key, $memcache_individual)) {
				$cache[] = $this->getCached($key, $memcache_individual);
			} else {
				$this->setCache($key, $item, $memcache_individual);
				$cache[] = $item;
			}
		}
		$this->setCache($resultKey, $cache, $memcache_result);
	}

	/**
	 * Find and cache existing entities with optional search criteria.
	 * Retrieves all attributes.
	 * 
	 * @param string $criteria WHERE
	 * @param array $criteria_params optional named parameters
	 * @param string $orderby
	 * @param int $limit max amount of results
	 * @param int $start results from index
	 * @return array
	 */
	public function find($criteria = null, array $criteria_params = array(), $orderby = null, $groupby = null, $limit = null, $start = 0) {
		$key = 'f' . $this->cacheKey(array($criteria, implode('+', $criteria_params), $orderby, $groupby, $limit, $start));
		if (!$this->isCached($key)) {
			$result = parent::find($criteria, $criteria_params, $orderby, $groupby, $limit, $start);
			$this->cacheResult($key, $result);
		}
		return $this->getCached($key);
	}

	/**
	 * Find and cache existing entities with optional search criteria.
	 * Retrieves only requested attributes and the primary key values.
	 * 
	 * @param array $attributes to retrieve
	 * @param string $criteria WHERE
	 * @param array $criteria_params optional named parameters
	 * @param string $orderby
	 * @param int $limit max amount of results
	 * @param int $start results from index
	 * @return array
	 */
	public function findSparse(array $attributes, $criteria = null, array $criteria_params = array(), $orderby = null, $groupby = null, $limit = null, $start = 0) {
		$key = 'f' . $this->cacheKey(array($criteria, implode('+', $criteria_params), $orderby, $groupby, $limit, $start));
		if (!$this->isCached($key)) {
			$result = parent::findSparse($attributes, $criteria, $criteria_params, $orderby, $groupby, $limit, $start);
			$this->cacheResult($key, $result);
		}
		return $this->getCached($key);
	}

	/**
	 * Check if enitity with primary key exists.
	 * 
	 * @param array $primary_key_values
	 * @return boolean primary key exists
	 */
	protected function existsByPrimaryKey(array $primary_key_values) {
		$key = $this->cacheKey($primary_key_values);
		if ($this->isCached($key)) {
			return true;
		} else {
			return parent::existsByPrimaryKey($primary_key_values);
		}
	}

	/**
	 * Load saved entity data and create new object.
	 * 
	 * @param array $primary_key_values
	 * @return PersistentEntity|false
	 */
	protected function retrieveByPrimaryKey(array $primary_key_values) {
		$key = $this->cacheKey($primary_key_values);
		if ($this->isCached($key)) {
			return $this->getCached($key);
		} else {
			return parent::retrieveByPrimaryKey($primary_key_values);
		}
	}

	/**
	 * Requires positional values.
	 * 
	 * @param array $primary_key_values
	 * @return boolean rows affected
	 */
	protected function deleteByPrimaryKey(array $primary_key_values) {
		$this->unsetCache($this->cacheKey($primary_key_values));
		return parent::deleteByPrimaryKey($primary_key_values);
	}

}
