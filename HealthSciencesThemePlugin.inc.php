<?php

/**
 * @file plugins/themes/healthSciences/HealthSciencesThemePlugin.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HealthSciencesThemePlugin
 * @ingroup plugins_themes_healthSciences
 *
 * @brief Health Sciences theme
 */

import('lib.pkp.classes.plugins.ThemePlugin');
class HealthSciencesThemePlugin extends ThemePlugin {

	/**
	 * Load the custom styles for our theme
	 * @return null
	 */
	public function init() {

		// Add theme options
		$this->addOption('baseColour', 'colour', array(
			'label' => 'plugins.themes.healthSciences.option.colour.label',
			'description' => 'plugins.themes.healthSciences.option.colour.description',
			'default' => '#10BECA',
		));

		// Update colour based on theme option
		$additionalLessVariables = [];
		if ($this->getOption('baseColour') !== '#10BECA') {
			$additionalLessVariables[] = '@primary:' . $this->getOption('baseColour') . ';';
			if (!$this->isColourDark($this->getOption('baseColour'))) {
				$additionalLessVariables[] = '@primary-light: desaturate(lighten(@primary, 41%), 15%);';
				$additionalLessVariables[] = '@primary-text: darken(@primary, 15%);';
			}
		}

		// Load dependencies from CDN
		if (Config::getVar('general', 'enable_cdn')) {
			$this->addStyle(
				'fonts',
				'https://fonts.googleapis.com/css?family=Droid+Serif:200,200i,400,400i|Fira+Sans:300,300i,400,400i,700,700i',
				array('baseUrl' => '')
			);
			$this->addStyle(
				'bootstrap',
				'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css',
				array('baseUrl' => '')
			);
			$this->addScript(
				'jquery',
				'https://code.jquery.com/jquery-3.2.1.min.js',
				array('baseUrl' => '')
			);
			$this->addScript(
				'popper',
				'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js',
				array('baseUrl' => '')
			);
			$this->addScript(
				'bootstrap',
				'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js',
				array('baseUrl' => '')
			);

		// Load local copies of dependencies if CDNs are not allowed
		} else {
			$this->addStyle('bootstrap', 'libs/bootstrap.min.css');
			$this->addScript('jquery', 'libs/jquery-3.2.1.slim.min.js');
			$this->addScript('popper', 'libs/popper.min.js');
			$this->addScript('bootstrap', 'libs/bootstrap.min.js');
		}

		// Load theme stylesheet and script
		$this->addStyle('stylesheet', 'styles/index.less');
		$this->modifyStyle('stylesheet', array('addLessVariables' => join($additionalLessVariables)));
		$this->addScript('main', 'js/main.js');

		// Add navigation menu areas for this theme
		$this->addMenuArea(array('primary', 'user'));

		// Get extra data for templates
		HookRegistry::register ('TemplateManager::display', array($this, 'loadTemplateData'));
	}

	/**
	 * Get the display name of this theme
	 * @return string
	 */
	public function getDisplayName() {
			return __('plugins.themes.healthSciences.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	public function getDescription() {
			return __('plugins.themes.healthSciences.description');
	}

	/**
	 * Load custom data for templates
	 *
	 * @param string $hookName
	 * @param array $args [
	 *		@option TemplateManager
	 *		@option string Template file requested
	 *		@option string
	 *		@option string
	 *		@option string output HTML
	 * ]
	 */
	public function loadTemplateData($hookName, $args) {
		$templateMgr = $args[0];
		$request = Application::getRequest();
		$context = $request->getContext();

		if (!defined('SESSION_DISABLE_INIT')) {

			// Get possible locales
			if ($context) {
				$locales = $context->getSupportedLocaleNames();
			} else {
				$locales = $request->getSite()->getSupportedLocaleNames();
			}

			// Load login form
			$loginUrl = $request->url(null, 'login', 'signIn');
			if (Config::getVar('security', 'force_login_ssl')) {
				$loginUrl = PKPString::regexp_replace('/^http:/', 'https:', $loginUrl);
			}

			$templateMgr->assign(array(
				'languageToggleLocales' => $locales,
				'loginUrl' => $loginUrl,
				'brandImage' => 'templates/images/ojs_brand_white.png',
			));
		}
	}
}
