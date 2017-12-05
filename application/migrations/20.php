<?php
class Migration_20 extends Doctrine_Migration_Base {
    public function up(){
        $this->changeColumn('usuario_backend', 'rol',"VARCHAR(150)");
    }
    public function down(){
        $this->changeColumn('usuario_backend', 'rol',"VARCHAR(150)");
    }
}