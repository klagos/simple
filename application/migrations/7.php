<?php
class Migration_7 extends Doctrine_Migration_Base {
    public function up(){
//        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','gestion','desarrollo','configuracion')");
        $this->changeColumn('usuario_backend', 'rol',"VARCHAR(150)");
    }
    public function down(){
//        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','gestion','desarrollo')");
        $this->changeColumn('usuario_backend', 'rol',"VARCHAR(150)");
    }
}