<?php
App::uses('HtmlHelper', 'View/Helper');
class HtmlBootstrapHelper extends HtmlHelper {
	
	private static $accordionCount = 0;
	
	public $helpers = array('Form');
	
	public function dropdown($items, $options = array()) {
		
		$output = '<ul class="dropdown-menu">';
		foreach ($items as $itemLabel => $itemOptions) {

			$defaultsLink = array(
				'link' => '#',
				'tabindex' => -1,
				'postLink' => false
			);
			
			if (is_array($itemOptions)) {
				$linkLabel = $itemLabel;
				$linkOptions = array_merge($defaultsLink, $itemOptions);
			} else {
				$linkLabel = $itemOptions;
				$linkOptions = $defaultsLink;
			}
			
			$linkUrl = $linkOptions['link'];
			unset($linkOptions['link']);
			
			$isPostLink = $linkOptions['postLink'];
			unset($linkOptions['postLink']);
			
			if ($isPostLink) {
				$output .= '<li>'.$this->Form->postLink($linkLabel, $linkUrl, $linkOptions).'</li>';
			} else {
				$output .= '<li>'.$this->link($linkLabel, $linkUrl, $linkOptions).'</li>';	
			}
		}
		$output .= '</ul>';
		
		return $output;
	}
	
	public function button($title, $options = array())
	{
		$options += array(
			'size' => null,
			'type' => null,
			'link' => null,
			'block' => false,
			'disabled' => false,
			'tag' => 'div',
			'class' => 'btn'
		);
		
		$useTag = $options['tag'];
		unset($options['tag']);
		
		if ($options['type'])
			$options['class'] .= ' btn-'.$options['type'];
			
		if ($options['size'])
			$options['class'] .= ' btn-'.$options['size'];
			
		if ($options['block'])
			$options['class'] .= ' btn-block';
			
		if ($options['disabled'] === true)
			$options['class'] .= ' disabled';
		
		return $this->tag($useTag, __($title), $options);
	}
	
	public function buttonGroup($buttons, $options = array())
	{
		if (!is_array($buttons))
			return;
		
		$options += array(
			'vertical' => false
		);
		
		$output = '';
		
		foreach ($buttons as $buttonData) {
		
			if (is_string($buttonData)) {
				$output .= $buttonData;	
			} else {
				$output .= $this->button($buttonData);
			}
			
		}
		
		$classes = array(
			'btn-group'
		);
		
		if ($options['vertical'] === true)
			$classes[] = 'btn-group-vertical';
		
		return $this->tag('div', $output, array('class' => $classes));
	}
	
	public function buttonDropdown($title, $items, $options = array())
	{
		$options += array(
			'button' => array(
				'class' => '',
				'data-toggle' => ''
			),
			'dropdown' => array()
		);
		
		$options['button']['tag'] = 'a';
		
		$options['button']['class'] = trim('btn dropdown-toggle '.$options['button']['class']);
		$options['button']['data-toggle'] = trim('dropdown ' . $options['button']['data-toggle']);
		
		$button = $this->button($title, $options['button']);
		// Weird thing to add caret
		$button = str_replace('</a>', '&nbsp;<span class="caret"></span></a>', $button);
		
		$dropdown = $this->dropdown($items, $options['dropdown']);
		
		return $this->buttonGroup(array(
			$button,
			$dropdown
		));
	}
	
	public function label($title, $options = array())
	{
		$options += array(
		);
		
		$options['class'] = 'label';
		if (!empty($options['type']))
			$options['class'] .= ' label-'.$options['type'];
			
		return $this->tag('span', $title, $options);
	}
	
	public function badge($title, $options = array())
	{
		$options += array(
		);
		
		$options['class'] = 'badge';
		if (!empty($options['type']))
			$options['class'] .= ' badge-'.$options['type'];
			
		return $this->tag('span', $title, $options);
	}
	
	public function alert($title, $content, $options = array())
	{
		$options += array(
			'closeButton' => true,
			'block' => false
		);
		
		$options['class'] = 'alert';
		
		if (!empty($options['type'])) {
			$options['class'] .= ' alert-'.$options['type'];
			unset($options['type']);
		}
		
		if ($options['block'] === true) {
			$options['class'] .= ' alert-block';
			$title = $this->tag('h4', $title);
		} else {
			$title = $this->tag('strong', $title);
		}
		
		$alertContent = $title . ' ' . $content;
		
		if ($options['closeButton'] === true) {
			$alertContent = $this->tag('button', '&times;', array('escape' => false, 'type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert' )) . $alertContent;
		}
			
		return $this->tag('div', $alertContent, $options);
	}
	
