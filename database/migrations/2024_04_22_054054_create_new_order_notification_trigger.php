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
        DECLARE latest_user_id INT;
        SELECT user_id INTO latest_user_id FROM orders ORDER BY created_at DESC LIMIT 1;

        INSERT INTO notifications (order_id, user_id, created_at, updated_at, read_at)
        VALUES (NEW.id, latest_user_id, NOW(), NOW(), NULL);
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
