<?php

class Migration_16 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('tramite', 'tramite_proc_cont', 'int(10)', null, array('notnull'=>1));
    }


    public function down() {
        $this->removeColumn('tramite', 'tramite_proc_cont');
    }

}
?>