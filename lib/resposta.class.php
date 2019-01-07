<?php

class Resposta
{
	public $dades    = null;     //Dades generades per l'acció.
	public $correcta    = false;    //Operació correcta?
	public $missatge    = '';
	
	public function SetCorrecta($correcta, $m = '')
	{
		$this->correcta = $correcta;
		$this->missatge = $m;

		if(!$correcta && $m = '') {
            $this->missatge = 'Hi ha hagut un error inesperat';
        }    
    }
    
    public function SetDades($dades)
    {
        $this->dades=$dades;
	}
	
	public function SetRegistres($registres)
    {
        $this->registres=$registres;
    }
}