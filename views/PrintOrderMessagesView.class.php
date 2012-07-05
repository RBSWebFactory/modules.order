<?php
/**
 * order_PrintOrderPropertiesView
 * @package modules.order.views
 */
class order_PrintOrderMessagesView extends change_View
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Order-Action-PrintOrder-Messages');
		$this->includeStyle();
		/* @var $order order_persistentdocument_order */
		$order = $request->getAttribute('order');
		
		$this->setAttribute('order', $order);
		$this->setAttribute('messages', order_MessageService::getInstance()->getByOrder($order));
	}

	protected function includeStyle()
	{
		$ss = StyleService::getInstance();
		$ss->registerStyle('modules.order.printOrder');
		$ss->registerStyle('modules.order.printOrderPrint', StyleService::MEDIA_PRINT);
		$this->setAttribute('cssInclusion', $ss->execute('html'));
	}
}