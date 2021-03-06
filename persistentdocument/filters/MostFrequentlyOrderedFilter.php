<?php
class order_MostFrequentlyOrderedFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('maxcount', 'Integer');
		$info->setLabelKey('&modules.order.bo.documentfilters.parameter.max-count;');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$this->setParameter('maxcount', $parameter);
	}
	
	/**
	 * @return String
	 */
	public static function getDocumentModelName()
	{
		return 'modules_catalog/product';
	}
	
	/**
	 * @param array $b
	 * @param array $a
	 * @return integer
	 */
	public function sortProducts($b, $a)
	{
		return $a['count'] > $b['count'] ? 1 : ($a['count'] === $b['count'] ? 0 : -1);
	}
	
	/**
	 * @param array $a
	 * @return integer
	 */
	public function extractId($a)
	{
		return $a['productId'];
	}
	
	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
		$query = order_OrderlineService::getInstance()->createQuery();
		$query->setProjection(Projections::groupProperty('productId'), Projections::rowCount('count'));
		$rows = $query->find();
		
		if (count($rows) === 0)
		{
			return new filter_StaticQuery($this->getDocumentModelName(), array());
		}
		
		usort($rows, array($this, "sortProducts"));
		$rows = array_slice($rows, 0, $this->getParameter('maxcount')->getValueForQuery());
		$rows = array_map(array($this, "extractId"), $rows);
		
		return catalog_ProductService::getInstance()->createQuery()->add(Restrictions::in('id', $rows));
	}
}