	public function progress($value, $options = array())
	{
		$options += array(
		);
		
		if (!is_numeric($value))
			$value = 0;
		

		$value = $value < 0 ? 0 : ( $value > 100 ? 100 : $value);
				
		$options['class'] = 'progress';
		
		return $this->tag('div',
			$this->tag('div',
				'',
				array(
					'class' => 'bar',
					'style' => 'width: '.$value.'%'
				)),
			$options);
	}
	
	public function mediaObject($title, $content, $media, $options = array())
	{
		$options += array(
		);
		
		$title = $this->tag('h4', $title, array('class' => 'media-heading'));
		
		$mediaContent = $media . $title . $content;
		
		return $this->tag('div', $mediaContent, array('media'));
		
	}
	
	public function well($content)
	{
		return $this->tag('div', $content, array('class' => 'well'));
	}
	
	public function accordion($collapseData, $options = array())
	{	
		$output = '';
		
		if (!is_array($collapseData))
			return $output;
			
		/*
			{
				'title' => '',
				'body' => '',
				'headingDiv' => array(),
				'bodyDiv' => array()
			}
		*/
		
		$accordionId = 'accordion-'.self::$accordionCount;
		
		$accordionContent = '';
		foreach (array_values($collapseData) as $index => $collapse) {
			
			$accordionGroupId = 'accordion-group-'.$index;
			
			$accordionHeadingOptions = array(
				'class' => 'accordion-heading'
			);
			if (!empty($options['headingDiv']))
				$accordionHeadingOptions = array_merge($accordionHeadingOptions, $options['headingDiv']);
				
			$accordionHeading = $this->tag('div',
				$this->link(
					__($collapse['title']),
					'#'.$accordionGroupId,
					array(
						'class' => 'accordion-toggle',
						'data-toggle' => 'collapse',
						'data-parent' => '#'.$accordionId
					)
				),
				$accordionHeadingOptions
			);
			
			$accordionBodyOptions = array(
				'id' => $accordionGroupId,
				'class' => array(
					'accordion-body',
					'collapse'
				)
			);
			if (!empty($options['bodyDiv']))
				$accordionBodyOptions = array_merge($accordionBodyOptions, $options['bodyDiv']);
			
			$accordionBodyInner = $this->tag('div', $collapse['body'], array('class' => 'accordion-inner'));
			$accordionBody = $this->tag('div', $accordionBodyInner, $accordionBodyOptions);
			
			$accordionGroup = $this->tag('div',
				$accordionHeading . $accordionBody,
				array('class' => 'accordion-group')	
			);
			
			$accordionContent .= $accordionGroup;
		}
		
		return $this->tag('div', $accordionContent, array('class' => 'accordion', 'id' => $accordionId));
	}
	
	public function modal($id, $headerTitle, $bodyContent, $footerContent, $options = array()) {
		$defaultModalOptions = array(
			'id' => $id,
			'class' => 'modal hide fade',
			'tabindex' => -1,
			'role' => 'dialog',
			'aria-labeledby' => $id+'Label',
			'aria-hidden' => 'true',
			'footerButtons' => true
		);
		if (!empty($options['class']))
			$options['class'] = $defaultModalOptions['class'] . ' ' . $options['class'];
		$options = array_merge($defaultModalOptions, $options);
			
		$headerContent = $this->Form->button('&times;', array('class' => 'close', 'data-dismiss' => 'modal', 'aria-hidden' => 'true'));
		$headerContent .= $this->tag('h3', $headerTitle);
		$headerContent = $this->tag('div', $headerContent, array('class' => 'modal-header'));
		$bodyContent = $this->tag('div', $bodyContent, array('class' => 'modal-body'));
		
		if ($options['footerButtons']) {
			$footerContent .= $this->link(__('Close'), '#', array('data-dismiss' => 'modal', 'class' => 'btn'));
			unset($options['footerButtons']);
		}
		$footerContent = $this->tag('div', $footerContent, array('class' => 'modal-footer'));
		
		return $this->tag('div', $headerContent.$bodyContent.$footerContent, $options);
	}
	
