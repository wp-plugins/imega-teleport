/**
 * @package iMegaTeleport
 * @version 1.6.8
 * 
 * Copyright 2013 iMega ltd (email: info@imega.ru)
 *
 * This program is free software you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
delete from {$table_prefix}woocommerce_attribute_taxonomies where attribute_id in(select object_id from {$table_prefix}imegateleport where name='woocommerce_attribute_taxonomies');