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
class core_menu_Core {
  public static function site_navigation($menu, $theme) {
    $menu->append(
      Menu::factory("link")
      ->id("home")
      ->label(_("Home"))
      ->url(url::base()));

    $menu->append(
      Menu::factory("link")
      ->id("browse")
      ->label(_("Browse"))
      ->url(url::site("albums/1")));

    $item = $theme->item();
    $user = Session::instance()->get("user", null);
    if ($user) {
      // @todo need to do a permission check here
      $menu->append(
        Menu::factory("submenu")
        ->id("upload_menu")
        ->label(_("Upload"))
        ->append(
          Menu::factory("dialog")
          ->id("add_photos")
          ->label(_("Add Photos"))
          ->url(url::site("form/add/photos/$item->id"))));

      $admin_menu = Menu::factory("submenu")
        ->id("admin_menu")
        ->label(_("Admin"));
      $menu->append($admin_menu);

      // @todo need to do a permission check here
      $admin_menu->append(
        Menu::factory("dialog")
        ->id("edit")
        ->label(_("Edit"))
        ->url(url::site("form/edit/{$item->type}s/$item->id")));

      if ($user->admin) {
        $admin_menu->append(
          Menu::factory("link")
          ->id("site_admin")
          ->label(_("Site Admin"))
          ->url(url::site("admin")));
      }
    }
  }
}