<?php

	class VirtualUserMappingPlugin extends \RainLoop\Plugins\AbstractPlugin
	{
		public function Init()
		{
			$this->addHook('filter.login-credentials', 'FilterLoginСredentials');
		}

		public function FilterLoginСredentials(&$sEmail, &$sLogin, &$sPassword)
		{
			$sMapping = trim($this->Config()->Get('plugin', 'mapping_table', ''));

			if (is_file($sMapping) && is_readable($sMapping)) {
				$sMapping = file_get_contents($sMapping);

				if (empty($sMapping) === false) {
					$aLines = preg_split('/\v+/', $sMapping);

					foreach ($aLines as $sLine) {
						$aData = preg_split('/\h+/', trim($sLine));

						if (isset($aData[0], $aData[1]) && $aData[0] === $sEmail && preg_match('/^[0-9a-z_\.\-]+$/i', $aData[1])) {
							$sLogin = $aData[1];
						}
					}
				}
			}
		}

		public function configMapping()
		{
			return array(
				\RainLoop\Plugins\Property::NewInstance('mapping_table')->SetLabel('Mapping table')
					->SetType(\RainLoop\Enumerations\PluginPropertyType::STRING)
					->SetDescription('Absolute path to the mapping table')
					->SetDefaultValue('/etc/postfix/virtual/addresses')
			);
		}
	}