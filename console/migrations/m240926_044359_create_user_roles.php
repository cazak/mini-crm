<?php

use yii\db\Migration;

class m240926_044359_create_user_roles extends Migration
{
    public function safeUp(): void
    {
        $this->batchInsert('{{%auth_item}}', ['type', 'name', 'description'], [
            [1, 'manager', 'Manager'],
            [1, 'admin', 'Admin'],
        ]);

        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
            ['admin', 'manager'],
        ]);

        $this->execute('INSERT INTO {{%auth_assignment}} (item_name, user_id) SELECT \'manager\', u.id FROM {{%user}} u ORDER BY u.id');
    }

    public function down(): void
    {
        $this->delete('{{%auth_item}}', ['name' => ['manager', 'admin']]);
    }
}
