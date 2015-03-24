/**
 * @package iMegaTeleport
 * @version 1.6.14
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
update {$table_prefix}term_relationships set term_taxonomy_id=(select tt.term_taxonomy_id from {$table_prefix}term_taxonomy tt left join {$table_prefix}terms t on t.term_id=tt.term_id where t.slug='{$status}')where object_id={$id};