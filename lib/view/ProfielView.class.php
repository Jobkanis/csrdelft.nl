<?php

/**
 * ProfielView.class.php
 * 
 * @author C.S.R. Delft	<pubcie@csrdelft.nl>
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 */
class ProfielView extends SmartyTemplateView {

	function __construct(Profiel $profiel) {
		parent::__construct($profiel, 'Het profiel van ' . $profiel->getNaam());
	}

	public function getBreadcrumbs() {
		return '<a href="/ledenlijst" title="Ledenlijst"><img src="/plaetjes/knopjes/people-16.png" class="module-icon"></a> » <span class="active">' . $this->model->getNaam('civitas') . '</span>';
	}

	function view() {
		$this->smarty->assign('profiel', $this->model);

		$woonoord = $this->model->getWoonoord();
		if ($woonoord instanceof OldGroep) {
			$this->smarty->assign('woonoord', '<strong>' . $woonoord->getLink() . '</strong>');
		} else {
			$this->smarty->assign('woonoord', '');
		}

		require_once 'view/groepen/OldGroepenView.class.php';
		$this->smarty->assign('groepen', new GroepenProfielContent($this->model->uid));

		if (LoginModel::getUid() == $this->model->uid || LoginModel::mag('P_MAAL_MOD')) {

			require_once 'model/maalcie/MaaltijdAanmeldingenModel.class.php';
			$this->smarty->assign('recenteAanmeldingen', MaaltijdAanmeldingenModel::getRecenteAanmeldingenVoorLid($this->model->uid));

			require_once 'model/maalcie/MaaltijdAbonnementenModel.class.php';
			$this->smarty->assign('abos', MaaltijdAbonnementenModel::getAbonnementenVoorLid($this->model->uid));
		}

		require_once 'lid/saldi.class.php';
		if (Saldi::magGrafiekZien($this->model->uid)) {
			$this->smarty->assign('saldografiek', Saldi::getDatapoints($this->model->uid, 60));
		}

		$this->smarty->assign('corveepunten', $this->model->corvee_punten);
		$this->smarty->assign('corveebonus', $this->model->corvee_punten_bonus);

		require_once 'model/maalcie/CorveeTakenModel.class.php';
		$this->smarty->assign('corveetaken', CorveeTakenModel::getTakenVoorLid($this->model->uid));

		require_once 'model/maalcie/CorveeVoorkeurenModel.class.php';
		$this->smarty->assign('corveevoorkeuren', CorveeVoorkeurenModel::getVoorkeurenVoorLid($this->model->uid));

		require_once 'model/maalcie/CorveeVrijstellingenModel.class.php';
		$this->smarty->assign('corveevrijstelling', CorveeVrijstellingenModel::getVrijstelling($this->model->uid));

		require_once 'model/maalcie/KwalificatiesModel.class.php';
		$this->smarty->assign('corveekwalificaties', KwalificatiesModel::instance()->getKwalificatiesVanLid($this->model->uid));

		require_once 'model/ForumModel.class.php';
		$this->smarty->assign('forumpostcount', ForumPostsModel::instance()->getAantalForumPostsVoorLid($this->model->uid));

		require_once 'model/bibliotheek/BiebCatalogus.class.php';
		$this->smarty->assign('boeken', BiebCatalogus::getBoekenByUid($this->model->uid, 'eigendom'));
		$this->smarty->assign('gerecenseerdeboeken', BiebCatalogus::getBoekenByUid($this->model->uid, 'gerecenseerd'));

		$this->smarty->display('profiel/profiel.tpl');
	}

}

class ProfielForm extends Formulier {

	public function getBreadcrumbs() {
		return '<a href="/ledenlijst" title="Ledenlijst"><img src="/plaetjes/knopjes/people-16.png" class="module-icon"></a> » ' . $this->model->getLink('civitas');
	}