	/*public function dropdown($toggle, $items, $options = array()) {
		$defaults = array(
			'data-toggle' => 'dropdown',
			'backupLink' => '#',
			'arrow' => true
		);
		$options = array_merge($defaults, $options);
		
		$backupLink = $options['backupLink'];
		unset($options['backupLink']);
		
		$options['class'] = 'dropdown-toggle';
		
		$toggleLink = $this->link($toggle, $backupLink, $options);
		
		$itemList = '<ul class="dropdown-menu">';
		foreach ($items as $itemLabel => $itemOptions) {

			$defaultsLink = array(
				'link' => '#',
				'tabindex' => -1,
				'postLink' => false
			);
			
			if (is_array($itemOptions)) {
				$linkLabel = $itemLabel;
				$linkOptions = array_merge($defaultsLink, $itemOptions);
			} else {
				$linkLabel = $itemOptions;
				$linkOptions = $defaultsLink;
			}
			
			$linkUrl = $linkOptions['link'];
			unset($linkOptions['link']);
			
			$isPostLink = $linkOptions['postLink'];
			unset($linkOptions['postLink']);
			
			if ($isPostLink) {
				$itemList .= '<li>'.$this->Form->postLink($linkLabel, $linkUrl, $linkOptions).'</li>';
			} else {
				$itemList .= '<li>'.$this->link($linkLabel, $linkUrl, $linkOptions).'</li>';	
			}
		}
		$itemList .= '</ul>';
		
		return $this->tag('div', $toggleLink.$itemList, array('class' => 'dropdown' . ($options['arrow'] ? ' dropdown-arrowed' : '')));
	}*/
	
	public function table($data, $options = array()) {
		
		if (empty($data))
			return;
		
		$defaults = array(
			'class' => 'table',
			'useModel' => false,
			'fields' => false,
			'actions' => array(
				'edit',
				'delete',
				'view'
			)
		);
		$options = array_merge($defaults, $options);
		
		$useModel = $options['useModel'];
		$fields = $options['fields'];
		$actions = $options['actions'];
		
		$table = '';
		
		$thead = '';
		$tbody = '';
		
		$trHead = '';
		
		$rowDataHead = $data[0];
		if ($useModel)
			$rowDataHead = $rowDataHead[$useModel];
		foreach (array_keys($rowDataHead) as $field) {
		
			if (!$fields || ($fields && in_array($field, $fields))) {
			
				$trHead .= $this->tag('th', $field, array('class' => 'field-'.Inflector::slug($field)));
				
			}
			
		}
		$thead .= $this->tag('tr', $trHead);

		foreach ($data as $index => $rowData) {
		
			$tr = '';
			
			if ($useModel)
				$rowData = $rowData[$useModel];
			
			// Add actions
			
			if ($actions) {
			
				$actionDropdown = $this->dropdown(
					'<i class="icon-cog icon-medium"></i>',
					array(
						'Edit' => array(
							'link' => array('action' => 'edit', $rowData['id'])
						),
						'Delete' => array(
							'link' => array('action' => 'delete', $rowData['id']),
							'postLink' => true
						),
						'View' => array(
							'link' => array('action' => 'edit', $rowData['id'])
						)
					),
					array('escape' => false)
				);
				
				$tr .= $this->tag('td', $actionDropdown, array('class' => 'actions-'.Inflector::slug($field), 'style' => 'vertical-align: middle;'));
				
			}
			
			foreach ($rowData as $field => $value) {
			
				if (!$fields || ($fields && in_array($field, $fields))) {
					
					$tr .= $this->tag('td', $value, array('class' => 'field-'.Inflector::slug($field)));
					
				}
				
			}
			$tbody .= $this->tag('tr', $tr, array('class' => 'row-'.$index));
		}
		
		$table .= $this->tag('thead', $thead);
		$table .= $this->tag('tbody', $tbody);
		
		return $this->tag('table', $table, $options);
	}
	
	public function nav(array $items, array $options = array()) {
		
		$defaults = array(
			'class' => 'nav-tabs'
		);
		$options = array_merge($defaults, $options);
		
		$options['class'] = 'nav '+$options['class'];
		
		$itemsHtml = '';
		foreach ($items as $itemKey => $itemValue) {
		
			$itemLabel = $itemValue;
			$itemLink = '';
			
			if (is_string($itemKey)) {
				$itemLabel = $itemKey;
				$itemLink = $itemValue;
			}
			
			$itemsHtml .= $this->tag('li', $this->link($itemLabel, $itemLink));
		}
		
		return $this->tag('ul', $itemsHtml, $options);
		
	}
	
}