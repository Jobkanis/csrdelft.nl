<?php
/**
 * DeleteKnop.class.php
 *
 * @author P.W.G. Brussee <brussee@live.nl>
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 */
class DeleteKnop extends FormulierKnop {

	public function __construct($url, $action = 'post confirm ReloadPage', $label = 'Verwijderen', $title = 'Definitief verwijderen', $icon = 'cross') {
		parent::__construct($url, $action, $label, $title, $icon);
	}

}