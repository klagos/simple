<?php
class Migration_30 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'campo', 'agenda_campo', 'int' ,11);
        
    }
    
    public function down(){
        $this->removeColumn( 'campo', 'agenda_campo' );
    }
}
?>
