<?php

class Migration_15 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('proceso', 'proc_cont', 'int(10)', null, array('notnull'=>1));
    }


    public function down() {
        $this->removeColumn('proceso', 'proc_cont');
    }

}
?>