	public function __construct(Profiel $profiel) {
		parent::__construct($profiel, 'profielForm', '/profiel/' . $profiel->uid . '/bewerken');

		$isMod = LoginModel::mag('P_LEDEN_MOD');
		$editNoviet = $profiel->status === LidStatus::Noviet;

		if ($editNoviet) {
			$this->titel = 'Welkom bij C.S.R.!';

			$fields[] = new HtmlComment('<p>
				Hieronder mag je gegevens invullen in het databeest van de Civitas. Zo kunnen we contact met je houden,
				kunnen andere leden opzoeken waar je woont en kun je (na het novitiaat) op het forum berichten plaatsen.
			</p>');
		} else {
			$this->titel = 'Bewerk het profiel van ' . $profiel->getNaam('volledig');
			$fields[] = new HtmlComment('<p>
				Hieronder kunt u uw eigen gegevens wijzigen. Voor enkele velden is het niet mogelijk zelf
				wijzigingen door te voeren. Voor de meeste velden geldt daarnaast dat de ingevulde gegevens
				een geldig formaat moeten hebben. Mochten er fouten in het gedeelte van uw profiel staan,
				dat u niet zelf kunt wijzigen, meld het dan bij de <a href="mailto:vice-abactis@csrdelft.nl">Vice-Abactis</a>.
			</p>');
		}
		$fields[] = new HtmlComment('<p>
			Als er <span class="waarschuwing">tekst in rode letters</span> wordt afgebeeld bij een veld, dan
			betekent dat dat de invoer niet geaccepteerd is, en dat u die zult moeten moeten aanpassen aan het
			gevraagde formaat. Een aantal velden kan leeg gelaten worden als er geen zinvolle informatie voor is.
		</p>');

		if ($isMod) {
			$statussen = array();
			foreach (LidStatus::getTypeOptions() as $optie) {
				$statussen[$optie] = LidStatus::getDescription($optie);
			}
			$fields[] = new SelectField('status', $profiel->status, 'Lidstatus', $statussen);
			$fields[] = new HtmlComment('<p>Bij het wijzigen van de lidstatus worden overbodige <span class="waarschuwing">gegevens verwijderd</span>, onomkeerbaar, opletten dus!</p>');

			require_once 'lid/lidzoeker.class.php';
			$gelijknamigenovieten = LidZoeker::zoekLeden($profiel->voornaam, 'voornaam', 'alle', 'achternaam', array(LidStatus::Noviet), array('uid'));
			$gelijknamigeleden = LidZoeker::zoekLeden($profiel->achternaam, 'achternaam', 'alle', 'lidjaar', array(LidStatus::Lid, LidStatus::Gastlid), array('uid'));

			$html = '<div class="novieten">';
			if (count($gelijknamigenovieten) > 1 OR ( $profiel->status !== LidStatus::Noviet AND ! empty($gelijknamigenovieten))) {
				$html .= 'Gelijknamige novieten:<ul class="nobullets">';
				foreach ($gelijknamigenovieten as $noviet) {
					$html .= '<li>' . ProfielModel::getLink($noviet['uid'], 'volledig') . '</li>';
				}
				$html .= '</ul>';
			} else {
				$html .= 'Geen novieten met overeenkomstige namen.';
			}
			$html .= '</div><div class="leden">';
			if (count($gelijknamigeleden) > 1 OR ( !($profiel->status == LidStatus::Lid OR $profiel->status == LidStatus::Gastlid) AND ! empty($gelijknamigeleden))) {
				$html .= 'Gelijknamige (gast)leden:<ul class="nobullets">';
				foreach ($gelijknamigeleden as $lid) {
					$html .= '<li>' . ProfielModel::getLink($lid['uid'], 'volledig') . '</li>';
				}
				$html .= '</ul>';
			} else {
				$html .= 'Geen (gast)leden met overeenkomstige namen.';
			}
			$html .= '</div>';

			$fields[] = new HtmlComment($html);
		}

		if ($isMod OR $editNoviet OR $profiel->isOudlid()) {
			$fields[] = new Subkopje('Identiteit');
			$fields[] = new RequiredTextField('voornaam', $profiel->voornaam, 'Voornaam', 50);
			$fields[] = new RequiredTextField('voorletters', $profiel->voorletters, 'Voorletters', 10);
			$fields[] = new TextField('tussenvoegsel', $profiel->tussenvoegsel, 'Tussenvoegsel', 15);
			$fields[] = new RequiredTextField('achternaam', $profiel->achternaam, 'Achternaam', 50);
			if ($isMod OR $editNoviet) {
				$fields[] = new GeslachtField('geslacht', $profiel->geslacht, 'Geslacht');
				$fields[] = new TextField('voornamen', $profiel->voornamen, 'Voornamen', 100);
				if (!$editNoviet) {
					$fields[] = new TextField('postfix', $profiel->postfix, 'Postfix', 7);
					$fields[] = new NickField('nickname', $profiel->nickname, 'Bijnaam', $profiel);
				}
			}
			$fields[] = new DatumField('gebdatum', $profiel->gebdatum, 'Geboortedatum', date('Y') - 15);
			if ($isMod AND ! $profiel->isLid() AND ! $profiel->isOudlid()) {
				$fields[] = new DatumField('sterfdatum', $profiel->sterfdatum, 'Overleden op');
			}
			if ($isMod OR $profiel->isOudlid() OR $profiel->status === LidStatus::Overleden) {
				$fields[] = new LidField('echtgenoot', $profiel->echtgenoot, 'Echtgenoot', 'allepersonen');
				$fields[] = new Subkopje('Oudledenpost');
				$fields[] = new TextField('adresseringechtpaar', $profiel->adresseringechtpaar, 'Tenaamstelling post echtpaar', 250);
				$fields[] = new SelectField('ontvangtcontactueel', $profiel->ontvangtcontactueel, 'Ontvangt Contactueel?', array('ja' => 'ja', 'digitaal' => 'ja, digitaal', 'nee' => 'nee'));
			}
		}

		$fields[] = new Subkopje('Adres');
		$fields[] = new RequiredTextField('adres', $profiel->adres, 'Straatnaam + Huisnummer', 100);
		$fields[] = new RequiredTextField('postcode', $profiel->postcode, 'Postcode', 20);
		$fields[] = new RequiredTextField('woonplaats', $profiel->woonplaats, 'Woonplaats', 50);
		$fields[] = new RequiredLandField('land', $profiel->land, 'Land');
		$fields[] = new TelefoonField('telefoon', $profiel->telefoon, 'Telefoonnummer (vast)', 20);
		$fields[] = new TelefoonField('mobiel', $profiel->mobiel, 'Paupernummer', 20);

		if (!$profiel->isOudlid()) {
			$fields[] = new Subkopje('Adres ouders');
			$fields[] = new TextField('o_adres', $profiel->o_adres, 'Straatnaam', 100);
			$fields[] = new TextField('o_postcode', $profiel->o_postcode, 'Postcode', 20);
			$fields[] = new TextField('o_woonplaats', $profiel->o_woonplaats, 'Woonplaats', 50);
			$fields[] = new LandField('o_land', $profiel->o_land, 'Land', 50);
			$fields[] = new TelefoonField('o_telefoon', $profiel->o_telefoon, 'Telefoonnummer', 20);
		}

		$fields[] = new Subkopje('Contact');
		//TODO: email & multiple contacts
		//$fields[] = new RequiredEmailField('email', $profiel->getPrimaryEmail(), 'Emailadres');
		$fields[] = new EmailField('msn', $profiel->msn, 'MSN');
		$fields[] = new TextField('icq', $profiel->icq, 'ICQ', 10);
		$fields[] = new EmailField('jid', $profiel->jid, 'Jabber/Google-talk');
		$fields[] = new TextField('skype', $profiel->skype, 'Skype', 20);
		$fields[] = new UrlField('linkedin', $profiel->linkedin, 'Publiek LinkedIn-profiel');
		$fields[] = new UrlField('website', $profiel->website, 'Website');

		$fields[] = new Subkopje('Boekhouding');
		$fields[] = new TextField('bankrekening', $profiel->bankrekening, 'Bankrekening', 18);
		if ($isMod) {
			$fields[] = new VinkField('machtiging', $profiel->machtiging, 'Machtiging getekend?');
		}
		if (LoginModel::mag('P_ADMIN')) {
			$fields[] = new IntField('soccieID', (int) $profiel->soccieID, 'SoccieID (uniek icm. bar)', 0, 10000);
			$fields[] = new SelectField('createTerm', $profiel->createTerm, 'Aangemaakt bij', array('barvoor' => 'barvoor', 'barmidden' => 'barmidden', 'barachter' => 'barachter', 'soccie' => 'soccie'));
		}

		$fields[] = new Subkopje('Studie');
		$fields[] = new StudieField('studie', $profiel->studie, 'Studie');
		$fields['studiejaar'] = new IntField('studiejaar', (int) $profiel->studiejaar, 'Beginjaar studie', 1950, date('Y'));
		$fields['studiejaar']->leden_mod = $isMod;

		if (!$profiel->isOudlid()) {
			$fields[] = new TextField('studienr', $profiel->studienr, 'Studienummer (TU)', 20);
		}

		if (!$editNoviet AND ( $isMod OR $profiel->isOudlid() )) {
			$fields[] = new TextField('beroep', $profiel->beroep, 'Beroep/werk', 4096);
			$fields[] = new IntField('lidjaar', (int) $profiel->lidjaar, 'Lid sinds', 1950, date('Y'));
		}

		if ($isMod) {
			$fields[] = new DatumField('lidafdatum', $profiel->lidafdatum, 'Lid-af sinds');
		}

		if ($isMod AND ! $editNoviet) {
			$fields[] = new VerticaleField('verticale', $profiel->verticale, 'Verticale');
			$fields[] = new SelectField('kring', $profiel->kring, 'Kring', range(0, 9));
			if ($profiel->isLid()) {
				$kringleider = array();
				foreach (Kringleider::getTypeOptions() as $optie) {
					$kringleider[$optie] = Kringleider::getDescription($optie);
				}
				$fields[] = new SelectField('kringleider', $profiel->kringleider, 'Kringleider', $kringleider);
				$fields[] = new VerticaleField('kringcoach', $profiel->kringcoach, 'Kringcoach');
				$fields[] = new VinkField('verticaleleider', $profiel->verticaleleider, 'Verticaan');
			}
			$fields[] = new LidField('patroon', $profiel->patroon, 'Patroon', 'allepersonen');
		}

		if (!$editNoviet) {
			$fields[] = new Subkopje('Duckstad');
			$fields[] = new DuckField('duckname', $profiel->duckname, 'Duckstad-naam', $profiel);
			$duckfoto = new Afbeelding(PICS_PATH . $profiel->getPasfotoPath(false, 'Duckstad'));
			if (!$duckfoto->exists() OR strpos($duckfoto->directory, '/Duckstad/') === false) {
				$duckfoto = null;
			}
			$fields[] = new ImageField('duckfoto', 'Duck-pasfoto', $duckfoto, null, null, false, 100, 100, 250, 250);
		}

		$fields[] = new Subkopje('Persoonlijk');
		$fields[] = new TextField('eetwens', $profiel->eetwens, 'Dieet/allergie');
		$fields[] = new IntField('lengte', (int) $profiel->lengte, 'Lengte (cm)', 50, 250);
		$fields[] = new SelectField('ovkaart', $profiel->ovkaart, 'OV-kaart', array('' => 'Kies...', 'geen' => '(Nog) geen OV-kaart', 'week' => 'Week', 'weekend' => 'Weekend', 'niet' => 'Niet geactiveerd'));
		$fields[] = new TextField('kerk', $profiel->kerk, 'Kerk', 50);
		$fields[] = new TextField('muziek', $profiel->muziek, 'Muziekinstrument', 50);
		$fields[] = new SelectField('zingen', $profiel->zingen, 'Zingen', array('' => 'Kies...', 'ja' => 'Ja, ik zing in een band/koor', 'nee' => 'Nee, ik houd niet van zingen', 'soms' => 'Alleen onder de douche', 'anders' => 'Anders'));

		if ($isMod OR $editNoviet) {
			$fields[] = new TextField('vrienden', $profiel->vrienden, 'Vrienden binnnen C.S.R.', 300);
			$fields[] = new TextField('middelbareSchool', $profiel->middelbareSchool, 'Middelbare school', 200);
		}

		if (LoginModel::mag('P_ADMIN,groep:bestuur,groep:novcie')) {
			$fields[] = new TextareaField('novitiaat', $profiel->novitiaat, 'Wat verwacht je van het novitiaat?');
			$fields[] = new Subkopje('<b>Einde vragenlijst</b><br /><br /><br /><br /><br />');
			$fields[] = new CollapsableSubkopje('novcieForm', 'In te vullen door NovCie', true);
			$fields[] = new SelectField('novietSoort', $profiel->novietSoort, 'Soort Noviet', array('noviet', 'nanoviet'));
			$fields[] = new SelectField('matrixPlek', $profiel->matrixPlek, 'Matrix plek', array('voor', 'midden', 'achter'));
			$fields[] = new SelectField('startkamp', $profiel->startkamp, 'Startkamp', array('ja', 'nee'));
			$fields[] = new TextareaField('medisch', $profiel->medisch, 'medisch (NB alleen als relevant voor hele NovCie)');
			$fields[] = new TextareaField('novitiaatBijz', $profiel->novitiaatBijz, 'Bijzonderheden novitiaat (op dag x ...)');
			$fields[] = new TextareaField('kgb', $profiel->kgb, 'Overige NovCie-opmerking');
			$fields[] = new HtmlComment('</div>');
		}
		$fields[] = new FormDefaultKnoppen('/profiel/' . $profiel->uid);

		$this->addFields($fields);
	}

	/**
	 * Maak een stukje bbcode aan met daarin de huidige wijziging, door wie en wanneer.
	 */
	public function changelog($diff) {
		$changelog = '[div]Bewerking van [lid=' . LoginModel::getUid() . '] op [reldate]' . getDatetime() . '[/reldate][br]';
		foreach ($diff as $change) {
			$changelog .= '(' . $change->property . ') ' . $change->old_value . ' => ' . $change->new_value . '[br]';
		}
		return $changelog . '[/div][hr]';
	}

}