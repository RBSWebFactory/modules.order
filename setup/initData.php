<?php
/**
 * @package modules.order
 */
class order_Setup extends object_InitDataSetup
{
	public function install()
	{
		$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
		$task->setSystemtaskclassname('order_BackgroundOrderCheck');
		$task->setUniqueExecutiondate(date_Calendar::getInstance());
		$task->setLabel('order_BackgroundOrderCheck');
		$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'order'));
		
		$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
		$task->setSystemtaskclassname('order_BackgroundPDFBillGenerator');
		$task->setMinute(-1);
		$task->setLabel('order_BackgroundPDFBillGenerator');
		$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'order'));
		
		$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
		$task->setSystemtaskclassname('order_BackgroundCommentReminder');
		$task->setMinute(-1);
		$task->setLabel('order_BackgroundCommentReminder');
		$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'order'));
		
		$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
		$task->setSystemtaskclassname('order_GenerateNumbers');
		$task->setLabel('m.order.bo.general.task-generate-numbers');
		$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'order'));
		
		$this->executeModuleScript('init.xml');
		
		$mbs = uixul_ModuleBindingService::getInstance();
		$mbs->addImportInPerspective('catalog', 'order', 'catalog.perspective');
		$mbs->addImportInActions('catalog', 'order', 'catalog.actions');
		$result = $mbs->addImportform('catalog', 'modules_order/fees');
		if ($result['action'] == 'create')
		{
			uixul_DocumentEditorService::getInstance()->compileEditorsConfig();
		}
		change_PermissionService::getInstance()->addImportInRight('catalog', 'order', 'catalog.rights');
	}
	
	/**
	 * @var string
	 */
	private $resourceDir = null;
	
	/**
	 * @param string $relativePath
	 * @return string absolute path
	 */
	protected final function getResourcePath($relativePath)
	{
		if (is_null($this->resourceDir))
		{
			$class = new ReflectionClass($this);
			$base = realpath(dirname($class->getFileName()));
			while (!is_dir(realpath($base . DIRECTORY_SEPARATOR . 'resources')))
			{
				$base = realpath(dirname($base));
				if ($base == '/')
				{
					throw new Exception("Could not find resources folder");
				}
			}
			$this->resourceDir = $base . DIRECTORY_SEPARATOR . 'resources';
		}
		return realpath($this->resourceDir . DIRECTORY_SEPARATOR . $relativePath);
	}
}
