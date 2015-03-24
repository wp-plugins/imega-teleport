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
select p.id,p.post_date,i.order_item_name,im.meta_key,im.meta_value,tp.guid from {$table_prefix}posts p left join {$table_prefix}woocommerce_order_items i on i.order_id=p.id left join {$table_prefix}woocommerce_order_itemmeta im on im.order_item_id=i.order_item_id left join {$table_prefix}imegateleport tp on tp.object_id=im.meta_value and im.meta_key='_product_id'where p.post_type='shop_order'and im.meta_key='_qty'or im.meta_key='_product_id'or im.meta_key='_line_total';