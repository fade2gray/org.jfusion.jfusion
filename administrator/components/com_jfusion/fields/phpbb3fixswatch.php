<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JFormFieldphpBB3FixSwatch extends JFormField
{
	protected $type = 'phpBB3FixSwatch';

	function __construct(){
		setcookie ("JFusion_phpBB3_FixSwatch", "", time() - 3600);
		$doc = JFactory::getDocument();
		$js = <<<JS
			function fixswatch(style) {
				if(style.value != 0){
					document.cookie = "JFusion_phpBB3_FixSwatch" + "=" + escape(style.value);
				}
			}
JS;
		$doc->addScriptDeclaration($js);
	}

	protected function getInput(){
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a regular list.
		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		if (stristr($html[0],' selected="selected"')) {
			$html[0] = str_ireplace(' selected="selected"','',$html[0]);
		}
		if (stristr($html[0],'editor.js">')) {
			$html[0] = str_ireplace('editor.js">','editor.js" onclick="fixswatch(this);">',$html[0]);
		}
		$buttonText = JText::_('FIX_SWATCH');
		$buttonTitle = JText::_('FIX_SELECTED');
		$html[] = <<<HTML
		<div id="{$this->id}" class="button2-left">
				<div class="blank">
						<a title="{$buttonTitle}" href="javascript:void(0);" onclick="return module('fixModSwatch')">{$buttonText}</a>
				</div>
		</div>
HTML;

		return implode($html);
	}

	protected function getOptions(){
		$styleList[0] =  JText::_('STYLE_LIST1');

		$sourcePath=JFusionFactory::getParams('phpbb3')->get('source_path');
		$stylePaths = glob($sourcePath.DS.'styles'.DS.'*', GLOB_ONLYDIR );
		foreach($stylePaths as $stylePath){
			$editorFile = $stylePath.DS.'template'.DS.'editor.js';
			if(file_exists($editorFile)){
				$file_data = JFile::read($editorFile);
				if(stristr($file_data,'"images/spacer.gif"')){
					$cfgData = JFile::read($stylePath.DS.'style.cfg');
					preg_match('#\s*name\s*=.*#i',$cfgData,$styleName);
					$styleName = preg_replace('#\s*name\s*=\s*#i','',$styleName[0]);
					$styleList[$editorFile] = $styleName;
				}
			}
		}

		if(count($styleList) == 1){
			$styleList[0] = JText::_('STYLE_LIST2');
		}

		return $styleList;
	}
}
