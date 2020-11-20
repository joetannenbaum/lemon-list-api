<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingListItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopping_list_version_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('order');
            $table->unsignedInteger('quantity');
            $table->boolean('checked_off')->default(false);
            $table->timestamps();

            $table->unique(['shopping_list_version_id', 'item_id'], 'version_id_item_id_uniq');

            $table->foreign('shopping_list_version_id')->references('id')->on('shopping_list_versions')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopping_list_items');
    }
}
