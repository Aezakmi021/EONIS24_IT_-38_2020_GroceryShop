<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER new_order_notification AFTER INSERT ON orders
            FOR EACH ROW
            BEGIN
                DECLARE admin_id INT;
                SELECT id INTO admin_id FROM users WHERE isAdmin = 1 LIMIT 1;

                INSERT INTO notifications (order_id, user_id, created_at, updated_at)
                VALUES (NEW.id, admin_id, NOW(), NOW());
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS new_order_notification');
    }
};
