<?php
class Migration_22 extends Doctrine_Migration_Base {
    public function up(){

        $definition = array(
        'local'        => 'cuenta_id',
        'foreign'      => 'id',
        'foreignTable' => 'cuenta',
        'onDelete'     => 'CASCADE',
        'onUpdate'     => 'CASCADE',
        );

        $this->createForeignKey( 'auditoria_operaciones', 'fk_cuenta', $definition );
    }

    public function down(){
        $this->dropForeignKey('auditoria_operaciones', 'fk_cuenta');
    }
}
