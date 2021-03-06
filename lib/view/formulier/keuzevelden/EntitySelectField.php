<?php

namespace CsrDelft\view\formulier\keuzevelden;

use CsrDelft\common\ContainerFacade;
use CsrDelft\common\CsrException;
use CsrDelft\entity\ISelectEntity;
use CsrDelft\view\formulier\invoervelden\InputField;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 */
class EntitySelectField extends InputField {

	public $size;
	/**
	 * @var ISelectEntity[]
	 */
	protected $options;
	private $groups;
	private $entityType;
	/**
	 * @var ObjectRepository
	 */
	private $repository;
	/**
	 * @var ObjectManager
	 */
	private $entityManager;

	public function __construct($name, $value, $description, $entityType) {
		parent::__construct($name, $value ? $value->getId() : null, $description);

		if (!in_array(ISelectEntity::class, class_implements($entityType))) {
			throw new CsrException($entityType . " implementeerd niet ISelectEntity");
		}

		$this->entityType = $entityType;
		$doctrine = ContainerFacade::getContainer()->get('doctrine');
		$this->repository = $doctrine->getRepository($entityType);
		$this->entityManager = ContainerFacade::getContainer()->get('doctrine.orm.entity_manager');

		$this->options = $this->repository->findAll();
	}

	public function getOptions() {
		return $this->options;
	}

	public function validate() {
		if (!parent::validate()) {
			return false;
		}

		$options = $this->options;
		if (($this->required || $this->getValue() !== null) && !array_key_exists($this->value, $options)) {
			$this->error = 'Onbekende optie gekozen';
		}
		return $this->error === '';
	}

	public function getFormattedValue() {
		$value = $this->getValue();

		if (!$value) {
			return null;
		}

		return $this->entityManager->getReference($this->entityType, $value);
	}

	public function getPreviewDiv() {
		if ($this->groups) {
			return '<div id="selectPreview_' . $this->getId() . '" class="previewDiv"></div>';
		}
		return '';
	}

	public function getJavascript() {
		return parent::getJavascript() . <<<JS

var preview{$this->getId()} = function () {
	var selected = $(':selected', '#{$this->getId()}');
	$('#selectPreview_{$this->getId()}').html(selected.parent().attr('label'));
};
preview{$this->getId()}();
JS;
	}

	public function getHtml($include_hidden = true) {
		$html = '';
		if ($include_hidden) {
			$html .= '<input type="hidden" name="' . $this->name . '" value="" />';
		}
		$html .= '<select name="' . $this->name;
		$html .= '"';
		if ($this->size > 1) {
			$html .= ' size="' . $this->size . '"';
		}
		$html .= $this->getInputAttribute(array('id', 'origvalue', 'class', 'disabled', 'readonly')) . '>';
		$html .= $this->getOptionsHtml($this->options);
		return $html . '</select>';
	}

	/**
	 * @param ISelectEntity[] $options
	 * @return string
	 */
	protected function getOptionsHtml(array $options) {
		$html = '';
		foreach ($options as $description) {
			$html .= '<option value="' . $description->getId() . '"';
			if ($this->value && $description->getId() == $this->value) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . str_replace('&amp;', '&', htmlspecialchars($description->getValue())) . '</option>';
		}
		if ($this->value == null) {
			$html .= "<option hidden disabled selected value=''></option>";
		}
		return $html;
	}

}
