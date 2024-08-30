<?php

	/**
	 * Check.class [ HELPER ]
	 * Classe responável por manipular e validar dados do sistema!
	 * @copyright (c) 2014, Marcos Orlando - ZEN AGÊNCIA WEB
	 */
	class Template
	{
		private static $Html;
		private static $Data;

		public static function getTemplate($template, $folder = __DIR__.'/../templates/')
		{
			self::$Html = $folder . $template;

			if (is_file(self::$Html)) {
				self::$Html = file_get_contents(self::$Html);
			}

			return self::$Html;
		}

		public static function setTemplate($template, $arrayData)
		{
			self::$Html = $template;
			self::$Data = $arrayData;

			foreach (self::$Data as $key => $value) {
				self::$Html = str_replace('{'.$key.'}', $value, self::$Html);
			}
			return self::$Html;
		}


	}
