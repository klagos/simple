<?php
class Migration_34 extends Doctrine_Migration_Base {
    public function up(){
        
        $this->changeColumn('dato_seguimiento','valor',"MEDIUMTEXT",null,array('notnull'=>true));
    }
    
    public function down(){
        $this->changeColumn('dato_seguimiento', 'valor',"TEXT",null,array('notnull'=>false));
    }
}
?>