<?php
/*
 * class Consumable  extends Gw2Api 
 */
class Consumable extends Gw2Api  {

	public function __construct( $APIItem ) {
		parent::__construct($APIItem);	

        $this->sub_type        = empty( $APIItem['details']['type'] )        ? null : $APIItem['details']['type'];
        $this->duration_ms     = empty( $APIItem['details']['duration_ms'] ) ? null : $APIItem['details']['duration_ms'];
        $this->sub_description = empty( $APIItem['details']['description'] ) ? null : $APIItem['details']['description'];
	}

	public function getTooltip() {
		return <<<HTML
        <div>
            <img src="{$this->img_file}" class="item-icon-tooltip">
            <span class="{$this->rarity} item_title">{$this->getName()}</span>
        </div>
		{$this->getNourishment()}	
		{$this->getDescriptionDisplay()}
		<div>{$this->type} - {$this->sub_type}</div>
        {$this->getLevelDisplay()}
        {$this->getSoulboundStatus()}
        {$this->getVendorValueDisplay()}
HTML;
	}

	public function getSubTypeInsert( ) {
		global $aConsumable_types;
		return (int) array_search( $this->sub_type, $aConsumable_types );
	}
	
	public function getNourishment(){			
		if ( !empty( $this->duration_ms ) )
		{
            $input = $this->duration_ms;
			$uSec = $input % 1000;
            $input = floor($input / 1000);
            $seconds = $input % 60;
            $input = floor($input / 60);
            $minutes = $input % 60;
            $input = floor($input / 60);
            $hours = $input % 60;
            $input = floor($input / 60);
            $time = array();
            if ($hours > 0)
                $time[] = "$hours h";
            
            if ($minutes > 0)
                $time[] = "$minutes m";
            
            if ($seconds > 0)
                $time[] = "$seconds s";
            
            $time_string = implode(',', $time);
			
			// if genetic display the name
			$displayName = $this->sub_type == 'Generic' ? $this->getName() : 'Nourishment';

			// if food or utility display Nourishment
			return "<div>Double-click to consume</div>
					<div class='Junk'>{$displayName}({$time_string}): ". nl2br( $this->sub_description )."</div>";

		}
		// if the sub description is there return that
		if( !empty( $this->sub_description ) )
			return $this->sub_description;
		return;
	}
}
?>