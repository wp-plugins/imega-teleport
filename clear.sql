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
delete from {$table_prefix}term_taxonomy where term_taxonomy_id in(select object_id from {$table_prefix}imegateleport where name='term_taxonomy');delete from {$table_prefix}term_relationships where object_id in(select object_id from {$table_prefix}imegateleport where name='posts');delete from {$table_prefix}postmeta where post_id in(select p.id from {$table_prefix}imegateleport tp left join {$table_prefix}posts p on p.post_parent=tp.object_id where name='posts'and p.post_parent is not null);delete from {$table_prefix}postmeta where post_id in(select object_id from {$table_prefix}imegateleport where name='posts');delete from {$table_prefix}posts where post_parent in(select object_id from {$table_prefix}imegateleport where name='posts');delete from {$table_prefix}posts where id in(select object_id from {$table_prefix}imegateleport where name='posts');delete from {$table_prefix}terms where term_id in(select object_id from {$table_prefix}imegateleport where name='terms');truncate {$table_prefix}imegateleport;