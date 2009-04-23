<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2008 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class organize_task_Core {
  static function available_tasks() {
    // Return empty array so nothing appears in the maintenance screen
    return array();
  }

  static function rearrange($task) {
    $context = unserialize($task->context);

    try {
      $total = count($context["items"]);
      $stop = min($total - $context["position"], $context["batch"]);
      for ($offset = 0; $offset < $stop; $offset++) {
        $current_id = $context["position"] + $offset;
        $id = $context["items"][$current_id];
        Database::instance()
          ->query("Update {items} set weight = {$context["position"]} where id=$id;");
      }
      $context["position"] += $stop;
      $task->state = "success";
    } catch(Exception $e) {
      $task->status = $e->getMessage();
      $task->state = "error";
      $task->save();
      throw $e;
    }
    $task->context = serialize($context);
    $total = count($context["items"]);
    $task->percent_complete = $context["position"] / (float)$total * 100;
    $task->done = $context["position"] == $total || $task->state == "error";
  }

  static function move($task) {
    $context = unserialize($task->context);

    try {
      $target = ORM::factory("item", $context["target"]);
      $total = count($context["items"]);
      $stop = min($total - $context["position"], $context["batch"]);
      for ($offset = 0; $offset < $stop; $offset++) {
        $current_id = $context["position"] + $offset;
        $source = ORM::factory("item", $context["items"][$current_id]);
        core::move_item($source, $target);
      }
      $context["position"] += $stop;
      $task->state = "success";
    } catch(Exception $e) {
      $task->status = $e->getMessage();
      $task->state = "error";
      $task->save();
      throw $e;
    }
    $task->context = serialize($context);
    $total = count($context["items"]);
    $task->percent_complete = $context["position"] / (float)$total * 100;
    $task->done = $context["position"] == $total || $task->state == "error";
  }
}