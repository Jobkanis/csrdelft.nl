<?php
class MededelingenContent extends SimpleHTML{
	private $selectedMededeling;
	private $paginaNummer;
	private $paginaNummerOpgevraagd;
	private $topMost;
	
	const aantalTopMostBlock=3;
	const aantalPerPagina=2;
	
	public function __construct($mededelingId){
		$this->selectedMededeling=null;
		$this->paginaNummer=1;
		$this->paginaNummerOpgevraagd=false;
		if($mededelingId!=0)
		{
			try{
				$this->selectedMededeling=new Mededeling($mededelingId);
			} catch (Exception $e) {
				// Do nothing, keeping $selectedMededeling equal to null.
			}
		}
		if($this->selectedMededeling===null)
		{
			$topmost=Mededeling::getTopmost(self::aantalTopMostBlock);
			// If there is at least one topmost, make it the selected one.
			// Otherwise, keep $this->selectedMededeling equal to null.
			if(isset($topmost[0]))
				$this->selectedMededeling=$topmost[0];
			$this->topMost=$topmost;
		}
	}
	
	public function setPaginaNummer($pagina){
		if(is_numeric($pagina) AND $pagina>=1)
		{
			$this->paginaNummerOpgevraagd=true;
			$this->paginaNummer=$pagina;
		}
	}

	public function view(){
		if(!$this->paginaNummerOpgevraagd)
			$this->paginaNummer = $this->selectedMededeling->getPaginaNummer();

		$content=new Smarty_csr();
		define( 'NIEUWS_ROOT', '/actueel/mededelingen/');

		$content->assign('melding', $this->getMelding());
		$content->assign('nieuws_root', NIEUWS_ROOT);
		$content->assign('csr_pics', CSR_PICS);
		
		$content->assign('topmost', $this->topMost);
		$content->assign('lijst', Mededeling::getLijstVanPagina($this->paginaNummer, self::aantalPerPagina));
		// The following attribute can't be null. Otherwise, the page will
		// not display a full Mededeling.
		$content->assign('selectedMededeling', $this->selectedMededeling);
		$content->assign('ubb', CsrUBB::instance());
		
		$content->assign('huidigePagina', $this->paginaNummer);
		$content->assign('totaalAantalPaginas', (ceil(Mededeling::getAantal()/self::aantalPerPagina)));

		$content->display('mededelingen/mededelingen.tpl');
	}
}
?>